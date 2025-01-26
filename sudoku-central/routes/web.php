<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SudokuController;

Route::get('/', [SudokuController::class, 'index']);
Route::post('/sudoku/new-game', [SudokuController::class, 'newGame']);
Route::post('/sudoku/validate-move', [SudokuController::class, 'validateMove']);
Route::post('/sudoku/check-completion', [SudokuController::class, 'checkCompletion']);
Route::post('/sudoku/hint', [SudokuController::class, 'getHint']);
Route::post('/sudoku/generate-complete', [SudokuController::class, 'generateComplete']);
