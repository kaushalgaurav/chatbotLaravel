<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TemplateController;

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
Route::post('chatbots/store', [ChatbotController::class, 'store'])->name('chatbots.store');
Route::get('chatbots/{chatbot}/build', [ChatbotController::class, 'buildChatbot'])->name('chatbots.build');
Route::put('chatbots/{id}', [ChatbotController::class, 'update'])->name('chatbots.update');
Route::get('chatbots/{chatbot}/design', [ChatbotController::class, 'designChatbot'])->name('chatbots.design');
Route::get('chatbots/{chatbot}/settings', [ChatbotController::class, 'settingChatbot'])->name('chatbots.settings');
Route::get('chatbots/{chatbot}/share', [ChatbotController::class, 'shareChatbot'])->name('chatbots.share');
Route::get('chatbots/{chatbot}/analyze', [ChatbotController::class, 'analyzeChatbot'])->name('chatbots.analyze');
Route::post('/msme/upload-products', [ChatbotController::class, 'uploadProducts'])->name('msme.upload-products');
Route::get('/msme/upload-status/{upload_uuid}', [ChatbotController::class, 'uploadStatus'])->name('msme.upload-status');
Route::get('/msme/download-dummy', [ChatbotController::class, 'downloadDummyFile'])->name('msme.download-dummy');

Route::resource('chatbots', ChatbotController::class);


// for preview test.html
Route::view('/bot/preview/{id}', 'chatbots.preview')->name('bot.preview');
Route::view('/bot/preview/{id}/{any?}', 'chatbots.preview')->where('any', '.*');
Route::post('/chatbot/publish', [ChatbotController::class, 'publish'])->name('chatbot.publish');
Route::get('/chatbot/history/{bot_id}', [ChatbotController::class, 'history'])->name('chatbot.history');
Route::get('/publish-chatbot/{bot_id}', [ChatbotController::class, 'getPublishedChatbot'])->name('publish.chatbot');

Route::prefix('templates')->group(function () {
    Route::get('/', [TemplateController::class, 'index'])->name('templates.index');
    // Route::post('/', [TemplateController::class, 'store']);
    Route::post('/{template}/copy', [TemplateController::class, 'copyToChatbot'])->name('templates.copy');
});
