<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MCAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mcadmin:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user to login with';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->ask('Set the user\'s name');
        $email = $this->ask('Set the user\'s email');
        $password = $this->secret('Set the user\'s password');
        $confirm = $this->secret('Confirm the user\'s password');

        if ($password != $confirm) {
            return $this->error('Password confirmation did not match.');
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        return $this->info('User created successfully! You may now login.');
    }
}
