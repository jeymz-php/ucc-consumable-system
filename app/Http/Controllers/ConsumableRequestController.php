<?php

namespace App\Http\Controllers;

use App\Models\ConsumableRequest;
use App\Models\ConsumableRequestItem;
use App\Models\Consumable;
use App\Models\ConsumableStockLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ConsumableRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Parse a full name into first_name, mi, and last_name.
     * Handles: "Juan Dela Cruz"   → first: Juan,      mi: null,  last: Dela Cruz
     *          "Maria P. Santos"  → first: Maria,     mi: P.,    last: Santos
     *          "Jose Santos"      → first: Jose,      mi: null,  last: Santos
     *          "System Administrator" → first: System, mi: null, last: Administrator
     */
    private function parseRecipientName(?string $fullName): array
    {
        $trimmed = trim((string) $fullName);

        if ($trimmed === '') {
            return ['first_name' => '', 'mi' => null, 'last_name' => ''];
        }

        $parts = preg_split('/\s+/', $trimmed);

        if (count($parts) === 1) {
            return ['first_name' => $trimmed, 'mi' => null, 'last_name' => ''];
        }

        // Last token = last name
        $lastName = array_pop($parts);

        // Check if the current last token is a middle initial (1-2 letters, optional period)
        $mi = null;
        if (count($parts) >= 2) {
            $possibleMi = end($parts);
            if (preg_match('/^[A-Za-z]{1,2}\.?$/', $possibleMi)) {
                $mi = array_pop($parts);
                if (!str_ends_with($mi, '.')) {
                    $mi .= '.';
                }
            }
        }

        $firstName = implode(' ', $parts);

        return [
            'first_name' => $firstName,
            'mi'         => $mi,
            'last_name'  => $lastName,
        ];
    }

    public function index(Request $request)
    {
        $status   = $request->get('status', 'all');
        $authUser = auth()->user();

        $requests = ConsumableRequest::with(['items.consumable', 'campus', 'requester', 'reviewer'])
            ->where('requested_by', $authUser->id)
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pages.my_requests', compact('requests', 'status'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items'                 => 'required|array|min:1',
            'items.*.consumable_id' => 'required|exists:consumables,id',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.purpose'       => 'required|string|max:255',
        ]);

        $authUser = auth()->user();
        $parsed   = $this->parseRecipientName($authUser->name);

        $consumableRequest = ConsumableRequest::create([
            'reference_no'         => ConsumableRequest::generateReferenceNo(),
            'recipient_first_name' => $parsed['first_name'],
            'recipient_mi'         => $parsed['mi'],
            'recipient_last_name'  => $parsed['last_name'],
            'campus_id'            => $authUser->campus_id,
            'department'           => $authUser->department->department_name ?? 'N/A',
            'request_date'         => now(),
            'approved_by'          => 'REYNALDO H. CARANDANG JR.',
            'supply_officer'       => 'MARVIN Z. GERVACIO',
            'status'               => 'pending',  // CS users always start as pending
            'requested_by'         => $authUser->id,
            'reviewed_by'          => null,
            'reviewed_at'          => null,
            'source'               => 'cs',       // ← marks this as a CS request
        ]);

        foreach ($request->items as $itemData) {
            ConsumableRequestItem::create([
                'consumable_request_id' => $consumableRequest->id,
                'consumable_id'         => $itemData['consumable_id'],
                'quantity'              => $itemData['quantity'],
                'purpose'               => $itemData['purpose'],
                'status'                => 'pending',
            ]);
        }

        ActivityLog::record(
            'create',
            'Consumables',
            "Submitted consumable request {$consumableRequest->reference_no} (pending review) [source: CS]"
        );

        return redirect()->route('consumable-requests')
            ->with('success', "Request {$consumableRequest->reference_no} submitted and is pending review.");
    }

    public function show(ConsumableRequest $consumableRequest)
    {
        $consumableRequest->load(['items.consumable', 'campus', 'requester', 'reviewer']);
        return response()->json($consumableRequest);
    }

    public function report(ConsumableRequest $consumableRequest)
    {
        $consumableRequest->load(['items.consumable', 'campus', 'requester']);

        $headerLogoPath   = public_path('images/ucc.png');
        $headerLogoBase64 = file_exists($headerLogoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerLogoPath))
            : null;

        $footerLogoPath   = public_path('images/caloocannewlogo.png');
        $footerLogoBase64 = file_exists($footerLogoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerLogoPath))
            : null;

        $pdf = \PDF::loadView('pdf.consumable_release_report', compact(
            'consumableRequest', 'headerLogoBase64', 'footerLogoBase64'
        ));

        return $pdf->stream('Release-Report-' . $consumableRequest->reference_no . '.pdf');
    }
}