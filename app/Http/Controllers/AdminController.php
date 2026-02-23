<?php

namespace App\Http\Controllers;

use App\Models\AdminApiSetting;
use App\Models\ApiUsageLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    // ─────────────────────────────────────────────────
    // Admin Dashboard Overview
    // ─────────────────────────────────────────────────

    public function dashboard()
    {
        $stats = [
            'total_users'         => User::count(),
            'total_api_calls'     => ApiUsageLog::count(),
            'calls_today'         => ApiUsageLog::whereDate('created_at', today())->count(),
            'calls_this_week'     => ApiUsageLog::where('created_at', '>=', now()->startOfWeek())->count(),
            'failed_calls'        => ApiUsageLog::where('success', false)->count(),
            'approved_apis'       => AdminApiSetting::where('status', 'approved')->count(),
            'pending_apis'        => AdminApiSetting::where('status', 'pending')->count(),
            'disabled_apis'       => AdminApiSetting::where('status', 'disabled')->count(),
        ];

        // Per-API call breakdown
        // Use CASE WHEN instead of SUM(success) because PostgreSQL cannot SUM a boolean column directly
        $apiBreakdown = ApiUsageLog::selectRaw('api_source, COUNT(*) as total, SUM(CASE WHEN success THEN 1 ELSE 0 END) as successful, AVG(response_time_ms) as avg_ms')
            ->groupBy('api_source')
            ->get();

        // Recent 5 logs
        $recentLogs = ApiUsageLog::with('user')
            ->latest()
            ->limit(5)
            ->get();

        // Top 5 users by usage
        $topUsers = User::withCount('apiUsageLogs')
            ->orderByDesc('api_usage_logs_count')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'apiBreakdown', 'recentLogs', 'topUsers'));
    }

    // ─────────────────────────────────────────────────
    // API Settings Management
    // ─────────────────────────────────────────────────

    public function apiSettings()
    {
        $settings = AdminApiSetting::with('approvedBy')->latest()->get();
        return view('admin.api-settings', compact('settings'));
    }

    public function updateApiSetting(Request $request, AdminApiSetting $setting)
    {
        $request->validate([
            'api_key'  => 'nullable|string|max:500',
            'api_url'  => 'nullable|url|max:500',
            'notes'    => 'nullable|string|max:1000',
        ]);

        $setting->update([
            'api_key'  => $request->api_key ?? $setting->api_key,
            'api_url'  => $request->api_url ?? $setting->api_url,
            'notes'    => $request->notes,
        ]);

        // Clear the specific caches so BookApiService picks up changes immediately
        Cache::forget('admin_api_status_' . $setting->api_name);
        Cache::forget('admin_google_books_setting'); // relevant if google_books key changed

        return back()->with('success', "API settings for \"{$setting->display_name}\" updated successfully.");
    }

    public function approveApi(AdminApiSetting $setting)
    {
        $setting->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        Cache::forget('admin_api_status_' . $setting->api_name);
        Cache::forget('admin_google_books_setting');

        return back()->with('success', "\"{$setting->display_name}\" has been approved and is now active.");
    }

    public function disableApi(AdminApiSetting $setting)
    {
        $setting->update(['status' => 'disabled']);

        Cache::forget('admin_api_status_' . $setting->api_name);
        Cache::forget('admin_google_books_setting');

        return back()->with('success', "\"{$setting->display_name}\" has been disabled.");
    }

    public function setPendingApi(AdminApiSetting $setting)
    {
        $setting->update([
            'status'      => 'pending',
            'approved_by' => null,
            'approved_at' => null,
        ]);

        Cache::forget('admin_api_status_' . $setting->api_name);
        Cache::forget('admin_google_books_setting');

        return back()->with('success', "\"{$setting->display_name}\" status set back to pending.");
    }

    // ─────────────────────────────────────────────────
    // Usage Logs
    // ─────────────────────────────────────────────────

    public function usageLogs(Request $request)
    {
        $query = ApiUsageLog::with('user')->latest();

        // Filter by API source
        if ($request->filled('api_source')) {
            $query->where('api_source', $request->api_source);
        }

        // Filter by success/fail
        if ($request->filled('status')) {
            $query->where('success', $request->status === 'success' ? true : false);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs  = $query->paginate(25)->withQueryString();
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('admin.usage-logs', compact('logs', 'users'));
    }

    // ─────────────────────────────────────────────────
    // Users Management
    // ─────────────────────────────────────────────────

    public function users(Request $request)
    {
        $users = User::withCount('apiUsageLogs')
            ->orderByDesc('api_usage_logs_count')
            ->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function promoteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->update(['role' => 'admin']);
        return back()->with('success', "{$user->name} has been promoted to administrator.");
    }

    public function demoteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->update(['role' => 'user']);
        return back()->with('success', "{$user->name} has been demoted to regular user.");
    }
}
