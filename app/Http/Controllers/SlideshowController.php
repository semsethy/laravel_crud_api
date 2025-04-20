<?php

namespace App\Http\Controllers;

use App\Models\Slideshow;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class SlideshowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 5); // Default to 5 slideshow per page if not provided
        $slideshow = Slideshow::with('category')->paginate($perPage); // Use dynamic pagination per page value
    
        return response()->json([
            "status" => true,
            "slideshows" => $slideshow->items(),
            "total_pages" => $slideshow->lastPage(),
            "current_page" => $slideshow->currentPage(),
            "total_slideshows" => $slideshow->total() // Include total number of slideshow
        ]);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate( [
            "title" => "required"
        ]);
        $data["description"] = $request->description;
        $data["caption"] = $request->caption;
        $data["link"] = $request->link;
        $data["status"] = $request->status;
        $data["category_id"] = $request->category_id;

        // Handle main image upload
        if ($request->hasFile('image')) {
            $data["image"] = $request->file('image')->store('slideshows', 'public');
        } 

        // Create a new Slideshow with the validated data
        $slideshow = Slideshow::create($data);

        return response()->json([
            "status" => true,
            "message" => "Slideshow created successfully"
        ]);
    }

   /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Fetch product with its related category
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Transform the main image URL
        $product->main_image_url = $product->main_image_url ? asset('storage/' . $product->main_image_url) : null;

        // Handle the collection_image_url transformation
        if ($product->collection_image_url) {
            // Decode the JSON collection images to an array
            $collectionImages = json_decode($product->collection_image_url, true);

            // Transform each collection image URL to a fully qualified URL
            $transformedImages = array_map(function ($image) {
                return asset('storage/' . $image);
            }, $collectionImages);

            // Assign the transformed collection image URLs back to the product
            $product->collection_image_url = $transformedImages;
        }

        // Return the product with the transformed URLs
        return response()->json($product);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Slideshow $slideshow)
    {   
        $data = $request->validate([
            'title' => 'required'
        ]);
        $data["description"] = $request->description ?? $slideshow->description;
        $data["caption"] = $request->caption ?? $slideshow->caption;
        $data["link"] = $request->link ?? $slideshow->link;
        $data["status"] = $request->status ?? $slideshow->status;
        $data["category_id"] = $request->category_id ?? $slideshow->category_id;

        // Handle main image upload (if new image is provided)
        if ($request->hasFile('image')) {
            if ($slideshow->image) {
                // Delete old image if a new one is uploaded
                Storage::disk("public")->delete($slideshow->image);
            }
            $data["image"] = $request->file('image')->store('slideshows', 'public');
        }



        // Update the slideshow with the new data
        $slideshow->update($data);

        return response()->json([
            "status" => true,
            "message" => "Slideshow updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Slideshow $slideshow)
    {
        // Delete associated images from storage if needed
        if ($slideshow->image) {
            Storage::disk('public')->delete($slideshow->image);
        }
    
        // Delete the slideshow from the database
        $slideshow->delete();
    
        return response()->json([
            "status" => true,
            "message" => "slideshow deleted successfully"
        ]);
    }

    public function getActiveSlideshows()
    {
        $slideshows = Slideshow::with('category')
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($slideshows);
    }
    
}


