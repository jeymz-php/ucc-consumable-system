<?php

namespace App\Http\Controllers;

use App\Models\Consumable;
use App\Models\ConsumableRequest;
use App\Models\ConsumableRequestItem;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function message(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $session = session('chatbot', [
            'step'    => 'greeting',
            'cart'    => [],
            'current' => null,
        ]);

        $userMsg  = trim($request->message);
        $step     = $session['step'];
        $cart     = $session['cart'];
        $current  = $session['current'];
        $response = '';
        $actions  = [];
        $type     = 'bot';

        switch ($step) {

            case 'greeting':
                $response = "Hello, **" . auth()->user()->name . "**! 👋 I'm your UCC-CS assistant.\n\nHow can I help you today?";
                $actions  = [
                    ['label' => '📦 Request consumable items', 'value' => 'request'],
                    ['label' => '💬 Talk to Admin',            'value' => 'admin'],
                ];
                $session['step'] = 'main_menu';
                break;

            case 'main_menu':
                if (str_contains(strtolower($userMsg), 'request') || $userMsg === 'request') {
                    $session['step'] = 'search_item';
                    $response = "Sure! Let's build your request. 📋\n\nType the name of the first item you'd like to request:";
                } elseif (str_contains(strtolower($userMsg), 'admin') || $userMsg === 'admin') {
                    $session = ['step' => 'greeting', 'cart' => [], 'current' => null];
                    session(['chatbot' => $session]);
                    return response()->json([
                        'response' => "I'll take you to the Messages page where you can open a ticket and chat directly with an administrator. 💬",
                        'actions'  => [
                            ['label' => '💬 Go to Messages', 'value' => 'goto_messages', 'url' => route('messages.index')],
                        ],
                        'type' => 'bot',
                        'step' => 'main_menu',
                    ]);
                } else {
                    $response = "I didn't quite get that. Please choose an option:";
                    $actions  = [
                        ['label' => '📦 Request consumable items', 'value' => 'request'],
                        ['label' => '💬 Talk to Admin',            'value' => 'admin'],
                    ];
                }
                break;

            case 'search_item':
                $items = Consumable::where('item_name', 'like', "%{$userMsg}%")
                    ->orderBy('item_name')
                    ->take(6)
                    ->get(['id', 'item_name', 'unit', 'current_stock', 'status']);

                if ($items->isEmpty()) {
                    $response = "❌ I couldn't find any items matching **\"{$userMsg}\"**.\n\nTry a different name:";
                } else {
                    $response = "I found these items matching **\"{$userMsg}\"**:\n\nWhich one would you like to request?";
                    $actions  = $items->map(fn($i) => [
                        'label' => "{$i->item_name} — Stock: {$i->current_stock} {$i->unit}" . ($i->current_stock <= 0 ? ' ⛔ Out of stock' : ($i->status === 'critical' ? ' ⚠️ Critical' : ($i->status === 'low' ? ' 🔶 Low' : ' ✅ Available'))),
                        'value' => "select_item:{$i->id}",
                        'disabled' => $i->current_stock <= 0,
                    ])->toArray();
                    $session['step'] = 'select_item';
                }
                break;

            case 'select_item':
                if (str_starts_with($userMsg, 'select_item:')) {
                    $itemId   = (int) str_replace('select_item:', '', $userMsg);
                    $item     = Consumable::find($itemId);

                    if (!$item) {
                        $response = "Item not found. Please search again:";
                        $session['step'] = 'search_item';
                        break;
                    }

                    if ($item->current_stock <= 0) {
                        $response = "⛔ Sorry, **{$item->item_name}** is out of stock and cannot be requested right now.\n\nWould you like to search for something else?";
                        $actions  = [['label' => '🔍 Search another item', 'value' => 'search_again']];
                        $session['step'] = 'after_item';
                        break;
                    }

                    $session['current'] = ['id' => $item->id, 'name' => $item->item_name, 'unit' => $item->unit, 'stock' => $item->current_stock];
                    $session['step']    = 'enter_quantity';
                    $response = "Great choice! **{$item->item_name}** is available ✅\nCurrent stock: **{$item->current_stock} {$item->unit}**\n\nHow many units would you like to request? (max: {$item->current_stock})";
                } else {
                    $response = "Please select an item from the options above, or type a name to search:";
                    $session['step'] = 'search_item';
                }
                break;

            case 'enter_quantity':
                $qty = (int) $userMsg;
                if ($qty <= 0 || !is_numeric($userMsg)) {
                    $response = "Please enter a valid quantity (a number greater than 0):";
                    break;
                }
                if ($qty > $current['stock']) {
                    $response = "⚠️ That exceeds available stock ({$current['stock']} {$current['unit']}). Please enter a smaller quantity:";
                    break;
                }
                $session['current']['qty'] = $qty;
                $session['step']           = 'enter_purpose';
                $response = "Got it — **{$qty} {$current['unit']}** of **{$current['name']}**.\n\nWhat is the purpose of this request?";
                break;

            case 'enter_purpose':
                if (strlen($userMsg) < 3) {
                    $response = "Please provide a brief purpose (e.g. Office use, Lab supplies):";
                    break;
                }
                $session['current']['purpose'] = $userMsg;
                $cart[] = $session['current'];
                $session['cart']    = $cart;
                $session['current'] = null;
                $session['step']    = 'after_item';

                $cartSummary = collect($cart)->map(fn($i, $k) => ($k+1) . ". {$i['name']} — {$i['qty']} {$i['unit']} ({$i['purpose']})")->join("\n");
                $response = "✅ Added to your request!\n\n**Current Cart:**\n{$cartSummary}\n\nWould you like to add more items or proceed?";
                $actions  = [
                    ['label' => '➕ Add another item',  'value' => 'search_again'],
                    ['label' => '📋 Review & Submit',   'value' => 'review'],
                ];
                break;

            case 'after_item':
                if ($userMsg === 'search_again') {
                    $session['step'] = 'search_item';
                    $response = "Sure! Type the name of the next item you'd like to add:";
                } elseif ($userMsg === 'review') {
                    if (empty($cart)) {
                        $response = "Your cart is empty. Let's add some items first:";
                        $session['step'] = 'search_item';
                        break;
                    }
                    $cartSummary = collect($cart)->map(fn($i, $k) =>
                        ($k+1) . ". **{$i['name']}** — {$i['qty']} {$i['unit']}\n   Purpose: {$i['purpose']}"
                    )->join("\n\n");

                    $response = "📋 **Request Summary:**\n\n{$cartSummary}\n\n---\n**Requester:** " . auth()->user()->name . "\n**Department:** " . (auth()->user()->department->department_name ?? 'N/A') . "\n\nReady to submit this request?";
                    $actions  = [
                        ['label' => '✅ Confirm & Submit',  'value' => 'confirm'],
                        ['label' => '➕ Add more items',    'value' => 'search_again'],
                        ['label' => '❌ Cancel request',   'value' => 'cancel'],
                    ];
                    $session['step'] = 'confirm';
                } else {
                    $response = "What would you like to do?";
                    $actions  = [
                        ['label' => '➕ Add another item',  'value' => 'search_again'],
                        ['label' => '📋 Review & Submit',   'value' => 'review'],
                    ];
                }
                break;

            case 'confirm':
                if ($userMsg === 'confirm') {
                    session(['chatbot' => $session]);
                    return $this->submitRequest($request, $cart);
                } elseif ($userMsg === 'search_again') {
                    $session['step'] = 'search_item';
                    $response = "Sure! Type the next item name:";
                } elseif ($userMsg === 'cancel') {
                    $session = ['step' => 'greeting', 'cart' => [], 'current' => null];
                    $response = "Request cancelled. Your cart has been cleared.\n\nIs there anything else I can help you with?";
                    $actions  = [
                        ['label' => '📦 Request consumable items', 'value' => 'request'],
                        ['label' => '💬 Talk to Admin',            'value' => 'admin'],
                    ];
                    $session['step'] = 'main_menu';
                } else {
                    $response = "Please confirm your request:";
                    $actions  = [
                        ['label' => '✅ Confirm & Submit', 'value' => 'confirm'],
                        ['label' => '❌ Cancel',           'value' => 'cancel'],
                    ];
                }
                break;

            default:
                $session = ['step' => 'greeting', 'cart' => [], 'current' => null];
                $response = "Let me start over. How can I help you?";
                $actions  = [
                    ['label' => '📦 Request consumable items', 'value' => 'request'],
                    ['label' => '💬 Talk to Admin',            'value' => 'admin'],
                ];
                $session['step'] = 'main_menu';
        }

        session(['chatbot' => $session]);

        return response()->json([
            'response' => $response,
            'actions'  => $actions,
            'type'     => $type,
            'step'     => $session['step'],
        ]);
    }

    private function submitRequest(Request $request, array $cart)
    {
        $authUser = auth()->user();

        $parsed      = $this->parseRecipientName($authUser->name);
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
            'status'               => 'pending',
            'requested_by'         => $authUser->id,
            'source'               => 'cs',
        ]);

        foreach ($cart as $item) {
            ConsumableRequestItem::create([
                'consumable_request_id' => $consumableRequest->id,
                'consumable_id'         => $item['id'],
                'quantity'              => $item['qty'],
                'purpose'               => $item['purpose'],
                'status'                => 'pending',
            ]);
        }

        ActivityLog::record('create', 'Consumables',
            "Chatbot submitted consumable request {$consumableRequest->reference_no} [source: CS]"
        );

        // Clear chatbot session
        session()->forget('chatbot');

        $itemList = collect($cart)->map(fn($i, $k) => ($k+1) . ". {$i['name']} — {$i['qty']} {$i['unit']}")->join("\n");

        return response()->json([
            'response' => "🎉 **Request submitted successfully!**\n\nReference: **{$consumableRequest->reference_no}**\n\n{$itemList}\n\nYour request is now pending review by an administrator. You'll be notified once it's processed.\n\nIs there anything else I can help you with?",
            'actions'  => [
                ['label' => '📋 View my requests', 'value' => 'view_requests', 'url' => route('consumable-requests')],
                ['label' => '📦 New request',      'value' => 'request'],
            ],
            'type'     => 'bot',
            'step'     => 'main_menu',
            'reference_no' => $consumableRequest->reference_no,
        ]);
    }

    private function parseRecipientName(?string $fullName): array
    {
        $trimmed = trim((string) $fullName);
        if ($trimmed === '') return ['first_name' => '', 'mi' => null, 'last_name' => ''];
        $parts    = preg_split('/\s+/', $trimmed);
        $lastName = array_pop($parts);
        $mi       = null;
        if (count($parts) >= 2) {
            $possibleMi = end($parts);
            if (preg_match('/^[A-Za-z]{1,2}\.?$/', $possibleMi)) {
                $mi = array_pop($parts);
                if (!str_ends_with($mi, '.')) $mi .= '.';
            }
        }
        return ['first_name' => implode(' ', $parts), 'mi' => $mi, 'last_name' => $lastName];
    }

    public function reset()
    {
        session()->forget('chatbot');
        return response()->json(['ok' => true]);
    }
}