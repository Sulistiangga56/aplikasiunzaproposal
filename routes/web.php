<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});
Route::get('/download/proposal/{file}', function ($file) {
    $filePath = storage_path('app/public/' . $file);
    if (file_exists($filePath)) {
        return response()->download($filePath);
    }
    abort(404);
})->name('download.proposal');
