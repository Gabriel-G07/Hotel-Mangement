<?php
// app/Console/Commands/CreateSuperuser.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Roles;

class CreateSuperuser extends Command
{
    protected $signature = 'superuser:create';

    protected $description = 'Create a superuser and an unassigned role';

    public function handle()
    {
        // Create the "Unassigned" role if it doesn't exist
        $unassignedRole = Roles::firstOrCreate(
            ['role_name' => 'Unassigned'],
            ['description' => 'Default role for users without a specific role']
        );

        $this->info('Unassigned role created or already exists!');

        // Create the "Developer" role
        $developerRole = Roles::firstOrCreate(
            ['role_name' => 'Developer'],
            ['description' => 'System Development and Maintenance']
        );

        $this->info('Developer role created or already exists!');

        // Create the superuser
        $superuser = User::firstOrCreate(
            ['email' => 'gabrielg@doubleg.tech'],
            [
                'username' => 'gg07',
                'first_name' => 'Gabriel',
                'last_name' => 'G',
                'password' => bcrypt('1234'),
                'phone_number' => '+262783298690',
                'national_id_number' => '63-1685210 H 03',
                'is_verified' => 2,
                'role_id' => $developerRole->role_id,
                'profile_picture' => 'https://lh3.googleusercontent.com/a/ACg8ocJ91Qw-fHSrpmd2cmufXi-kV7L8bcC3sr_bJ_dUt9nABYjHt4Ml=s96-c-rg-br100',
            ]
        );

        $this->info('Superuser created or already exists!');
    }
}
