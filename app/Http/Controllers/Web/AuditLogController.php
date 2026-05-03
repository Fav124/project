<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhere('event', 'like', '%' . $request->search . '%')
              ->orWhere('auditable_type', 'like', '%' . $request->search . '%');
        }

        $logs = $query->latest()->paginate(20);

        return view('audit-logs.index', compact('logs'));
    }
}
