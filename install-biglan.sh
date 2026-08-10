#!/usr/bin/env bash
#
# install-biglan.sh
#
# Automated installer script for the BigLan Network Monitoring System
# server, based on the official Installation Guide.
#
# Target system: fresh Ubuntu Server 24.04 LTS.
# Usage:         sudo bash install-biglan.sh
#
# The script prints its progress at every step and stops at the first
# error, so it never keeps running from a broken state. The full output
# is also written to a log file (see LOGFILE) for later inspection.

set -Eeuo pipefail

# ------------------------------------------------------------------
# Settings / defaults
# ------------------------------------------------------------------
PROJECT_DIR_DEFAULT="/var/www/biglan"
REPO_URL="https://github.com/atlantisguru/biglan-server.git"
LOGFILE="/var/log/biglan-install.log"
TOTAL_STEPS=20
CURRENT_STEP=0

# Colors (only if the terminal supports them)
if [ -t 1 ]; then
    C_RESET="\033[0m"; C_BOLD="\033[1m"
    C_GREEN="\033[32m"; C_RED="\033[31m"; C_YELLOW="\033[33m"; C_BLUE="\033[34m"
else
    C_RESET=""; C_BOLD=""; C_GREEN=""; C_RED=""; C_YELLOW=""; C_BLUE=""
fi

# ------------------------------------------------------------------
# Helper functions
# ------------------------------------------------------------------
step() {
    CURRENT_STEP=$((CURRENT_STEP + 1))
    echo ""
    echo -e "${C_BLUE}${C_BOLD}[${CURRENT_STEP}/${TOTAL_STEPS}]${C_RESET} ${C_BOLD}$1${C_RESET}"
}

info()    { echo -e "  ${C_YELLOW}->${C_RESET} $1"; }
success() { echo -e "  ${C_GREEN}${C_BOLD}OK${C_RESET}   $1"; }
fail()    { echo -e "  ${C_RED}${C_BOLD}FAIL${C_RESET} $1"; }

on_error() {
    local exit_code=$?
    local line_no=$1
    echo ""
    fail "Installation stopped at step ${CURRENT_STEP} (script line: ${line_no}, exit code: ${exit_code})."
    echo -e "  ${C_RED}Full log available at: ${LOGFILE}${C_RESET}"
    echo -e "  ${C_RED}Check the end of the log for the exact error message:${C_RESET} tail -n 40 ${LOGFILE}"
    exit "${exit_code}"
}
trap 'on_error $LINENO' ERR

run() {
    # Logs every command to the logfile; failures are caught by the trap.
    echo "+ $*" >> "${LOGFILE}"
    eval "$@" >> "${LOGFILE}" 2>&1
}

ask() {
    # ask "Question text" "default value"
    local prompt="$1" default="${2:-}" answer
    if [ -n "$default" ]; then
        read -r -p "  ${prompt} [${default}]: " answer
        echo "${answer:-$default}"
    else
        read -r -p "  ${prompt}: " answer
        echo "${answer}"
    fi
}

ask_secret() {
    local prompt="$1" answer
    read -r -s -p "  ${prompt}: " answer
    echo "" >&2
    echo "${answer}"
}

require_root() {
    if [ "$(id -u)" -ne 0 ]; then
        fail "This script must be run as root: sudo bash $0"
        exit 1
    fi
}

# ------------------------------------------------------------------
# Prerequisite: root privileges
# ------------------------------------------------------------------
require_root
touch "${LOGFILE}"
echo -e "${C_BOLD}BigLan server - automated installer${C_RESET}"
echo "Full output is logged to: ${LOGFILE}"
echo ""
echo -e "${C_YELLOW}NOTE: this script assumes it is running on a fresh Ubuntu Server"
echo -e "24.04 LTS with apt package access. It does not install the OS itself.${C_RESET}"
echo ""

# ------------------------------------------------------------------
# Ask for input up front, so the rest of the install runs unattended
# ------------------------------------------------------------------
echo -e "${C_BOLD}A few things need to be answered first:${C_RESET}"

PROJECT_DIR=$(ask "Installation target folder" "${PROJECT_DIR_DEFAULT}")

