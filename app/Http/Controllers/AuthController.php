<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request){
        //validate fields
        $attrs = $request->validate([
            'name' => 'rquired|string',
            'email' => 'required|email|unique|users,email',
            'password' => 'required|min:6|confirmed'
        ]);

        // create users
        $user = User::create([
            'name' => $attrs['name'],
            'email' => $attrs['email'],
            'password' => bcrypt($attrs['password'])
        ]);

        // return user and token in response
        return response([
            'user' => $user,
            'token'=> $user->createToken('secret')->plainTextToken;
        ]);

    }

    public function login(Request $request){
        //validate fields
        $attrs = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        // attempt login
        if (!Auth::attempt($attrs)) {
            return response([
                'message' => 'Invalid credentials.'
            ],403);
        }

        // return user and token in response
        return response([
            'user' => auth()->user(),
            'token'=> auth()->user()->createToken('secret')->plainText();
        ],200);

    }

    // logout out user
    
}
