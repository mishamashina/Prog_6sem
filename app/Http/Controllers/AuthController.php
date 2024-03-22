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
        // $response = [
        //     'name'=>$request->name,
        //     'email'=>$request->email,
        //     'password'=>$request->password,
        // ];
        //return response()->json($response);
        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
        ]);
        $user->createToken('MyAppTokens');
        return redirect()->route('login');
    }
    public function login()
    {
        //var_dump($articles);
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
            $request->session()->regenerate();
            return redirect()->intended('/article');
        }
        return back()->withErrors([
            'email'=>'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
