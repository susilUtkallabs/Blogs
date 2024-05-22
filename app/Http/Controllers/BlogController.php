<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Validator;

class BlogController extends Controller
{
    public function blogs(Request $request){
        $user = Auth::user();
        $blog_query = Blog::select('*');

        if($request->sortBy && in_array($request->sortBy, ['id', 'created_at'])){
            $sortBy = $request->sortBy;
        }else{
            $sortBy = 'id';
        }

        if($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])){
            $sortOrder = $request->sortOrder;
        }else{
            $sortOrder = 'desc';
        }

        if($user){
            $blogs = $blog_query->where('is_deleted', 0)->orderBy($sortBy, $sortOrder)->paginate(10);
        }else{
            $blogs = $blog_query->where('valid_user', 0)->where('is_deleted', 0)->orderBy($sortBy, $sortOrder)->paginate(10);
        }

        return response()->json($blogs);
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        if($user){
            $blogs = Blog::where('is_deleted', 0)
                         ->where('title', 'LIKE', '%' . $request->title . '%')
                         ->paginate(10);
        }else{
            $blogs = Blog::where('is_deleted', 0)
                         ->where('valid_user', 0)
                         ->where('title', 'LIKE', '%' . $request->title . '%')
                         ->paginate(10);
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

                if ($request->has('id')) {
                    $blog = Blog::find($request->id);
                    if (!$blog) {
                        return response()->json(['message' => 'Blog not found'], 404);
                    }
                    $blog->update([
                        'title' => $request->title,
                        'image' => $imagePath,
                        'description' => $request->description,
                        'creator_name' => $request->creator_name,
                        'creator_image' => $creatorProfileImagePath,
                        'estimated_reading_time' => $request->estimated_reading_time,
                        'valid_user' => $request->valid_user ?? 0
                    ]);
                    return response()->json(['message' => 'Blog updated successfully', 'blog' => $blog], 200);
                }
        
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

    public function deleteblog($blogid){
        $user = Auth::user();
        if($user){
            $blog = Blog::find($blogid);
            if ($blog) {
                $blog->update([
                    'is_deleted' => 1
                ]);
                return response()->json([
                    'message' => 'Blog deleted',
                    'blog' => $blog
                ]);
            }else{
                return response()->json(['message' => 'Blog not found'], 404);
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

    public function addFavorite($id){
        $user = Auth::user();
        
        if($user){
            $blog = Blog::find($id);
            if($blog){
                if(!Favorite::where('user_id', $user->id)->where('blog_id', $blog->id)->first()){
                    $favorite = new Favorite();
                    $favorite->user_id = $user->id;
                    $favorite->blog_id = $blog->id;
                    $favorite->save();
                    $blog->increment('total_favorites');

                    return response()->json([
                        'message' => 'Blog added to favorites'
                    ]);
                }else{
                    return response()->json([
                        'message' => 'Blog already added to favorites'
                    ]);
                }
            }else{
                return response()->json([
                    'message' => 'Blog not found'
                ]);
            }
        }else{
            return response()->json([
                'message' => 'You have to login first'
            ]);
        }
    }

    public function removeFavorite($id){
        $user = Auth::user();
        
        if($user){
            $blog = Blog::find($id);
            if($blog){
                $favorite = Favorite::where('user_id', $user->id)->where('blog_id', $blog->id)->first();
                if($favorite){
                    $favorite->delete();
                    $blog->decrement('total_favorites');

                    return response()->json([
                        'message' => 'Blog removed from favorites'
                    ]);
                }else{
                    return response()->json([
                        'message' => 'Favorite Blog not found'
                    ]);
                }
            }else{
                return response()->json([
                    'message' => 'Blog not found'
                ]);
            }
        }else{
            return response()->json([
                'message' => 'You have to login first'
            ]);
        }
    }

    public function createCategory(Request $request){
        if(Auth::user()){
            $usertype = Auth::user()->is_admin;
            if($usertype == 1){
                $validatedData = Validator::make($request->all(), [
                    'category_name' => 'required|string|max:50',
                ]);
        
                if($validatedData->fails()){
                    return response()->json($validatedData->errors(), 400);
                }

                if ($request->has('id')) {
                    $category = Category::find($request->id);
                    if (!$category) {
                        return response()->json(['message' => 'Category not found'], 404);
                    }
                    $category->update([
                        'category_name' => $request->category_name
                    ]);
                    return response()->json(['message' => 'Category updated successfully', 'blog' => $category], 200);
                }
        
                $category = Category::create([
                    'category_name' => $request->category_name
                ]);
        
                return response()->json([
                    'message' => 'Category created successfully',
                    'category' => $category
                ]);
            }else{
                return response()->json([
                    'message' => 'Only admin can create category'
                ]);
            }
        }else{
            return response()->json([
                'message' => 'You have to login first'
            ]);
        }
    }

    public function addCategoryToBlog(Request $request){
        if(Auth::user()){
            $usertype = Auth::user()->is_admin;
            if($usertype == 1){
                $validatedData = Validator::make($request->all(), [
                    'category_id' => 'required|integer|max:50',
                    'blog_id' => 'required|integer|max:50',
                ]);
        
                if($validatedData->fails()){
                    return response()->json($validatedData->errors(), 400);
                }
                $categoryExist = Category::find($request->category_id);
                if($categoryExist){
                    $blog = Blog::find($request->blog_id);
                    if($blog){
                        $blog->category_id = $request->category_id;
                        $blog->save();
                        return response()->json([
                            'message' => 'Blog Category updated successfully',
                            'category' => $blog
                        ]);
                    }else{
                        return response()->json([
                            'message' => 'Blog not found'
                        ]);
                    }
                }else{
                    return response()->json([
                        'message' => 'Category not found'
                    ]);
                }
            }else{
                return response()->json([
                    'message' => 'Only admin can create category'
                ]);
            }
        }else{
            return response()->json([
                'message' => 'You have to login first'
            ]);
        }
    }
}
