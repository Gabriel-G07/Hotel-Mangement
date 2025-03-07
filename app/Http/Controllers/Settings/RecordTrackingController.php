<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RecordTrackingController extends Controller
{
    public function index(Request $request): Response
    {
        $logs = AuditLog::with('user')->orderBy('created_at', 'desc')->get()->map(function ($log) {
            return [
                'user' => [
                    'username' => $log->user->username,
                    'full_name' => $log->user->first_name . ' ' . $log->user->last_name,
                ],
                'action' => $log->action,
                'new_value' => $log->new_value,
                'old_value' => $log->old_value,
                'table_column' => $log->table_name . ' - ' . $log->column_affected,
                'created_at' => $log->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return Inertia::render('management/settings/activities', [
            'logs' => $logs,
        ]);
    }
}
