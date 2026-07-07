<?php

namespace App\Http\Controllers;

use App\Models\CashBalance;
use App\Models\Role;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;

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

        return [
            'totalSubmissions' => Submission::where('user_id', $userId)->count(),
            'pendingSubmissions' => Submission::where('user_id', $userId)
                ->whereIn('current_status', ['draft', 'submitted', 'waiting_spv', 'waiting_manager', 'waiting_director'])
                ->count(),
            'approvedSubmissions' => Submission::where('user_id', $userId)
                ->where('current_status', 'paid')
                ->count(),
            'rejectedSubmissions' => Submission::where('user_id', $userId)
                ->where('current_status', 'rejected')
                ->count(),
            'recentSubmissions' => Submission::with('category')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
            'lastSubmission' => Submission::where('user_id', $userId)
                ->whereIn('current_status', ['draft', 'submitted', 'waiting_spv', 'waiting_manager', 'waiting_director'])
                ->orderBy('created_at', 'desc')
                ->first(),
        ];
    }

    private function approverDashboard(string $roleSlug): array
    {
        $role = Role::where('slug', $roleSlug)->firstOrFail();

        $pendingCount = Submission::whereHas('approvals', function ($q) use ($role) {
            $q->where('role_id', $role->id)->where('decision', 'pending');
        })->count();

        $pendingSubmissions = Submission::with('category', 'user')
            ->whereHas('approvals', function ($q) use ($role) {
                $q->where('role_id', $role->id)->where('decision', 'pending');
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return [
            'pendingCount' => $pendingCount,
            'pendingSubmissions' => $pendingSubmissions,
        ];
    }

    private function financeDashboard(): array
    {
        $waitingFinance = Submission::where('current_status', 'waiting_finance')->count();
        $totalPaid = Submission::where('current_status', 'paid')->count();
        $totalRejected = Submission::where('current_status', 'rejected')->count();
        $cashBalance = CashBalance::first();

        $recentWaiting = Submission::with('category', 'user')
            ->where('current_status', 'waiting_finance')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return [
            'waitingFinance' => $waitingFinance,
            'totalPaid' => $totalPaid,
            'totalRejected' => $totalRejected,
            'cashBalance' => $cashBalance,
            'recentWaiting' => $recentWaiting,
        ];
    }
}