DB_NAME=$(ask "Database name" "biglan")
DB_USER=$(ask "Database username" "biglan_admin")
DB_PASS=$(ask_secret "Database password (hidden while typing)")
if [ -z "${DB_PASS}" ]; then
    fail "The database password cannot be empty."
    exit 1
fi

APP_TIMEZONE=$(ask "Timezone (PHP/Laravel format, e.g. Europe/Budapest)" "Europe/Budapest")
APP_URL=$(ask "Server address (IP or domain, without https://)" "$(hostname -I | awk '{print $1}')")

LOCALE_CHOICE=$(ask "Default language (hu/en)" "en")
if [ "${LOCALE_CHOICE}" != "hu" ]; then LOCALE_CHOICE="en"; fi

ENABLE_SSL=$(ask "Also enable Apache self-signed SSL? (y/n)" "y")

MASTER_KEY=$(openssl rand -base64 32 | tr -dc 'A-Za-z0-9' | head -c 32)
info "MASTER_KEY generated automatically (32 characters)."

echo ""
echo -e "${C_BOLD}Starting installation - this may take a few minutes.${C_RESET}"

# ------------------------------------------------------------------
# 1. Apache 2
# ------------------------------------------------------------------
step "Installing Apache 2"
run "apt-get update -y"
run "apt-get install -y apache2"
success "Apache installed."

# ------------------------------------------------------------------
# 2. MySQL
# ------------------------------------------------------------------
step "Installing MySQL and applying basic hardening"
run "apt-get install -y mysql-server"
success "MySQL installed."

info "Applying basic hardening (automated equivalent of mysql_secure_installation)..."
# Remove anonymous users and the test database, restrict remote root login.
if mysql -u root -e "SELECT 1" >/dev/null 2>&1; then
    MYSQL_ROOT_CMD="mysql -u root"
else
    MYSQL_ROOT_CMD="mysql"
fi
${MYSQL_ROOT_CMD} <<-'SQL' >> "${LOGFILE}" 2>&1 || true
DELETE FROM mysql.user WHERE User='';
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost','127.0.0.1','::1');
DROP DATABASE IF EXISTS test;
DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';
FLUSH PRIVILEGES;
SQL
success "MySQL basic hardening done."

# ------------------------------------------------------------------
# 3. Create database and user
# ------------------------------------------------------------------
step "Creating database and user (${DB_NAME} / ${DB_USER})"
${MYSQL_ROOT_CMD} <<-SQL >> "${LOGFILE}" 2>&1
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
SQL
success "Database and user created."

# ------------------------------------------------------------------
# 4. SNMP
# ------------------------------------------------------------------
step "Installing SNMP"
run "apt-get install -y snmp snmpd"
success "SNMP installed."

# ------------------------------------------------------------------
# 5. PHP and required extensions
# ------------------------------------------------------------------
step "Installing and configuring PHP"
# The package list from the official guide has been extended with
# php-mbstring and php-bcmath: without these, the Laravel "composer
# install" step below fails.
run "apt-get install -y php libapache2-mod-php php-mysql php-snmp php-xml php-zip php-curl php-mbstring php-bcmath"
success "PHP and required extensions installed."

# ------------------------------------------------------------------
# 6. Firewall
# ------------------------------------------------------------------
step "Configuring firewall"
if command -v ufw >/dev/null 2>&1; then
    # Port 8080 is required by the guide (client <-> server command channel).
    # 80/443 are also opened, otherwise the web UI itself would be
    # unreachable once ufw is active.
    run "ufw allow 8080/tcp"
    run "ufw allow 80/tcp"
    run "ufw allow 443/tcp"
    success "Firewall rules added (8080, 80, 443)."
else
    info "ufw is not installed, skipping this step."
fi

# ------------------------------------------------------------------
# 7. Git
# ------------------------------------------------------------------
step "Installing Git (if needed)"
if command -v git >/dev/null 2>&1; then
    success "Git is already installed."
else
    run "apt-get install -y git"
    success "Git installed."
fi

# ------------------------------------------------------------------
# 8. Composer
# ------------------------------------------------------------------
step "Installing Composer"
if command -v composer >/dev/null 2>&1; then
    success "Composer is already installed."
