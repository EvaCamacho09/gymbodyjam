<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CreateTestToken extends Command
{
    protected $signature = 'test:token';
    protected $description = 'Create a test token for API authentication';

    public function handle()
    {
        $user = User::first();
        
        if (!$user) {
            $this->error('No users found in database');
            return 1;
        }
        
        // Delete existing tokens for this user
        $user->tokens()->delete();
        
        // Create new token
        $token = $user->createToken('dashboard-test')->plainTextToken;
        
        $this->info("User: {$user->name}");
        $this->info("Email: {$user->email}");
        $this->info("Token: {$token}");
        $this->info("Use this token in localStorage with key 'token'");
        
        return 0;
    }
}
