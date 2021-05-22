<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Comment;
use App\Models\Post;
use Validator;
use App\Providers\AppServiceProvider;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        try {
            $time_start = microtime(true);
            $input = $request->all();
            $rules = array(
                'body' => 'required',
                'post_id' => 'required|numeric'
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return response()->errorResponse(422, $validator->errors()->first(), $time_start);
            }
            $input['user_id'] = auth()->user()->id;
            $response = Comment::create($input);
            return  response()->succeedResponse(200, 'POST', $response, $time_start);
        } catch(\Exception $e) {
            return response()->errorResponse(403, $e->getMessage(), $time_start);
        }
    }
}