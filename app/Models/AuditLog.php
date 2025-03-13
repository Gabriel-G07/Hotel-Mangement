<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';
    protected $primaryKey = 'log_id';

    protected $fillable = [
        'table_name',
        'record_id',
        'action',
        'old_value',
        'new_value',
        'changed_by',
        'column_affected',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by', 'username');
    }
}
