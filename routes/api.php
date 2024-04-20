<?php

use App\Http\Controllers\ScrapController;
use Illuminate\Support\Facades\Route;


Route::get('scraps', [ScrapController::class, 'scraps']);
