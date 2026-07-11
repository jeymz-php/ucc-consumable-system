<?php

namespace App\Http\Controllers;

use App\Models\Consumable;
use App\Models\ConsumableCategory;
use Illuminate\Http\Request;

class ConsumablesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // CS uses client-side filtering on the card grid,
        // so we load all items and pass them to the view.
        $items = Consumable::with('category')
            ->orderBy('item_name')
            ->get();

        $stats = [
            'total'        => $items->count(),
            'available'    => $items->filter(fn($i) => $i->status === 'available' && $i->current_stock > 0)->count(),
            'low'          => $items->filter(fn($i) => $i->status === 'low')->count(),
            'critical'     => $items->filter(fn($i) => $i->status === 'critical')->count(),
            'out_of_stock' => $items->filter(fn($i) => $i->current_stock <= 0)->count(),
        ];

        return view('pages.consumables', compact('items', 'stats'));
    }

    public function show(Consumable $consumable)
    {
        $consumable->load('category');
        return response()->json(['item' => $consumable]);
    }
}