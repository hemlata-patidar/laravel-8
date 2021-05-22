<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use App\Providers\AppServiceProvider;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\User;
use App\Models\Post;
use Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $time_start = microtime(true);
        try {
            $posts = Post::with('comments')->cursorPaginate(2);
            if (is_null($posts)) {
                return response()->errorResponse(404, "Data is not available", $time_start);
            } else {
                return  response()->succeedResponse(200, 'GET', $posts, $time_start);
            }
        } catch(\Exception $e) {
            return response()->errorResponse(403, $e->getMessage(), $time_start);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $time_start = microtime(true);
        try {
            $input = $request->all();
            $rules = array(
                'image' => 'required|mimes:jpeg,png,jpg',
                'title' => 'required'
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return response()->errorResponse(422, $validator->errors()->first(), $time_start);
            } else {
                try {
                    $file = $request->file('image');
                    if ($file) {
                        $input['image'] = time() . '.' . $file->getClientOriginalExtension();
                    }
                    $destinationPath = public_path('/images/');
                    $file->move($destinationPath, $input['image']);
                    $user = auth()->user();
                    $post = new POST();
                    $post->image = $input['image'];
                    $post->title = $input['title'];
                    $post->user_id = $user->id;
                    $post->save();
                    $post->image = url('public/images/' . $post->image);

                    /* Email start sent to the user */
                    Mail::send([], [], function ($message) {
                        $message->to(auth()->user()->email)
                          ->subject('New Post Created')
                          ->setBody('Hi '.auth()->user()->name.', Congratulation!!! You have created a new post successfully.');
                    });
                    return  response()->succeedResponse(200, 'POST', $post, $time_start);
                } catch(\Exception $ex) {
                    if (isset($ex->errorInfo[2])) {
                        $msg = $ex->errorInfo[2];
                    } else {
                        $msg = $ex->getMessage();
                    }
                    return response()->errorResponse(403, $msg, $time_start);
                }
            }
        } catch (\Exception $e) {
            return response()->errorResponse(403, $e->getMessage(), $time_start);
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
        $time_start = microtime(true);
        try {
            $posts = Post::find($id);
            if (is_null($posts)) {
                return response()->errorResponse(403, "Data is not available for this id", $time_start);
            } else {
                $post = Post::find($id)->first();
                return  response()->succeedResponse(200, 'GET', $post, $time_start);
            }
        } catch (\Exception $e) {
            return response()->errorResponse(403, $e->getMessage(), $time_start);
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
        $time_start = microtime(true);
        $input = $request->all();
        try {
            $post = Post::find($id);
            if($post->user_id !== auth()->user()->id) {
                return response()->errorResponse(404, "Sorry!! This post is created by another user", $time_start);
            }
            if (is_null($post)) {
                return response()->errorResponse(404, "Post is not available for this id", $time_start);
            } else {
                if ($input == null)  {
                    return response()->errorResponse(422, "Update atleast one, title or image", $time_start);
                }
                if ($request->hasFile('image')) {
                    $rules = array(
                        'image' => 'required|mimes:jpeg,png,jpg'
                    );
                    $validator = Validator::make($input, $rules);
                    if ($validator->fails()) {
                        return response()->errorResponse(422, $validator->errors()->first(), $time_start);
                    } else {
                        $file = $request->file('image');
                        if ($file) {
                            $input['image'] = time() . '.' . $file->getClientOriginalExtension();
                        }
                        $destinationPath = public_path('/images/');
                        $file->move($destinationPath, $input['image']);
                    }
                    $post->image = $input['image'];
                }
                if ($request->has('title')) {
                    $rules = array(
                        'title' => 'required'
                    );
                    $validator = Validator::make($input, $rules);
                    if ($validator->fails()) {
                        return response()->errorResponse(422, $validator->errors()->first(), $time_start);
                    }
                    $post->title = $request->input('title');
                }
                $post->update();
                $post->image = url('public/images/' . $post->image);
                return  response()->succeedResponse(200, 'POST', $post, $time_start);
            }
        } catch (\Exception $e) {
            return response()->errorResponse(403, $e->getMessage(), $time_start);
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
        $time_start = microtime(true);
        try {
            $post = Post::find($id);
            if($post->user_id !== auth()->user()->id) {
                return response()->errorResponse(404, "Sorry!! This post is created by another user", $time_start);
            }
            if (is_null($post)) {
                return response()->errorResponse(404, "Data is not available", $time_start);
            } else {
                if ($post->delete()) {
                    return  response()->succeedResponse(200, 'DELETE', $post, $time_start);
                } 
            }
        } catch (\Exception $e) {
            return response()->errorResponse(403, $e->getMessage(), $time_start);
        } 
    }

    public function search(Request $request)
    {
        $time_start = microtime(true);
        try {
            $input = $request->all();
            $rules = array(
                'search' => 'required'
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return response()->errorResponse(422, $validator->errors()->first(), $time_start);
            }
            $search = $input['search'];
            $searchPosts = Post::where('title', 'LIKE', "%{$search}%")->get();
            return  response()->succeedResponse(200, 'POST', $searchPosts, $time_start);
        } catch (\Exception $e) {
            return response()->errorResponse(403, $e->getMessage(), $time_start);
        } 
    }

    public function loggedInPosts()
    {
        $time_start = microtime(true);
        try {
            $user = auth()->user();
            $logPosts = Post::with('comments')->where('user_id',$user->id)->get();
            if (is_null($logPosts)) {
                return response()->errorResponse(404, "Post is not available for this id", $time_start);
            } 
            return  response()->succeedResponse(200, 'POST', $logPosts, $time_start);
        } catch (\Exception $e) {
            return response()->errorResponse(403, $e->getMessage(), $time_start);
        } 
    }

    public function likePost(Request $request)
    {
        $time_start = microtime(true);
        try {
            $input = $request->all();
            $rules = array(
                'id' => 'required|numeric'
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return response()->errorResponse(422, $validator->errors()->first(), $time_start);
            }
            $user = User::find(auth()->user()->id);
            $post = Post::find($request->id);
            $response = $user->toggleLike($post);
            return  response()->succeedResponse(200, 'GET', $response, $time_start);
        } catch (\Exception $e) {
            return response()->errorResponse(403, $e->getMessage(), $time_start);
        } 
    }
}
