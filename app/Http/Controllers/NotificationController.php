<?php

namespace App\Http\Controllers;

use App\Models\AccountDeletionRequest;
use App\Models\ConsumableRequest;
use App\Models\Conversation;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $tab    = $request->get('tab', 'deletions');
        $user   = auth()->user();
        $isAdmin = in_array($user->role, ['admin', 'superadmin']);

        if (!$isAdmin) {
            return redirect()->route('dashboard');
        }

        if ($tab === 'consumables') {
            $requests = ConsumableRequest::with(['requester', 'reviewer', 'items'])
                ->when($status !== 'all', fn($q) => $q->where('status', $status))
                ->latest()
                ->paginate(15)
                ->withQueryString();

            $stats = [
                'pending'  => ConsumableRequest::where('status', 'pending')->count(),
                'approved' => ConsumableRequest::whereIn('status', ['approved', 'partial'])->count(),
                'rejected' => ConsumableRequest::where('status', 'rejected')->count(),
            ];

            return view('pages.notifications', compact('requests', 'stats', 'status', 'tab'));
        }

        $requests = AccountDeletionRequest::with(['user', 'reviewer'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'pending'  => AccountDeletionRequest::where('status', 'pending')->count(),
            'approved' => AccountDeletionRequest::where('status', 'approved')->count(),
            'rejected' => AccountDeletionRequest::where('status', 'rejected')->count(),
        ];

        return view('pages.notifications', compact('requests', 'stats', 'status', 'tab'));
    }

    public function poll()
    {
        $user    = auth()->user();
        $isAdmin = in_array($user->role, ['admin', 'superadmin']);

        if ($isAdmin) {
            // ── Admin sees: pending consumable requests + pending deletion requests ──
            $deletionRequests = AccountDeletionRequest::with('user')
                ->where('status', 'pending')->latest()->get()
                ->map(fn($r) => [
                    'type'       => 'deletion',
                    'id'         => $r->id,
                    'title'      => $r->user->name ?? 'Unknown',
                    'subtitle'   => $r->user->email ?? '',
                    'reason'     => $r->reason,
                    'created_at' => $r->created_at->diffForHumans(),
                ]);

            $consumableRequests = ConsumableRequest::with('requester')
                ->where('status', 'pending')->latest()->get()
                ->map(fn($r) => [
                    'type'       => 'consumable',
                    'id'         => $r->id,
                    'title'      => $r->reference_no,
                    'subtitle'   => ($r->requester->name ?? '—') . ' — ' . $r->department,
                    'reason'     => null,
                    'created_at' => $r->created_at->diffForHumans(),
                ]);

            $all = $deletionRequests->concat($consumableRequests)->values();

            return response()->json([
                'count'            => $all->count(),
                'deletion_count'   => $deletionRequests->count(),
                'consumable_count' => $consumableRequests->count(),
                'requests'         => $all,
            ]);
        }

        // ── User sees: approved/partial/rejected requests (last 7 days) + unread admin messages ──
        $requestUpdates = ConsumableRequest::where('requested_by', $user->id)
            ->whereIn('status', ['approved', 'partial', 'rejected'])
            ->where('updated_at', '>=', now()->subDays(7))
            ->latest('updated_at')
            ->get()
            ->map(fn($r) => [
                'type'       => 'request_update',
                'id'         => $r->id,
                'title'      => $r->reference_no,
                'subtitle'   => ucfirst($r->status) . ' — ' . $r->items->count() . ' item(s)',
                'status'     => $r->status,
                'created_at' => $r->updated_at->diffForHumans(),
            ]);

        $unreadMessages = Conversation::where('user_id', $user->id)
            ->where('type', 'admin')
            ->whereHas('messages', fn($q) => $q->where('sender_type', 'admin')->where('is_read', false))
            ->with('lastMessage')
            ->get()
            ->map(fn($c) => [
                'type'       => 'message',
                'id'         => $c->id,
                'title'      => $c->ticket_no,
                'subtitle'   => 'Admin replied: ' . \Illuminate\Support\Str::limit($c->lastMessage?->body ?? '', 40),
                'created_at' => $c->lastMessage?->created_at->diffForHumans() ?? '',
            ]);

        $all = $requestUpdates->concat($unreadMessages)->values();

        return response()->json([
            'count'         => $all->count(),
            'request_count' => $requestUpdates->count(),
            'message_count' => $unreadMessages->count(),
            'requests'      => $all,
        ]);
    }

    public function approve(Request $request, AccountDeletionRequest $deletionRequest)
    {
        $user     = $deletionRequest->user;
        $userName = $user->name;

        $deletionRequest->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        \App\Models\ActivityLog::record(
            'delete', 'User',
            "Approved deletion request and deleted user account: {$userName}",
            'user', $user->id
        );

        $user->delete();

        return response()->json(['message' => "Account for {$userName} has been deleted."]);
    }

    public function reject(Request $request, AccountDeletionRequest $deletionRequest)
    {
        $deletionRequest->update([
            'status'      => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        \App\Models\ActivityLog::record(
            'reject', 'User',
            "Rejected deletion request for: {$deletionRequest->user->name}",
            'user', $deletionRequest->user_id
        );

        return response()->json(['message' => 'Deletion request rejected.']);
    }
}