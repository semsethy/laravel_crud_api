<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SlideshowController;
use App\Http\Controllers\SettingController;

Route::post("register", [AuthController::class, "register"]);
Route::post("login", [AuthController::class, "login"]);

// Route::apiResource("categories", CategoryController::class);
Route::apiResource("products", ProductController::class);
// Route::apiResource("slideshows", SlideshowController::class);
// Route::apiResource("settings", SettingController::class);

Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::apiResource("categories", CategoryController::class);
    // Route::apiResource("products", ProductController::class);
    Route::apiResource("slideshows", SlideshowController::class);
    Route::apiResource("settings", SettingController::class);
    Route::post("logout", [AuthController::class, "logout"]);
});


