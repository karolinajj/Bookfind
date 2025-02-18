<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\View\View;

use App\Models\UserLbaw;

class RegisterLbawController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'username' => 'required|string|max:250|unique:user,username',
            'email' => 'required|email|max:250|unique:user,email',
            'password' => 'required|min:8'
        ]);

        $user = UserLbaw::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 1, // default status = active
        ]);
        auth()->login($user);
        return redirect('/home');
    }
}