else
    run "apt-get install -y composer"
    success "Composer installed."
fi

# ------------------------------------------------------------------
# 9. Create target folder
# ------------------------------------------------------------------
step "Creating target folder (${PROJECT_DIR})"
run "mkdir -p ${PROJECT_DIR}"
run "chown -R \${SUDO_USER:-\$USER}:www-data ${PROJECT_DIR}"
success "Folder created, ownership set."

# ------------------------------------------------------------------
# 10. Download project from GitHub
# ------------------------------------------------------------------
step "Downloading project from GitHub"
if [ -d "${PROJECT_DIR}/.git" ]; then
    info "A git repo already exists here, running git pull instead of cloning."
    run "cd ${PROJECT_DIR} && git pull"
else
    run "git clone ${REPO_URL} ${PROJECT_DIR}"
fi
success "Project downloaded to: ${PROJECT_DIR}"

# ------------------------------------------------------------------
# 11. Install Composer dependencies
# ------------------------------------------------------------------
# NOTE: this step is missing from the official guide, but without it the
# Laravel app cannot run at all (no vendor/ folder, artisan won't work).
step "Installing PHP dependencies (composer install)"
run "cd ${PROJECT_DIR} && composer install --no-interaction --optimize-autoloader"
success "Composer dependencies installed."

# Set storage/ and bootstrap/cache/ ownership to www-data as early as
# possible, before any artisan command runs. This guarantees that no
# matter which later step fails, no root-owned log/cache file can ever
# be created in these folders - the classic cause of "Permission denied"
# errors on storage/logs/laravel.log once the web server (www-data)
# tries to write to it.
run "chown -R www-data:www-data ${PROJECT_DIR}/storage ${PROJECT_DIR}/bootstrap/cache"
run "chmod -R 775 ${PROJECT_DIR}/storage ${PROJECT_DIR}/bootstrap/cache"

# ------------------------------------------------------------------
# 12. Apache virtual host (public folder)
# ------------------------------------------------------------------
step "Configuring Apache virtual host (port 80)"
cat > /etc/apache2/sites-available/000-default.conf <<EOF
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot ${PROJECT_DIR}/public
    <Directory ${PROJECT_DIR}/public>
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF
run "a2enmod rewrite"
success "Virtual host configured, mod_rewrite enabled."

# ------------------------------------------------------------------
# 13. Enable SSL (optional, self-signed certificate)
# ------------------------------------------------------------------
step "Enabling SSL"
if [ "${ENABLE_SSL}" = "y" ] || [ "${ENABLE_SSL}" = "Y" ]; then
    run "a2enmod ssl"
    cat > /etc/apache2/sites-available/default-ssl.conf <<EOF
<IfModule mod_ssl.c>
<VirtualHost *:443>
    ServerAdmin webmaster@localhost
    DocumentRoot ${PROJECT_DIR}/public
    <Directory ${PROJECT_DIR}/public>
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
    SSLEngine on
    SSLCertificateFile      /etc/ssl/certs/ssl-cert-snakeoil.pem
    SSLCertificateKeyFile   /etc/ssl/private/ssl-cert-snakeoil.key
</VirtualHost>
</IfModule>
EOF
    run "a2ensite default-ssl"
    success "SSL enabled with a self-signed (snakeoil) certificate."
    info "This is only suitable for testing/internal networks - for production/external access, set up your own CA or a real certificate (see the guide's Introduction)."
else
    info "SSL skipped, as chosen."
fi

# ------------------------------------------------------------------
# 14. Restart Apache
# ------------------------------------------------------------------
step "Restarting Apache web server"
run "systemctl restart apache2"
success "Apache restarted."

# ------------------------------------------------------------------
# 15. Configure Laravel .env and generate APP key
# ------------------------------------------------------------------
# Note: in the official guide, "key:generate" (step 14) comes before
# creating the .env file (step 15) - that ordering is wrong, since
# key:generate can only write to an existing .env file. Here the
# correct order is used: .env first, then key:generate.
step "Configuring Laravel .env file and generating APP key"
cd "${PROJECT_DIR}"
# Always (re)generate .env from .env.example rather than only "if missing".
# This avoids a subtle bug: if the script (or artisan commands) had been
# run before and partially failed, an existing .env could already contain
# a key twice (once from .env.example, once appended by an earlier sed
# fallback) - phpdotenv honors the FIRST occurrence, so a stale/default
# value silently wins even after "fixing" the file. Starting fresh from
# .env.example every run guarantees there is exactly one line per key.
run "cp -f .env.example .env"

