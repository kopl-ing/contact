<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kopling\Contact\Controllers\Admin\MessagesController;

Route::middleware('can:kopling-contact::read-messages')->group(function () {
    Route::get('contact', [MessagesController::class, 'index'])->name('contact');
    Route::get('contact/{message}', [MessagesController::class, 'show'])->whereUuid('message')->name('contact.show');
    Route::post('contact/{message}/handled', [MessagesController::class, 'handled'])->whereUuid('message')->name('contact.handled');
    Route::post('contact/{message}/delete', [MessagesController::class, 'destroy'])->whereUuid('message')->name('contact.destroy');
});
