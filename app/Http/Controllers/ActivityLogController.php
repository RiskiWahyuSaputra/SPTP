<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $roleSlug = $user->role->slug;

        $query = ActivityLog::with('user')->latest();

        if ($roleSlug === 'staff') {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHasMorph('loggable', ['App\Models\Submission'], function ($q) use ($user) {
                      $q->where('user_id', $user->id);
                  });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20)->withQueryString();
        $types = ActivityLog::select('type')->distinct()->pluck('type');

        return view('activity-logs.index', compact('logs', 'types', 'roleSlug'));
    }
}
