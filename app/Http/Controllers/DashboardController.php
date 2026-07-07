<?php

namespace App\Http\Controllers;

use App\Models\CashBalance;
use App\Models\Role;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $roleSlug = $user->role->slug;

        $data = match ($roleSlug) {
            'staff' => $this->staffDashboard(),
            'spv', 'manager', 'direktur' => $this->approverDashboard($roleSlug),
            'finance' => $this->financeDashboard(),
            default => [],
        };

        return view('dashboard', array_merge($data, ['roleSlug' => $roleSlug]));
    }

    private function staffDashboard(): array
    {
        $userId = Auth::id();

        $totalSubmissions = Submission::where('user_id', $userId)->count();
        $totalAmount = Submission::where('user_id', $userId)->sum('amount');
        $pending = Submission::where('user_id', $userId)
            ->whereIn('current_status', ['draft', 'submitted', 'waiting_spv', 'waiting_manager', 'waiting_director', 'waiting_finance'])
            ->count();
        $paid = Submission::where('user_id', $userId)->where('current_status', 'paid')->count();
        $rejected = Submission::where('user_id', $userId)->where('current_status', 'rejected')->count();
        $paidAmount = Submission::where('user_id', $userId)->where('current_status', 'paid')->sum('amount');

        $recentSubmissions = Submission::with('category')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $lastDraft = Submission::where('user_id', $userId)
            ->where('current_status', 'draft')
            ->orderBy('created_at', 'desc')
            ->first();

        $monthlyData = Submission::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(amount) as amount')
        )
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return compact(
            'totalSubmissions', 'totalAmount', 'pending', 'paid', 'rejected', 'paidAmount',
            'recentSubmissions', 'lastDraft', 'monthlyData'
        );
    }

    private function approverDashboard(string $roleSlug): array
    {
        $role = Role::where('slug', $roleSlug)->firstOrFail();
        $userId = Auth::id();

        $pendingCount = Submission::whereHas('approvals', function ($q) use ($role) {
            $q->where('role_id', $role->id)->where('decision', 'pending');
        })->count();

        $pendingAmount = Submission::whereHas('approvals', function ($q) use ($role) {
            $q->where('role_id', $role->id)->where('decision', 'pending');
        })->sum('amount');

        $approvedCount = Submission::whereHas('approvals', function ($q) use ($role, $userId) {
            $q->where('role_id', $role->id)->where('approver_id', $userId)->where('decision', 'approved');
        })->count();

        $rejectedCount = Submission::whereHas('approvals', function ($q) use ($role, $userId) {
            $q->where('role_id', $role->id)->where('approver_id', $userId)->where('decision', 'rejected');
        })->count();

        $pendingSubmissions = Submission::with('category', 'user')
            ->whereHas('approvals', function ($q) use ($role) {
                $q->where('role_id', $role->id)->where('decision', 'pending');
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return compact(
            'pendingCount', 'pendingAmount', 'approvedCount', 'rejectedCount',
            'pendingSubmissions', 'roleSlug'
        );
    }

    private function financeDashboard(): array
    {
        $waitingFinance = Submission::where('current_status', 'waiting_finance')->count();
        $totalPaid = Submission::where('current_status', 'paid')->count();
        $totalRejected = Submission::where('current_status', 'rejected')->count();
        $cashBalance = CashBalance::first();

        $waitingAmount = Submission::where('current_status', 'waiting_finance')->sum('amount');
        $totalPaidAmount = Submission::where('current_status', 'paid')->sum('amount');

        $recentWaiting = Submission::with('category', 'user')
            ->where('current_status', 'waiting_finance')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $monthlyPaid = Submission::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(amount) as amount')
        )
            ->where('current_status', 'paid')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return compact(
            'waitingFinance', 'totalPaid', 'totalRejected', 'cashBalance',
            'waitingAmount', 'totalPaidAmount', 'recentWaiting', 'monthlyPaid'
        );
    }
}
