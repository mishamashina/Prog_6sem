<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use App\Models\User;

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
            'email'=>'required|email',
            'password'=>'required|min:6'
        ]);
        // $response = [
        //     'name'=>$request->name,
        //     'email'=>$request->email,
        //     'password'=>$request->password,
        // ];
        //return response()->json($response);
        User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
        ]);
        return redirect('/');
    }

}
