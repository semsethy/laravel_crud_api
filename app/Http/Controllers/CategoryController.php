<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all()->map(function($category){
            $category->image = $category->image ? asset('storage/' . $category->image) : null;
            return $category;
        });
        return response()->json([
            "status" => true,
            "categories" => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required'
        ]);
        $data["status"] = $request->status;
        
    
        // Handle the image file upload
        if ($request->hasFile('image')) {
            // Store the image in the 'public/images' directory and get its path
            $data["image"] = $request->file('image')->store('category', 'public');
        }
    
        // Create a new category with the validated data
        $category = Category::create($data);
    
        return response()->json([
            "status" => true,
            "message" => "Category created successfully"
        ]);
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'title' => 'required'
        ]);
        $data["status"] = $request->status ?? $category->status;
        
        if($request->hasFile("image")){
            if($category->image){
                Storage::disk("public")->delete($category->image);
            }
            $data["image"] = $request->file("image")->store("category","public");
        }


        // Update the category with new data
        $category->update($data);

        return response()->json([
            "status" => true,
            "message" => "Category updated successfully"
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Delete the category
        if($category->image){
            Storage::disk("public")->delete($category->image);
        }
        $category->delete();

        return response()->json([
            "status" => true,
            "message" => "Category deleted successfully"
        ]);
    }

}
