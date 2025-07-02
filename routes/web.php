<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\BlogCategoryController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReviewRatingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SocialSettingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\GeminiController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::group(['middleware'=>'installed'], function(){
Route::group(['middleware'=>'protectedPage'],function(){
    Route::any('/admin',[AdminController::class,'index']);
  Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
    Route::get('admin/dashboard',[AdminController::class,'dashboard']);
    Route::resource('admin/categories',CategoryController::class);
    Route::resource('admin/plans',PlanController::class);
    Route::resource('admin/blogs',BlogController::class);
    Route::resource('admin/b-categories',BlogCategoryController::class);
    Route::resource('admin/booking',BookingController::class);
    Route::resource('admin/location',LocationController::class);
    Route::resource('admin/rating',ReviewRatingController::class);
    Route::resource('admin/users',UserController::class);
    Route::resource('admin/pages',PageController::class);
    Route::resource('admin/comment',CommentsController::class);
    Route::post('admin/page_showIn_header',[PageController::class,'show_in_header']);
    Route::post('admin/page_showIn_footer',[PageController::class,'show_in_footer']);
    Route::resource('admin/social-settings',SocialSettingController::class);

    Route::any('admin/general-settings',[SettingController::class,'general_settings']);
    Route::any('admin/profile-settings',[SettingController::class,'profile_settings']);
    Route::any('admin/banner-settings',[SettingController::class,'banner_settings']);
    Route::post('admin/change-password',[SettingController::class,'change_password']);
    

});


Route::get('/',[HomeController::class,'index']);
Route::get('plan',[HomeController::class,'category']);
Route::get('contact',[HomeController::class,'contact']);
Route::post('contact',[HomeController::class,'contactStore']);
Route::get('/plan/{slug}/checkout',[UserController::class,'checkout']);
Route::get('/plan/{slug}/checkout/confirm',[BookingController::class,'store']);
Route::get('/success',[BookingController::class,'success']);
Route::get('/my_booking',[UserController::class,'booking']);

    Route::get('blogs',[HomeController::class,'blogs']);
    Route::get('blogs/c/{slug}',[HomeController::class,'blogs_categories']);
    Route::get('blogs/{slug}/{text}',[HomeController::class,'blogSinglePage']);
  
    Route::any('/login',[UserController::class,'login']);
    Route::any('signup',[UserController::class,'signup']);
    Route::get('profile',[UserController::class,'profile']);
    Route::post('profile',[UserController::class,'profileUpdate']);
    Route::post('user/change-image',[UserController::class,'update_image']);
    Route::any('logout',[UserController::class,'logout']);

    Route::any('change-password',[UserController::class,'change_password']);
    Route::any('forgot-password',[UserController::class,'forgot_password']);
    Route::post('update-password',[UserController::class,'reset_passwordUpdate']);
    Route::get('reset-password',[UserController::class,'reset_password']);

    Route::post('comment-store',[CommentsController::class,'store']);
    Route::post('review',[ReviewRatingController::class,'store']);
    
Route::get('{text}/{slug}',[HomeController::class,'singlePage']);
Route::get('/{page}',[HomeController::class,'footer_pages']);
 
// Gemini POST handler
Route::post('/ask-gemini', [GeminiController::class, 'ask']);

// AI Chat View Page
Route::get('/ai-chat', function () {
    return view('ai-chat'); // resources/views/chat.blade.php
});

// Footer pages
Route::get('ai-chat/{slug}', [HomeController::class, 'footer_pages'])->name('ai.chat');

// Plan route
Route::get('{text}/{slug}', [HomeController::class, 'singlePage']);

// Most generic, LAST
Route::get('/{slug}', [PageController::class, 'frontend']);
    
});