set_env() {
    local key="$1" value="$2"
    if grep -q "^${key}=" .env; then
        sed -i "s|^${key}=.*|${key}=${value}|" .env
    else
        echo "${key}=${value}" >> .env
    fi
}

set_env "APP_NAME" "BigLan"
set_env "APP_ENV" "production"
set_env "APP_TIMEZONE" "${APP_TIMEZONE}"
set_env "APP_URL" "https://${APP_URL}"
set_env "MASTER_KEY" "${MASTER_KEY}"
set_env "APP_LOCALE" "${LOCALE_CHOICE}"
set_env "APP_FALLBACK_LOCALE" "en"
set_env "APP_LANGUAGE" "${LOCALE_CHOICE}"
set_env "DB_CONNECTION" "mysql"
set_env "DB_HOST" "127.0.0.1"
set_env "DB_PORT" "3306"
set_env "DB_DATABASE" "${DB_NAME}"
set_env "DB_USERNAME" "${DB_USER}"
set_env "DB_PASSWORD" "${DB_PASS}"

# Defensive: remove any previously cached config from an earlier run, so
# there is no chance of artisan reading stale (e.g. default root/blank)
# database credentials from bootstrap/cache/config.php below.
run "rm -f ${PROJECT_DIR}/bootstrap/cache/config.php"

# Let www-data own the .env file too, then run key:generate as www-data
# (storage/ and bootstrap/cache/ are already www-data-owned since right
# after composer install) - this way even key:generate can never leave
# a root-owned file behind in storage/logs if something goes wrong.
run "chown www-data:www-data ${PROJECT_DIR}/.env"
run "sudo -u www-data php artisan key:generate --force"
success ".env configured, APP key generated."

# ------------------------------------------------------------------
# 16. Database migration
# ------------------------------------------------------------------
step "Running database migrations"
run "cd ${PROJECT_DIR} && sudo -u www-data php artisan config:clear"
run "cd ${PROJECT_DIR} && sudo -u www-data php artisan migrate --force"
success "Migrations completed."

# ------------------------------------------------------------------
# 17. Clear Laravel cache + rebuild config cache
# ------------------------------------------------------------------
step "Refreshing Laravel cache"
run "cd ${PROJECT_DIR} && sudo -u www-data php artisan cache:clear"
run "cd ${PROJECT_DIR} && sudo -u www-data php artisan config:cache"
success "Cache refreshed."

# ------------------------------------------------------------------
# 18. Seed default data
# ------------------------------------------------------------------
step "Seeding database with default data"
run "cd ${PROJECT_DIR} && sudo -u www-data php artisan db:seed --force"
success "Default data seeded."

# ------------------------------------------------------------------
# 19. Schedule the Laravel scheduler CronJob
# ------------------------------------------------------------------
# NOTE: the cron job runs under www-data's crontab, not root's. This is a
# deliberate fix: the official guide schedules it under root (sudo
# crontab -e), which means files created by the scheduler (e.g.
# storage/logs/laravel.log) end up owned by root. The web server itself
# runs as www-data, and once such a root-owned log file exists, Apache
# can no longer write to it -> "Permission denied" errors on the site.
# Running the scheduler as www-data keeps file ownership consistent with
# the web server user and avoids this entirely.
step "Scheduling CronJob (Laravel scheduler)"
CRON_LINE="* * * * * cd ${PROJECT_DIR} && php artisan schedule:run >> /dev/null 2>&1"
EXISTING_CRON="$(crontab -u www-data -l 2>/dev/null | grep -vF "${PROJECT_DIR} && php artisan schedule:run" || true)"
{ printf '%s\n' "${EXISTING_CRON}"; printf '%s\n' "${CRON_LINE}"; } | grep -v '^[[:space:]]*$' | crontab -u www-data -
success "CronJob configured under www-data (schedule:run every minute)."

