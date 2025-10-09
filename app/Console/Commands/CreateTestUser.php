<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateTestUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test user for debugging';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $existing = User::where('email', 'test@example.com')->first();
        if ($existing) {
            $this->info('User test đã tồn tại!');
            return 0;
        }

        
        $user = User::create([
            'email' => 'test@example.com',
            'password_hash' => Hash::make('password'),
            'status_id' => 1,
            'user_type' => 'admin',
        ]);

        $this->info('Test user created successfully!');
        $this->info('Email: test@example.com');
        $this->info('Password: password');
        $this->info('User Type: admin');

        return 0;
    }
}
