<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workstations', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 100)->nullable();
            $table->string('mboard_serial', 100)->nullable();
            $table->string('product_serial', 100)->nullable();
            $table->string('first_mac', 100)->nullable();
            $table->integer('score')->nullable();
        	$table->string('alias', 255)->nullable();
            $table->string('hostname', 255)->nullable();
            $table->string('workgroup', 255)->nullable();
            $table->string('domain', 255)->nullable();
            $table->string('os', 255)->nullable();
            $table->integer('os_activated')->nullable();
            $table->integer('os_drive_size')->nullable();
            $table->integer('os_drive_free_space')->nullable();
            $table->integer('ram')->nullable();
            $table->integer('ram_slots')->nullable();
            $table->integer('ram_max_capacity')->nullable();
            $table->string('cpu', 255)->nullable();
        	$table->date('cpu_release_date')->nullable();
            $table->integer('cpu_score')->nullable();
            $table->integer('architecture')->nullable();
            $table->string('hardware', 255)->nullable();
            $table->string('type', 100)->nullable();
            $table->string('serial', 255)->nullable();
            $table->string('active_mac', 255)->nullable();
            $table->string('inventory_id', 255)->nullable();
            $table->integer('fast_startup')->nullable();
            $table->dateTime('wu_checked')->nullable();
            $table->dateTime('wu_installed')->nullable();
            $table->dateTime('heartbeat')->nullable();
            $table->integer('idle')->nullable();
            $table->dateTime('bootup_at')->nullable();
            $table->dateTime('startup_at')->nullable();
            $table->integer('boot_time')->nullable();
            $table->integer('usb')->default(0);
            $table->integer('teamviewer')->default(0);
            $table->integer('anydesk')->default(0);
            $table->integer('rdp')->default(0);
            $table->integer('vnc')->default(0);
            $table->string('service_version', 255)->nullable();
            $table->string('update_channel', 100)->nullable();
        	$table->string('msg_token', 255)->nullable();
            $table->timestamps();
        });
    
    	Schema::create('ws_connection', function (Blueprint $table) {
            $table->id();
            $table->integer('wsid');
            $table->string('type', 200);
            $table->text('value')->nullable();
        	$table->string('notes', 255)->nullable();
            $table->timestamps();
        });
    
    	Schema::create('ws_control_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('wsid');
        	$table->integer('user_id');
        	$table->integer('outbound')->default(1);
        	$table->text('log')->nullable();
        	$table->timestamps();
        });
    
    	Schema::create('ws_dns', function (Blueprint $table) {
            $table->id();
            $table->integer('wsid');
        	$table->string('ip', 255);
        	$table->timestamps();
        });
    
    	Schema::create('ws_events', function (Blueprint $table) {
            $table->id();
            $table->integer('wsid')->index();
        	$table->integer('level')->default(0);
        	$table->text('event');
        	$table->text('description')->nullable();
        	$table->timestamps();
        });
    
    	Schema::create('ws_filters', function (Blueprint $table) {
            $table->id();
            $table->string('hash', 255);
        	$table->text('name');
        	$table->text('description')->nullable();
        	$table->text('parameters');
        	$table->timestamps();
        });
    
    	Schema::create('ws_harddrives', function (Blueprint $table) {
            $table->id();
            $table->integer('wsid');
        	$table->string('serial', 255);
        	$table->string('model', 255)->nullable();
        	$table->integer('capacity')->nullable();
        	$table->text('mediatype')->nullable();
        	$table->string('status', 255)->nullable();
        	$table->timestamps();
        });
    
    	Schema::create('ws_interventions', function (Blueprint $table) {
            $table->id();
            $table->integer('wsid')->index();
        	$table->integer('level');
        	$table->text('event');
        	$table->text('description');
        	$table->timestamps();
        });
    
    	Schema::create('ws_ips', function (Blueprint $table) {
            $table->id();
            $table->integer('wsid');
        	$table->string('ip', 255)->nullable();
        	$table->timestamps();
        });
    
    	Schema::create('ws_labels', function (Blueprint $table) {
            $table->id();
            $table->integer('wsid');
        	$table->text('name');
        	$table->string('prop', 255)->nullable();
        	$table->timestamps();
        });
    
    	Schema::create('ws_memories', function (Blueprint $table) {
            $table->id();
            $table->integer('wsid');
        	$table->integer('capacity')->default(0);
        	$table->string('slot', 100)->nullable();
        	$table->string('manufacturer', 100)->nullable();
        	$table->string('serial', 255)->nullable();
        	$table->integer('speed')->default(0);
        	$table->integer('type')->default(0);
        	$table->timestamps();
        });
    
    	Schema::create('ws_monitors', function (Blueprint $table) {
            $table->id();
            $table->integer('wsid');
        	$table->string('instance_name', 255)->nullable();
        	$table->string('manufacturer', 100)->nullable();
        	$table->string('name', 255)->nullable();
        	$table->string('serial', 255)->nullable();
        	$table->integer('year')->nullable();
        	$table->string('inventory_id', 100)->nullable();
        	$table->timestamps();
        });
    
    	Schema::create('ws_printers', function (Blueprint $table) {
            $table->id();
            $table->integer('wsid');
        	$table->string('name', 255)->nullable();
        	$table->string('port', 255)->nullable();
        	$table->integer('default')->nullable();
        	$table->integer('network')->nullable();
        	$table->integer('shared')->nullable();
        	$table->timestamps();
        });
    
    	Schema::create('ws_print_stats', function (Blueprint $table) {
            $table->id();
            $table->integer('wsid');
        	$table->integer('counter')->default(0);
        	$table->integer('pages')->default(0);
        	$table->timestamps();
        });
    
    	Schema::create('ws_user_accounts', function (Blueprint $table) {
            $table->id();
            $table->integer('wsid');
        	$table->string('username', 255)->nullable();
        	$table->string('sid', 255)->nullable();
        	$table->integer('isadmin')->default(0);
        	$table->timestamps();
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workstations');
        Schema::dropIfExists('ws_connection');
        Schema::dropIfExists('ws_control_logs');
        Schema::dropIfExists('ws_dns');
    	Schema::dropIfExists('ws_events');
    	Schema::dropIfExists('ws_filters');
    	Schema::dropIfExists('ws_harddrives');
    	Schema::dropIfExists('ws_interventions');
    	Schema::dropIfExists('ws_ips');
    	Schema::dropIfExists('ws_labels');
    	Schema::dropIfExists('ws_memories');
    	Schema::dropIfExists('ws_monitors');
    	Schema::dropIfExists('ws_printers');
    	Schema::dropIfExists('ws_print_stats');
    	Schema::dropIfExists('ws_user_accounts');
    
    }
};
