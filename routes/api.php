<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/upload', function (Request $request) {

    $rules = array(
        'image' => 'required|image|max:2048'
    );
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
        return response()->json(['message' => $validator->errors()->first()], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $name = time() .'photo.'.$file->getClientOriginalExtension();
        $filePath = 'images/' . $name;
        Storage::disk('s3')->put($filePath, file_get_contents($file));
        Storage::disk('s3')->setVisibility($filePath, 'public');
        return response()->json(['url' => env('AWS_BUCKET_URL').$filePath], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
    return response()->json(['message' => 'Bad Request'], Response::HTTP_BAD_REQUEST);

});



