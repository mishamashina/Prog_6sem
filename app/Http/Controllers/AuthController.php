<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function signin()
    {
        //var_dump($articles);
        return view('auth.signin');
    }
    public function registr(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'email'=>'required|unique:App\Models\User|email',
            'password'=>'required|min:6'
        ]);

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
        ]);

        $token = $user->createToken('MyAppToken');
        if(request()->expectsJson()) return response()->json($token);
        return redirect()->route('login');
    }
    public function login(Request $request)
    {
        return view('auth.signup');
    }
    public function signup(Request $request)
    {
        //+var_dump($articles);
        $credentials = $request->validate([
            'email'=>'required',
            'password'=>'required|min:6'
        ]);

        if (Auth::attempt($credentials))
        {
            $token = $request->user()->createToken('MyAppToken');
            if(request()->expectsJson()) return response()->json($token);
            $request->session()->regenerate();
            return redirect()->intended('/article');
        }
        return back()->withErrors([
            'email'=>'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
    public function logout(Request $request)
    {
        if(request()->expectsJson()){
            //auth()->user()->tokens()->delete();
            return response()->json('logout');
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
