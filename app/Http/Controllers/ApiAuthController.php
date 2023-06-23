<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends Controller
{
    public function register(Request $request){

        $request->validate([
            "name"=>"required|min:2",
            "email"=>"required|email|unique:users",
            "password"=>"required|min:8|confirmed"
        ]);

        $user = User::create([
            "name"=>$request->name,
            "email"=>$request->email,
            "password"=>Hash::make($request->password)
        ]);

        return response()->json([
            "message"=>"User Created",
            "success"=>true
        ],200);
    }

    public function login(Request $request){

        $request->validate([
            "email"=>"required|email",
            "password"=>"required|min:8"
        ]);

        if(Auth::attempt($request->only(['email','password']))){
            $token = Auth::user()->createToken("phone")->plainTextToken;

            return response()->json([
                "message"=>"Login Successful",
                "success"=>true,
                "token"=>$token,
                "auth" => new UserResource(Auth::user())
            ]);
        }

        return response()->json(["message"=>"User not found","success"=>false],401);
    }

    public function logout(){
        Auth::user()->currentAccessToken()->delete();

        return response()->json(["message"=>"logout success"]);
    }

    public function logoutAll(){
        Auth::user()->tokens()->delete();

        return response()->json([
            "message"=>"logout all session",
            "success"=>true
        ]);
    }
}
