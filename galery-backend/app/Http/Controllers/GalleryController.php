<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $galleries = Gallery::latest()->paginate(10);
        return $galleries;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:255',
            'description' => 'required|max:1000',
            'image_urls' => ['required', 'array', 'min:1'],
            'image_urls.*' => ['url', function ($attribute, $value, $fail) {
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
    
                $extension = pathinfo(parse_url($value, PHP_URL_PATH), PATHINFO_EXTENSION);
    
                if (!in_array($extension, $allowedExtensions)) {
                    $fail('The ' . $attribute . ' must be a valid URL pointing to an image (JPG, JPEG, PNG).');
                }
            }],
        ]);
        // replace this with user_id from request
        $userId = Auth::id(); 
        
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

       $gallery = Gallery::create([
        'name' => $request->input('name'),
        'description' => $request->input('description'),
        'image_urls' => $request->input('image_urls'),
        'user_id' => $userId,
        ]);
       
       return response()->json(['message' => 'Gallery created successfully', 'gallery' => $gallery], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $gallery = Gallery::with('comments')->findOrFail($id);
        return response()->json([
            'gallery' => $gallery,
            'comments' => $gallery->comments,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:255',
            'description' => 'required|max:1000',
            'image_urls' => ['required', 'array', 'min:1'],
            'image_urls.*' => ['url', function ($attribute, $value, $fail) {
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
    
                $extension = pathinfo(parse_url($value, PHP_URL_PATH), PATHINFO_EXTENSION);
    
                if (!in_array($extension, $allowedExtensions)) {
                    $fail('The ' . $attribute . ' must be a valid URL pointing to an image (JPG, JPEG, PNG).');
                }
            }],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $gallery = Gallery::findOrFail($id);
        $userId = Auth::id();
        $gallery->name = $request->input('name');
        $gallery->description = $request->input('description');
        $gallery->image_urls = $request->input('image_urls');
        $gallery->user_id = $userId;
        $gallery->save();

        return $gallery;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $gallery = Gallery::findOrFail($id);
        $gallery->delete();
        return response()->json(null, 204);
    }
}
