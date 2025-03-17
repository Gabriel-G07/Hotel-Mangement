<?php
// app/Console/Commands/CreateSuperuser.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Roles;
use App\Models\Settings;

class CreateSuperuser extends Command
{
    protected $signature = 'superuser:create';

    protected $description = 'Create a superuser and an unassigned role';

    public function handle()
    {
        $unassignedRole = Roles::firstOrCreate(
            ['role_name' => 'Unassigned'],
            ['description' => 'Default role for users without a specific role']
        );

        $this->info('Unassigned role created or already exists!');

        $GuestRole = Roles::firstOrCreate(
            ['role_name' => 'Guest'],
            ['description' => 'These are the hotel customers']
        );

        $this->info('Guest role created or already exists!');

        $developerRole = Roles::firstOrCreate(
            ['role_name' => 'Developer'],
            ['description' => 'System Development and Maintenance']
        );

        $this->info('Developer role created or already exists!');

        $superuser = User::firstOrCreate(
            ['email' => 'gabrielg@doubleg.tech'],
            [
                'username' => 'gg07',
                'first_name' => 'Gabriel',
                'last_name' => 'G',
                'password' => bcrypt('1234'),
                'phone_number' => '+263783298690',
                'national_id_number' => '63-1685210 H 03',
                'is_verified' => 1,
                'role_id' => $developerRole->role_id,
                'profile_picture' => 'https://lh3.googleusercontent.com/a/ACg8ocJ91Qw-fHSrpmd2cmufXi-kV7L8bcC3sr_bJ_dUt9nABYjHt4Ml=s96-c-rg-br100',
                'address' => 'Harare, Zimbabwe',
            ]
        );

        $this->info('Superuser created or already exists!');

        Settings::firstOrCreate(
            ['user_id' => $superuser->id],
            [
                'theme' => 'system',
                'screen_timeout' => 30,
                'font_style' => 'sans-serif',
                'font_size' => 16,
                'notifications_enabled' => true,
                'language' => 'en',
                'timezone' => 'UTC',
                'two_factor_auth' => false,
                'date_format' => 'Y-m-d',
                'time_format' => 'H:i',
            ]
        );

        $this->info('Superuser settings created or already exists!');
    }
}
