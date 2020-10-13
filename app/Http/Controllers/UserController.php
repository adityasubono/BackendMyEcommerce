<?php

namespace App\Http\Controllers;

use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|unique:users',
            'password' => 'required',
            'name' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json(['error' => $validator->errors()->all()], 409);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = encrypt($request->password);
        $user->save();

        return response()->json(['message' => ['Successfully Registered']]);

    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|unique:users',
            'password' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json(['error' => $validator->errors()->all()], 409);
        }

        $user = User::where('email',$request->email)->get()->first();
        $password = decrypt($user->password);
        if($user && $password->password)
        {
            return response()->json(['user' => $user]);
        } else {
            return response()->json(['error' => ['oops! Something Going Wrong']], 409);
        }
    }
}
