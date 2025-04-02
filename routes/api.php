<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FormController;

Route::get('/hello', function () {
    return response()->json(['message' => 'bonjour!']);
});

// Authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

// Documents liés aux services
Route::get('/services/{id}/documents', [DocumentController::class, 'getDocumentsByService']);

// Routes protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Mise à jour du profil utilisateur
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::put('/user/profile/matricule', [UserController::class, 'updateProfileMatricule']);

    // Gestion des documents
    Route::post('/documents/upload', [DocumentController::class, 'uploadDocument']);
    Route::get('/user/documents/{serviceId}/{id}', [DocumentController::class, 'getUserDocumentsByService']);
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);
    Route::get('/documents/{id}', [DocumentController::class, 'getDocument']);

    // Get Status
    Route::get('/status/{serviceId}', [DocumentController::class, 'getStatus']);
    Route::get('/statusId/{id}', [DocumentController::class, 'getStatusById']);

    // Submit Form
    Route::post('/form/{serviceId}', [FormController::class, 'submitForm']);

});