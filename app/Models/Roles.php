<?php

// app/Models/Roles.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_name',
        'description',
    ];

    protected $table = 'user_roles';
    protected $primaryKey = 'role_id';
    public $incrementing = true;


    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }
}
