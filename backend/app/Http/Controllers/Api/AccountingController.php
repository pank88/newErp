<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    public function index() { return Transaction::all(); }
    public function store(Request $request) {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required',
            'description' => 'required',
            'amount' => 'required|numeric',
        ]);
        $txn = Transaction::create($validated);
        return response()->json($txn, 201);
    }
    public function show($id) { return Transaction::findOrFail($id); }
    public function update(Request $request, $id) {
        $txn = Transaction::findOrFail($id);
        $txn->update($request->all());
        return response()->json($txn, 200);
    }
    public function destroy($id) {
        Transaction::destroy($id);
        return response()->json(null, 204);
    }
}
