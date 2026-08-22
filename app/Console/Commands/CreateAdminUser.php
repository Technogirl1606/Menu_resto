<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create {name} {email} {password}';
    protected $description = 'Crée un compte administrateur pour un restaurant';

    public function handle(): void
    {
        if (User::where('email', $this->argument('email'))->exists()) {
            $this->error('Un compte existe déjà avec cet email.');
            return;
        }

        User::create([
            'name' => $this->argument('name'),
            'email' => $this->argument('email'),
            'password' => bcrypt($this->argument('password')),
            'email_verified_at' => now(),
        ]);

        $this->info('Compte admin créé pour '.$this->argument('email'));
    }
}