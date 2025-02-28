<?php
// app/Console/Commands/CreateSuperuser.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class CreateSuperuser extends Command
{
    protected $signature = 'superuser:create';

    protected $description = 'Create a superuser';

    public function handle()
    {
        $superuser = new User();
        $superuser->username = 'gg07';
        $superuser->first_name = 'Gabriel';
        $superuser->last_name = 'G';
        $superuser->email = 'gabrielg@doubleg.tech';
        $superuser->password = bcrypt('1234');
        $superuser->phone_number = '+262783298690';
        $superuser->national_id_number = '63-1685210 H 03';
        $superuser->is_verified = 1;
        $superuser->save();

        $this->info('Superuser created successfully!');
    }
}
