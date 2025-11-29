<?php

namespace App\Http\Controllers;

use App\Models\Shipping;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function index()
    {
        $shippings = Shipping::paginate(10);
        return view('shippings.index', compact('shippings'));
    }

    public function create()
    {
        return view('shippings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'cost' => 'required|numeric',
        ]);

        Shipping::create($request->all());

        return redirect()->route('shippings.index')->with('success', 'Shipping method created successfully.');
    }

    public function edit(Shipping $shipping)
    {
        return view('shippings.edit', compact('shipping'));
    }

    public function update(Request $request, Shipping $shipping)
    {
        $request->validate([
            'name' => 'required',
            'cost' => 'required|numeric',
        ]);

        $shipping->update($request->all());

        return redirect()->route('shippings.index')->with('success', 'Shipping method updated successfully.');
    }

    public function destroy(Shipping $shipping)
    {
        $shipping->delete();
        return redirect()->route('shippings.index')->with('success', 'Shipping method deleted successfully.');
    }
}
