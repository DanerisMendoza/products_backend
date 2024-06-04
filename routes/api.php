
use Illuminate\Http\Request;
<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;

Route::post('/Login', [UserController::class, 'Login']);
Route::post('/Register', [UserController::class, 'Register']);
Route::middleware('auth:api')->group(function () {
    //USER API
    Route::get('/authenticate', [UserController::class, 'authenticate']);
    Route::post('/InsertUser', [UserController::class, 'InsertUser']);
    Route::delete('/DeleteUser', [UserController::class, 'DeleteUser']);
    Route::get('/GetUsers', [UserController::class, 'GetUsers']);
    Route::get('/GetUserDetails', [UserController::class, 'GetUserDetails']);
    Route::post('/Logout', [UserController::class, 'Logout']);
    //PRODUCT API
    Route::get('/GetProducts', [ProductController::class, 'GetProducts']);
    Route::post('/InsertProduct', [ProductController::class, 'InsertProduct']);
    Route::post('/UpdateProduct', [ProductController::class, 'UpdateProduct']);
    Route::delete('/DeleteProduct', [ProductController::class, 'DeleteProduct']);
});
