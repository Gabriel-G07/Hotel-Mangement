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

        $managerRole = Roles::firstOrCreate(
            ['role_name' => 'Manager'],
            ['description' => 'Managing and looking over the whole Hotel']
        );
        $this->info('Manager role created or already exists!');

        $receptionistRole = Roles::firstOrCreate(
            ['role_name' => 'Receptionist'],
            ['description' => 'Receiving and Serving Guests at the Hotel']
        );
        $this->info('Receptionist role created or already exists!');

        $accountantRole = Roles::firstOrCreate(
            ['role_name' => 'Accountant'],
            ['description' => 'Looking Over all Finances of the Hotel']
        );
        $this->info('Accountant role created or already exists!');

        $restaurantmanagerRole = Roles::firstOrCreate(
            ['role_name' => 'Restaurant Manager'],
            ['description' => 'Managing all the Activities in the Restaurant and Staff']
        );
        $this->info('Restaurant Manager role created or already exists!');

        $housekeepingmanagerRole = Roles::firstOrCreate(
            ['role_name' => 'Housekeeping Manager'],
            ['description' => 'Looking Over and Managing all House Keeping Issue and Staff']
        );
        $this->info('Housekeeping Manager role created or already exists!');

        $restauranttilloperatorRole = Roles::firstOrCreate(
            ['role_name' => 'Restaurant Till Operator'],
            ['description' => 'Operates the Till in the Restaurant']
        );
        $this->info('Restaurant Till Operator role created or already exists!');

        $housekeeperRole = Roles::firstOrCreate(
            ['role_name' => 'Housekeeping'],
            ['description' => 'Makes Sure that Everything is in Order and Clean Serves the Guests in all the Requests they have']
        );
        $this->info('Housekeeping role created or already exists!');

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

        $receptionist = User::firstOrCreate(
            ['email' => 'nyasha@doubleg.tech'],
            [
                'username' => 'ny',
                'first_name' => 'Nyasha',
                'last_name' => 'Mugodhi',
                'password' => bcrypt('1234@Qwer'),
                'phone_number' => '+0000000001',
                'national_id_number' => '63-xxxxxx H 03',
                'is_verified' => 1,
                'role_id' => $receptionistRole->role_id,
                'profile_picture' => 'https://lh3.googleusercontent.com/a/ACg8ocJ91Qw-fHSrpmd2cmufXi-kV7L8bcC3sr_bJ_dUt9nABYjHt4Ml=s96-c-rg-br100',
                'address' => 'Mberengwa, Chegato',
            ]
        );
        $this->info('Receptionist created or already exists!');

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

        Settings::firstOrCreate(
            ['user_id' => $receptionist->id],
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
        $this->info('Receptionist settings created or already exists!');

    }
}
