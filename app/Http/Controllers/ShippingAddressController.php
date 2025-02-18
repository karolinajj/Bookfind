<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

use App\Models\ShippingAddress;
use App\Models\Country;

class ShippingAddressController extends Controller
{
    public function showForm()
    {
        $countries = Country::all();

        return view('address', compact('countries'));
    }

    public function checkAddress(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:50',
            'code' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'country' => 'required|exists:country,id',
        ]);

        $request->session()->put('shipping_address', $validatedData);

        return redirect()->route('payment');
    }
}