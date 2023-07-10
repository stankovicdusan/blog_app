<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blogs = Post::all();

        return response()->json([
            'success' => true,
            'data'    => $blogs,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title'       => 'required',
            'description' => 'required',
            'slug'        => 'unique:posts',
        ]);
 
        $post              = new Post();
        $post->title       = $request->title;
        $post->description = $request->description;
        $post->user_id     = auth()->user()->id;

        $slug = $request->slug;
        if (null === $slug) {
            $slug = strtolower(implode('-', explode(' ', $request->title)));
        }

        $post->slug = $slug;

        try {
            $post->save();

            return response()->json([
                'success' => true,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }  
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== auth()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Only user who published post can update it.'
            ], 400);
        }

        $this->validate($request, [
            'title'       => 'required',
            'description' => 'required',
            'slug'        => 'unique:posts',
        ]);

        $data = [
            'title'       => $request->title,
            'description' => $request->description,
        ];

        if (null !== $request->slug) {
            $data['slug'] = $request->slug;
        }
 
        try {
            $post->update($data);

            return response()->json([
                'success' => true
            ], 204);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        if ($post->user_id !== auth()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Only user who published post can remove it.'
            ], 400);
        }
 
        try {
            $post->delete();

            return response()->json([
                'success' => true,
            ], 204);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
