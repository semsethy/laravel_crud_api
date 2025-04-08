<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 5); // Default to 5 products per page if not provided
        $products = Product::with('category')->paginate($perPage); // Use dynamic pagination per page value
    
        return response()->json([
            "status" => true,
            "products" => $products->items(),
            "total_pages" => $products->lastPage(),
            "current_page" => $products->currentPage(),
            "total_products" => $products->total() // Include total number of products
        ]);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate( [
            "product_name" => "required"
        ]);
        $data["description"] = $request->description;
        $data["price"] = $request->price;
        $data["stock_quantity"] = $request->stock_quantity;
        $data["status"] = $request->status;
        $data["category_id"] = $request->category_id;

        // Handle main image upload
        if ($request->hasFile('main_image_url')) {
            $data["main_image_url"] = $request->file('main_image_url')->store('products', 'public');
        } 

        // Handle collection images upload
        if ($request->hasFile('collection_image_url')) {
            $collectionImages = [];
            foreach ($request->file('collection_image_url') as $image) {
                $collectionImages[] = $image->store('products', 'public');
            }
            $data["collection_image_url"] = json_encode($collectionImages);  // Store as JSON if you want to store multiple URLs in a single field.
        }   


        // Create a new product with the validated data
        $product = Product::create($data);

        return response()->json([
            "status" => true,
            "message" => "Product created successfully"
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
    public function update(Request $request, Product $product)
    {   
        $data = $request->validate([
            'product_name' => 'required'
        ]);
        $data["description"] = $request->description ?? $product->description;
        $data["price"] = $request->price ?? $product->price;
        $data["stock_quantity"] = $request->stock_quantity ?? $product->stock_quantity;
        $data["status"] = $request->status ?? $product->status;
        $data["category_id"] = $request->category_id ?? $product->category_id;

        // Handle main image upload (if new image is provided)
        if ($request->hasFile('main_image_url')) {
            if ($product->main_image_url) {
                // Delete old image if a new one is uploaded
                Storage::disk("public")->delete($product->main_image_url);
            }
            $data["main_image_url"] = $request->file('main_image_url')->store('products', 'public');
        }


        // Handle collection images upload (if new images are provided)
        if ($request->hasFile('collection_image_url')) {
            $existingImages = json_decode($product->collection_image_url, true); // Decode existing images
            foreach ($existingImages as $imagePath) {
                Storage::disk('public')->delete($imagePath); // Delete old collection images
            }

            $collectionImages = [];
            foreach ($request->file('collection_image_url') as $image) {
                $collectionImages[] = $image->store('products', 'public');
            }
            $data["collection_image_url"] = json_encode($collectionImages);  // Store new images
        }



        // Update the product with the new data
        $product->update($data);

        return response()->json([
            "status" => true,
            "message" => "Product updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Delete associated images from storage if needed
        if ($product->main_image_url) {
            Storage::disk('public')->delete($product->main_image_url);
        }
    
        // Assuming 'collection_image_url' is an array of image paths
        if ($product->collection_image_url) {
            $collectionImages = json_decode($product->collection_image_url, true);
            foreach ($collectionImages as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }
    
        // Delete the product from the database
        $product->delete();
    
        return response()->json([
            "status" => true,
            "message" => "Product deleted successfully"
        ]);
    }
    
}


