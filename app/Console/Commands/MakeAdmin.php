<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    // THE FIX: This line tells Laravel to expect an email argument
    protected $signature = 'app:make-admin {email}';

    protected $description = 'Give an existing user the Admin role';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("No user found with the email: {$email}. Please register on the site first.");
            return self::FAILURE;
        }

        $user->role = 'Admin';
        $user->save();

        $this->info("✅ {$user->email} is now an Admin.");
        return self::SUCCESS;
    }
}