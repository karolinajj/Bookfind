<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\View\View;

use App\Models\UserLbaw;

class LoginLbawController extends Controller
{
    public function logout(){
        auth()->logout();
        return redirect()->intended('/home');
    }

    public function login(Request $request){

        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
        }
        else return redirect('/login_lbaw'); //authentication was not successful

        return redirect()->intended('/home');
    }
}