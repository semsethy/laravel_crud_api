<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $settings = Setting::all()->map(function($setting){
            $setting->logo = $setting->logo ? asset('storage/' . $setting->logo) : null;
            $setting->icon = $setting->icon ? asset('storage/' . $setting->icon) : null;
            return $setting;
        });
        return response()->json([
            "status" => true,
            "settings" => $settings
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
        $data["email"] = $request->email;
        $data["phone_number"] = $request->phone_number;
        $data["facebook_link"] = $request->facebook_link;
        $data["instagram_link"] = $request->instagram_link;
        $data["twitter_link"] = $request->twitter_link;

        // Handle main image upload
        if ($request->hasFile('icon')) {
            $data["icon"] = $request->file('icon')->store('settings', 'public');
        } 
        if ($request->hasFile('logo')) {
            $data["logo"] = $request->file('logo')->store('settings', 'public');
        } 

        // Create a new setting with the validated data
        $slideshow = Setting::create($data);

        return response()->json([
            "status" => true,
            "message" => "Setting created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Fetch the setting by its ID
        $setting = Setting::find($id);

        // If setting not found, return a 404 error with a message
        if (!$setting) {
            return response()->json(['message' => 'Setting not found'], 404);
        }

        // Transform the file URLs to be accessible via the 'storage' URL
        $setting->logo = $setting->logo ? asset('storage/' . $setting->logo) : null;
        $setting->icon = $setting->icon ? asset('storage/' . $setting->icon) : null;

        // Return the setting data with transformed URLs
        return response()->json($setting);
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Setting $setting)
    {   
        $data = $request->validate([
            'title' => 'required'
        ]);
        $data["description"] = $request->description ?? $setting->description;
        $data["caption"] = $request->caption ?? $setting->caption;
        $data["link"] = $request->link ?? $setting->link;
        $data["status"] = $request->status ?? $setting->status;
        $data["category_id"] = $request->category_id ?? $setting->category_id;

        // Handle main image upload (if new image is provided)
        if ($request->hasFile('logo')) {
            if ($setting->logo) {
                // Delete old image if a new one is uploaded
                Storage::disk("public")->delete($setting->logo);
            }
            $data["logo"] = $request->file('logo')->store('settings', 'public');
        }



        // Update the setting with the new data
        $setting->update($data);

        return response()->json([
            "status" => true,
            "message" => "setting updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting)
    {
        // Delete associated images from storage if needed
        if ($setting->image) {
            Storage::disk('public')->delete($setting->image);
        }
    
        // Delete the slideshow from the database
        $setting->delete();
    
        return response()->json([
            "status" => true,
            "message" => "slideshow deleted successfully"
        ]);
    }
    
}


