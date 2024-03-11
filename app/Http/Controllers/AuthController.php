<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register (Request $request) {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email'=> 'required|string|email|max:255',
            'password' => 'required|string|min:8'
        ]);
        $user = User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password' => Hash::make($request->password)
        ]);
        $token = $user->createToken('authToken')->plainTextToken;
        return response()->json(['message' => 'Usuario registrado', 'token' => $token],200);
    }

    public function login (Request $request) {
        $credentials = $request->only('email','password');

        if(Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json(['message'=> 'Login OK', 'token' => $token],200);
        }
        else{
            return response()->json(['message'=> 'Login error'],401);
        }
    }

    public function user(Request $request) {
        $user = $request->user();

        if($user) {
            return response()->json([
                'name' => $user->name,
                'email' => $user->email
            ]);
        }
        else {
            return response()->json(['message'=> 'Usuario no autenticado'],401);
        }
    }

    public function logout(Request $request) {
        Auth::logout();
        return response()->json(['message'=> 'Se ha cerrado sesion']);
    }
}
