<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kopling\Contact\Controllers\ContactController;

Route::get('/', [ContactController::class, 'show'])->name('show');
Route::post('/', [ContactController::class, 'send'])->name('send');
