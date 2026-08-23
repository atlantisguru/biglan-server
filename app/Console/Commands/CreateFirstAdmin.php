<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Users;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Str;

class CreateFirstAdmin extends Command
{
    protected $signature = 'biglan:create-admin';
    protected $description = 'Creates the first administrator account interactively during installation.';

    public function handle()
    {
        if (Users::count() > 0) {
            $this->error('A user already exists - this command only runs on a fresh installation.');
            return 1;
        }

        $username = $this->ask('Admin fullname (min. 8 characters)');
        if (strlen($username) < 8) {
            $this->error('Username must be at least 8 characters.');
            return 1;
        }

        $email = $this->ask('Admin email address');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address.');
            return 1;
        }

        $password = $this->secret('Admin password (min. 8 characters)');
        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters.');
            return 1;
        }

        $passwordConfirm = $this->secret('Confirm admin password');
        if ($password !== $passwordConfirm) {
            $this->error('Passwords do not match.');
            return 1;
        }

        if (Users::where('email', $email)->exists()) {
            $this->error('This email address is already in use.');
            return 1;
        }

        $token = Str::random(32);
        while (Users::where('token', $token)->exists()) {
            $token = Str::random(32);
        }

        $user = new Users();
        $user->username = $username;
        $user->email = $email;
        $user->password = bcrypt($password);
        $user->token = $token;
        $user->language = config('app.locale');
        $user->confirmed = 1;
        $user->save();

        (new RegisterController())->createFirstUserPermissions($user->id);

        $this->info("Admin account '{$username}' created successfully.");
        return 0;
    }
}
