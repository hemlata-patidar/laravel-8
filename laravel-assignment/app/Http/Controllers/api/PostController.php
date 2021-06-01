<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use App\Providers\AppServiceProvider;
use App\Helpers\Helper;
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
        try {
            $posts = Post::with('comments')->cursorPaginate(2);
            if (is_null($posts)) {
                return response()->errorResponse(404, trans('messages.not_availbale'));
            } else {
                return  response()->succeedResponse(200, $posts);
            }
        } catch(\Exception $e) {
            return response()->errorResponse(403, $e->getMessage());
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
        $input = $request->all();
        $rules = array(
            'image' => 'required|mimes:jpeg,png,jpg',
            'title' => 'required'
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->errorResponse(422, $validator->errors()->first());
        }
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
            emailSend(auth()->user()->email, auth()->user()->name);
            return  response()->succeedResponse(200, $post);
        } catch (\Exception $e) {
            return response()->errorResponse(403, $e->getMessage());
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
            $posts = Post::with('comments')->find($id);
            if (is_null($posts)) {
                return response()->errorResponse(403,trans('messages.not_availbale'));
            } else {
                $post = Post::find($id);
                return  response()->succeedResponse(200, $post);
            }
        } catch (\Exception $e) {
            return response()->errorResponse(403, $e->getMessage());
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
        $input = $request->all();
        $rules = array(
            'image' => 'sometimes|required|mimes:jpeg,png,jpg',
            'title' => 'sometimes|required'
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->errorResponse(422, $validator->errors()->first());
        } 
        try {
            $post = Post::find($id);
            if($post->user_id !== auth()->user()->id) {
                return response()->errorResponse(404, trans('messages.unauthorized_user'));
            }
            if (is_null($post)) {
                return response()->errorResponse(404, trans('messages.not_availbale'));
            } else {
                if ($input == null)  {
                    return response()->errorResponse(422, trans('messages.update_atleast'));
                }
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    if ($file) {
                        $input['image'] = time() . '.' . $file->getClientOriginalExtension();
                    }
                    $destinationPath = public_path('/images/');
                    $file->move($destinationPath, $input['image']);
                    $post->image = $input['image'];
                }
                if ($request->has('title')) {
                    $post->title = $request->input('title');
                }
                $post->update();
                $post->image = url('public/images/' . $post->image);
                return  response()->succeedResponse(200, $post);
            }
        } catch (\Exception $e) {
            return response()->errorResponse(403, $e->getMessage());
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
            $post = Post::find($id);
            if($post->user_id !== auth()->user()->id) {
                return response()->errorResponse(404, trans('messages.unauthorized_user'));
            }
            if (is_null($post)) {
                return response()->errorResponse(404, trans('messages.not_availbale'));
            } else {
                if ($post->delete()) {
                    return  response()->succeedResponse(200, $post);
                } 
            }
        } catch (\Exception $e) {
            return response()->errorResponse(403, $e->getMessage());
        } 
    }

    public function search(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'search' => 'required'
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->errorResponse(422, $validator->errors()->first());
        }
        try {
            $search = $input['search'];
            $searchPosts = Post::where('title', 'LIKE', "%{$search}%")->get();
            //$searchPosts = Post::searchClause('title', $search);
            return  response()->succeedResponse(200, $searchPosts);
        } catch (\Exception $e) {
            return response()->errorResponse(403, $e->getMessage());
        } 
    }

    public function loggedInPosts()
    {
        try {
            $user = auth()->user();
            $logPosts = Post::with('comments')->where('user_id',$user->id)->get();
            if (is_null($logPosts)) {
                return response()->errorResponse(404, trans('messages.not_availbale'));
            } 
            return  response()->succeedResponse(200, $logPosts);
        } catch (\Exception $e) {
            return response()->errorResponse(403, $e->getMessage());
        } 
    }

    public function likePost(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'id' => 'required|numeric'
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->errorResponse(422, $validator->errors()->first());
        }
        try {
            $user = User::find(auth()->user()->id);
            $post = Post::find($request->id);
            $response = $user->toggleLike($post);
            return  response()->succeedResponse(200, $response);
        } catch (\Exception $e) {
            return response()->errorResponse(403, $e->getMessage());
        } 
    }
}
