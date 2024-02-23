<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request){
        //validate fields
        // try {
            $attrs = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6|confirmed'
            ]);

            // create users
            $user = User::create([
                'first_name' => $attrs['first_name'],
                'last_name' => $attrs['last_name'],
                'email' => $attrs['email'],
                'password' => bcrypt($attrs['password'])
            ]);

            // return user and token in response
            return response()->json([
                'user' => $user,
                'token' => $user->createToken('secret')->plainTextToken
            ]);

        // }catch(\Throwable $throwable) {
        //     return response()->json(["error"=>$throwable->getMessage()]);
        // }


    }

    public function login(Request $request){
        //validate fields
        $attrs = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);


        // attempt login
        if (!Auth::attempt($attrs)) {
            return response()->json([
                'message' => 'Invalid credentials.'
            ],403);
        }

        // return user and token in response
        return response([
            'message'=>"success",
            'data' => auth()->user(),
            'token'=> auth()->user()->createToken('secret')->plainTextToken
        ],200);

    }

    public function update(Request $request)
    {
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);

        $image = $this->saveImage($request->image, 'profiles');

        auth()->user()->update([
            'name' => $attrs['name'],
            'image' => $image
        ]);

        return response([
            'message' =>'User updated.',
            'user' =>auth()->user()
        ],200);
    }

    // logout out user
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response([
            'message' =>'Logout successfully.'
        ],200);
    }

    // get user defaultAliases
    public function me()
    {
        return response(['user' => auth()->user()],200);

    }
}