# ------------------------------------------------------------------
# 20. Auto-register the server's local subnet in the IP Table
# ------------------------------------------------------------------
# Without at least one subnet registered under IP Table -> New Subnet,
# BigLan cannot determine whether any workstation is "on the LAN", and
# the Console (remote command) tab never appears for ANY workstation,
# no matter how it's actually connected. This detects the server's own
# directly-connected subnet and inserts it automatically, so Console
# access works immediately after install without a manual UI step.
step "Registering the local subnet in the IP Table (for Console visibility)"
SUBNET_CIDR="$(ip -o -4 route list scope link 2>/dev/null | awk '{print $1}' | head -n1)"
GATEWAY_IP="$(ip route show default 2>/dev/null | awk '{print $3}' | head -n1)"
if [ -n "${SUBNET_CIDR}" ]; then
    SUBNET_ID="${SUBNET_CIDR%%/*}"
    SUBNET_MASK="${SUBNET_CIDR##*/}"
    GATEWAY_IP="${GATEWAY_IP:-${SUBNET_ID}}"
    EXISTING_COUNT="$(${MYSQL_ROOT_CMD} -N -B "${DB_NAME}" -e "SELECT COUNT(*) FROM subnets WHERE identifier='${SUBNET_ID}' AND mask=${SUBNET_MASK};" 2>>"${LOGFILE}")"
    if [ "${EXISTING_COUNT}" = "0" ]; then
        ${MYSQL_ROOT_CMD} "${DB_NAME}" -e "INSERT INTO subnets (type, identifier, mask, gateway, alias, description, created_at, updated_at) VALUES (4, '${SUBNET_ID}', ${SUBNET_MASK}, '${GATEWAY_IP}', 'Auto-detected LAN', 'Automatically registered by install-biglan.sh', NOW(), NOW());" >> "${LOGFILE}" 2>&1
        success "Registered subnet ${SUBNET_CIDR} (gateway ${GATEWAY_IP}) in the IP Table."
    else
        success "Subnet ${SUBNET_CIDR} was already registered, nothing to do."
    fi
else
    info "Could not auto-detect the local subnet (no link-scope route found) - skipping."
    info "Add it manually later: IP Table -> New Subnet, in the BigLan web UI."
fi

# ------------------------------------------------------------------
# Final permissions pass
# ------------------------------------------------------------------
# The bulk of the project is owned by the human/deploy user (for
# convenient git/composer updates later), but storage/ and
# bootstrap/cache/ are explicitly owned by www-data - the same user the
# web server and the cron job run as - so both writing a session/cache
# file from a web request and writing a log line from the scheduler
# always succeed, regardless of which one created the file first.
run "chown -R \${SUDO_USER:-\$USER}:www-data ${PROJECT_DIR}"
run "chown -R www-data:www-data ${PROJECT_DIR}/storage ${PROJECT_DIR}/bootstrap/cache"
run "chmod -R 775 ${PROJECT_DIR}/storage ${PROJECT_DIR}/bootstrap/cache"

# ------------------------------------------------------------------
# Done
# ------------------------------------------------------------------
echo ""
echo -e "${C_GREEN}${C_BOLD}Installation completed successfully.${C_RESET}"
echo ""
echo -e "  Open in your browser: ${C_BOLD}https://${APP_URL}${C_RESET} (or http://, if you skipped SSL)"
echo -e "  Register a user - the first registered user is automatically"
echo -e "  confirmed and granted full administrator privileges."
echo ""
echo -e "  MASTER_KEY (keep this safe, it encrypts the client keys):"
echo -e "  ${C_BOLD}${MASTER_KEY}${C_RESET}"
echo ""
echo -e "  Database: ${DB_NAME} / user: ${DB_USER}"
echo -e "  Local subnet auto-registered in IP Table: ${SUBNET_CIDR:-none detected}"
echo -e "  Full installation log: ${LOGFILE}"
echo ""
echo -e "${C_YELLOW}If you enabled the self-signed SSL certificate, your browser will warn"
echo -e "about it - that is expected for a self-signed cert. For production/"
echo -e "external access, use your own CA or a real certificate instead.${C_RESET}"
