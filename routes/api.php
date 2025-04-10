<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/hello', function () {
    return response()->json(['message' => 'bonjour!']);
});

// Authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

// Documents liés aux services

// Routes protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'getUserNotifications']);
    Route::patch('/notifications/read', [NotificationController::class, 'allRead']);
    Route::patch('/notifications/read/{id}', [NotificationController::class, 'read']);

    // Gestion des documents
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);
    Route::get('/documents/{id}', [DocumentController::class, 'getDocument']);
    Route::get('/documents/download/{id}', [DocumentController::class, 'download']);

    // Get Status
    Route::get('/status/{serviceId}', [DocumentController::class, 'getStatus']);
    Route::get('/statusId/{id}', [DocumentController::class, 'getStatusById']);

    // Submit Form
    Route::post('/form/{serviceId}', [FormController::class, 'submitForm']);

    // Forms
    Route::get('/forms', [FormController::class, 'getForms']);
    Route::delete('/forms/{id}', [FormController::class, 'destroy']);
    Route::patch('/forms/{id}', [FormController::class, 'update']);
    Route::get('/forms/{id}', [FormController::class, 'get']);

    Route::delete('/forms/document/{id}', [FormController::class, 'documentDelete']);
    Route::get('/statistics', [FormController::class, 'getStatistics']);

    //Users
    Route::get('/users', [UserController::class, 'getUsers']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::put('/users/{id}', [UserController::class, 'update']);

    //Services
    Route::get('/services', [ServiceController::class, 'index']);
    Route::middleware('client')->group(function () {
        Route::get('/services/{id}/documents', [DocumentController::class, 'getDocumentsByService']);
        Route::put('/user/profile', [UserController::class, 'updateProfile']);
        Route::put('/user/profile/matricule', [UserController::class, 'updateProfileMatricule']);
        Route::post('/documents/upload', [DocumentController::class, 'uploadDocument']);
        Route::get('/user/documents/{serviceId}/{id}', [DocumentController::class, 'getUserDocumentsByService']);

    });
    Route::middleware('admin')->group(function () {

    });

});
