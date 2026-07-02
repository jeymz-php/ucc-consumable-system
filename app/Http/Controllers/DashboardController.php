<?php

namespace App\Http\Controllers;

use App\Models\Consumable;
use App\Models\ConsumableRequest;
use App\Models\ConsumableCategory;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $role = $user->role;

        $allConsumables = Consumable::all();

        $stats = [
            'total_items'      => $allConsumables->count(),
            'available'        => $allConsumables->filter(fn($c) => $c->status === 'available')->count(),
            'low_stock'        => $allConsumables->filter(fn($c) => $c->status === 'low')->count(),
            'critical'         => $allConsumables->filter(fn($c) => $c->status === 'critical')->count(),
            'categories'       => ConsumableCategory::count(),
            'pending_requests' => ConsumableRequest::where('status', 'pending')->count(),
            'my_requests'      => ConsumableRequest::where('requested_by', $user->id)->count(),
            'my_pending'       => ConsumableRequest::where('requested_by', $user->id)->where('status', 'pending')->count(),
        ];

        // Recent requests - admin sees all, user sees own
        $recentRequests = ConsumableRequest::with(['requester', 'items'])
            ->when($role === 'user', fn($q) => $q->where('requested_by', $user->id))
            ->latest()
            ->take(5)
            ->get();

        // Low/critical items for admin warning panel
        $criticalItems = Consumable::with('category')
            ->whereRaw('current_stock <= critical_threshold')
            ->orderBy('current_stock')
            ->take(5)
            ->get();

        // Top requested items (based on approved request items)
        $topItems = \App\Models\ConsumableRequestItem::with('consumable')
            ->where('status', 'approved')
            ->selectRaw('consumable_id, SUM(quantity) as total_qty')
            ->groupBy('consumable_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        return view('pages.dashboard', compact(
            'user', 'role', 'stats',
            'recentRequests', 'criticalItems', 'topItems'
        ));
    }
}