<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Userinfos;
use App\Models\User;
use Validator;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Userinfos = Userinfos::all();
        return response()->json(["method" => 'GET', "data" => $Userinfos])->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(),[
                'profile_image' => 'required',
                'gender' => 'required'
            ]);
    
            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors()])->setStatusCode(400);
            }

            $user = auth()->user();
            $userinfos = new Userinfos();
            $userinfos->users_id = $user->id;
            $userinfos->users_name = $user->name;
            $userinfos->profile_image = $request->input('profile_image');
            $userinfos->gender = $request->input('gender');
            $userinfos->save();
            Log::channel('daily')->info('Something happened!'.json_encode($request->all()));

            //Log::channel('mydailylogs')->info('User Info Reequest:'.json_encode($request->all()));
            return response()->json(["method" => 'POST', "data" => $userinfos])->setStatusCode(200);
        } catch (\Exception $e) {
            
            return response()->json(["message"=> $e->getMessage()])->setStatusCode(404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $userinfos = Userinfos::find($id);
            if (is_null($userinfos)) {
                return response()->json(["message"=> "Data is not available"])->setStatusCode(404);
            } else {
                $userinfos = Userinfos::find($id)->first();
                return response()->json(["method" => 'GET', "data" => $userinfos])->setStatusCode(200);
            }
        } catch (\Exception $e) {
            return response()->json(["message"=> $e->getMessage()])->setStatusCode(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $userinfos = Userinfos::find($id);
            if (is_null($userinfos)) {
                return response()->json(["message"=> "Data is not available"])->setStatusCode(404);
            } else {
                $userinfos = Userinfos::find($id)->first();
                $userinfos->profile_image = $request->input('profile_image');
                $userinfos->gender = $request->input('gender');
                $userinfos->save();
                return response()->json(["method" => 'PUT', "data" => $userinfos])->setStatusCode(200);
            }
        } catch (\Exception $e) {
            return response()->json(["message"=> $e->getMessage()])->setStatusCode(404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $userinfos = Userinfos::find($id);
            if (is_null($userinfos)) {
                return response()->json(["message"=> "Data is not available"])->setStatusCode(404);
            } else {
                if ($userinfos->delete()) {
                    return response()->json(["method" => 'DELETE', "data" => $userinfos])->setStatusCode(200);
                } 
            }
        } catch (\Exception $e) {
            return response()->json(["message"=> $e->getMessage()])->setStatusCode(404);
        }   
    }
}
