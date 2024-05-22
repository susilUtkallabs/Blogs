<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Auth;
use Validator;

class BlogController extends Controller
{
    public function blogs(Request $request){
        $user = Auth::user();

        $sort_by = $request->query('sort_by', 'created_at');
        $sort_order = $request->query('sort_order', 'desc');

        if($user){
            $blogs = Blog::orderBy($sort_by, $sort_order)->paginate(10);
        }else{
            $blogs = Blog::where('valid_user', 0)->orderBy($sort_by, $sort_order)->paginate(10);
        }

        return response()->json($blogs);
    }

    public function search(Request $request, $query)
    {
        $user = Auth::user();
        if($user){
            $blogs = Blog::where('title', 'LIKE', '%' . $query . '%')->paginate(10);
        }else{
            $blogs = Blog::where('valid_user', 0)->where('title', 'LIKE', '%' . $query . '%')->paginate(10);
        }
        return response()->json($blogs);
    }

    public function create(Request $request){
        if(Auth::user()){
            $usertype = Auth::user()->is_admin;
            if($usertype == 1){
                $validatedData = Validator::make($request->all(), [
                    'title' => 'required|string|max:255',
                    'description' => 'required|string',
                    'creator_name' => 'required|string|max:255',
                    'creator_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                    'estimated_reading_time' => 'required',
                ]);
        
                if($validatedData->fails()){
                    return response()->json($validatedData->errors(), 400);
                }
        
                $imagePath = $request->file('image') ? $request->file('image')->store('blog_images', 'public') : null;
                $creatorProfileImagePath = $request->file('creator_image') ? $request->file('creator_image')->store('profile_images', 'public') : null;
        
                $blog = Blog::create([
                    'title' => $request->title,
                    'image' => $imagePath,
                    'description' => $request->description,
                    'creator_name' => $request->creator_name,
                    'creator_image' => $creatorProfileImagePath,
                    'estimated_reading_time' => $request->estimated_reading_time,
                    'valid_user' => $request->valid_user ?? 0
                ]);
        
                return response()->json(['blog' => $blog], 201);
            }else{
                return response()->json([
                    'message' => 'Only admin can create blog'
                ]);
            }
        }else{
            return response()->json([
                'message' => 'You have to login first'
            ]);
        }
    }

    public function singleblog($blogid){
        $blog = Blog::find($blogid);
        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }
        return response()->json(['blog' => $blog], 200);
    }
}
