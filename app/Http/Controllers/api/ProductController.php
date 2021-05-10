<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\User;
use Validator;
//use App\Http\Resources\Product as PoductResource;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get records
        $product = Product::all();
        return response()->json(["method" => 'GET', "data" => $product, "status" => 200]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //create a new product
        $product = new Product();
        $product->name = $request->input('name');
        $product->price = $request->input('price');
        $product->save();
        return response()->json(["method" => 'POST', "data" => $product, "status" => 200]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //get specific record
        $product = Product::findOrFail($id);
        return response()->json(["method" => 'GET', "data" => $product, "status" => 200]);
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
        //update a specific product
        $product = Product::findOrFail($id);
        $product->name = $request->input('name');
        $product->price = $request->input('price');
        $product->save();
        return response()->json(["method" => 'PUT', "data" => $product, "status" => 200]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //delete a specific record
        $product = Product::findOrFail($id);
        if ($product->delete()) {
            return response()->json(["method" => 'DELETE', "data" => $product, "status" => 200]);
        }
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',

        ]);

        if ($validator->fails()) {
            return response()->json(["method" => 'POST', "error" => $validator->errors(), "status" => 200]);
        }

        $input = $request->all();
        //print_r($input);
        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);

        $responseArray = [];
        $responseArray['token'] = $user->createToken('MyApp')->accessToken;
        $responseArray['name'] = $user->name;
        return response()->json(["method" => 'POST', "data" => $responseArray, "status" => 200]);
    }

    public function login(Request $request) {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $responseArray = [];
            $responseArray['token'] = $user->createToken('MyApp')->accessToken;
            $responseArray['name'] = $user->name;
            return response()->json(["method" => 'POST', "data" => $responseArray, "status" => 200]);
        
        } else {
            return response()->json(['error' => 'Unauthanticated'],203);
        }
    }

}
