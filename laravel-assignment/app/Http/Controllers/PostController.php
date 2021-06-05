<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use DataTables;
use Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Post::orderBy('id', 'DESC')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($data) { 
                    $url= url('images/'.$data->image);
                    return '<img src="'.$url.'" border="0" width="40" class="img-rounded" align="center" />';
                })
                ->addColumn('action', function($row){
                    $btn = '<button type="button" class="btn btn-success btn-sm" id="getEditPostData" data-id="'.$row->id.'">Edit</button>
                    <button type="button" data-id="'.$row->id.'" data-toggle="modal" data-target="#DeletePostModal" class="btn btn-danger btn-sm" id="getDeleteId">Delete</button>';
                    return $btn;
                })
                ->rawColumns(['image','action'])
                ->make(true);
        }

        return view('post.post');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

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
            'image' => 'required',
            'title' => 'required'
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }
        try {
            $file = $request->image;
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $post = new post;
            $post->image = $filename;
            $post->title = $request->title;
            $post->user_id = 1;
            $post->save();
           return response()->json(['success'=>'Post created successfully']);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()]);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Post::find($id);
        $html = '<div class="form-group">
                <label for="Title">Title:</label>
                <input type="text" class="form-control" name="title" id="editTitle" value="'.$data->title.'">
            </div>';
        return response()->json(['html'=>$html]);
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
        $validator = \Validator::make($request->all(), [
            'title' => 'required'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }

        $post = Post::find($id);
        $post->title = $request->input('title');
        $post->update();
        return response()->json(['success'=>'Post updated successfully']);

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
            if ($post->delete()) {
                return response()->json(['success'=>'Post deleted successfully']);
            } 
        } catch (\Exception $e) {
            return response()->errorResponse(403, $e->getMessage());
        } 
    }
}
