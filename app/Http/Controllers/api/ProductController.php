<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Product;
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
        //$product = Product::paginate(2);
        // Product::withTrashed()
        // ->restore();
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

}
