<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\HomeController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['verify' => true]);

Route::get('/', [HomeController::class, 'root'])->name('root')->middleware('auth');

// customers route
Route::get('/customers', [App\Http\Controllers\CustomerController::class, 'index'])->name('customers.list');

//Update User Details
Route::post('/update-profile/{id}', [HomeController::class, 'updateProfile'])->name('updateProfile');
Route::post('/update-password/{id}', [HomeController::class, 'updatePassword'])->name('updatePassword');

Route::get('/dashboard/index', [HomeController::class, 'index'])->name('dashboard.index');
Route::get('/workspace/index', [HomeController::class, 'workspace'])->name('workspace.index');


//Language Translation
Route::get('index/{locale}', [HomeController::class, 'lang']);

Route::get('/chatbots/details/{id}', [ChatbotController::class, 'details'])->name('chatbots.details');

Route::get('chatbots/list', [ChatbotController::class, 'getChatbotList'])->name('chatbots.list');
Route::get('chatbots/{chatbot}/build', [ChatbotController::class, 'buildChatbot'])->name('chatbots.build');
Route::put('chatbots/{id}', [ChatbotController::class, 'update'])->name('chatbots.update');

Route::resource('chatbots', ChatbotController::class);
