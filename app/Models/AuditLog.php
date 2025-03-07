<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs'; // Ensure this matches your table name
    protected $primaryKey = 'log_id'; // Ensure this matches your primary key name

    protected $fillable = [
        'table_name',
        'record_id',
        'action',
        'old_value',
        'new_value',
        'changed_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by', 'username');
    }
}
