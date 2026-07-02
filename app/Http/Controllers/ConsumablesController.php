<?php

namespace App\Http\Controllers;

use App\Models\Consumable;
use App\Models\ConsumableCategory;
use App\Models\ConsumableRequest;
use App\Models\ConsumableRequestItem;
use App\Models\ConsumableStockLog;
use Illuminate\Http\Request;

class ConsumablesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $items = Consumable::with('category')->orderBy('item_name')->get();

        $stats = [
            'total'        => $items->count(),
            'available'    => $items->where('status', 'available')->count(),
            'low'          => $items->where('status', 'low')->count(),
            'critical'     => $items->where('status', 'critical')->count(),
            'out_of_stock' => $items->filter(fn($i) => $i->current_stock <= 0)->count(),
        ];

        return view('pages.consumables', compact('items', 'stats'));
    }
}