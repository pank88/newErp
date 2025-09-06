<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index() { return InventoryItem::all(); }
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required',
            'sku' => 'required|unique:inventory_items',
            'quantity' => 'required|integer',
            'location' => 'required',
            'cost_price' => 'required|numeric',
            'sale_price' => 'required|numeric',
        ]);
        $item = InventoryItem::create($validated);
        return response()->json($item, 201);
    }
    public function show($id) { return InventoryItem::findOrFail($id); }
    public function update(Request $request, $id) {
        $item = InventoryItem::findOrFail($id);
        $item->update($request->all());
        return response()->json($item, 200);
    }
    public function destroy($id) {
        InventoryItem::destroy($id);
        return response()->json(null, 204);
    }
}
