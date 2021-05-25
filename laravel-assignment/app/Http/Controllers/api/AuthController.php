<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\AppServiceProvider;
use App\Models\User;
use Validator;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->errorResponse(422, $validator->errors());
        }
        try {
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $responseArray = [];
            $responseArray['token'] = $user->createToken('MyApp')->accessToken;
            $responseArray['name'] = $user->name;
            return  response()->succeedResponse(200, $responseArray);
        } catch (\Exception $e) {
            return response()->errorResponse(403, $e->getMessage());
        } 
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->errorResponse(422, $validator->errors());
        }
        try {
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                $responseArray = [];
                $responseArray['token'] = $user->createToken('MyApp')->accessToken;
                $responseArray['name'] = $user->name;
                return  response()->succeedResponse(200, $responseArray);        
            } else {
                return response()->errorResponse(403, 'Wrong details');
            }
        } catch (\Exception $e) {
            return response()->errorResponse(403, $e->getMessage());
        } 
    }
}
