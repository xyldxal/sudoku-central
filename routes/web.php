<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SudokuController;

Route::get('/', [SudokuController::class, 'index'])->name('index');
Route::get('/sudoku/game', [SudokuController::class, 'game'])->name('sudoku.game');



Route::post('/sudoku/start-game', [SudokuController::class, 'startGame']);
Route::post('/sudoku/validate', [SudokuController::class, 'validateMove']);
Route::post('/sudoku/check-completion', [SudokuController::class, 'checkCompletion']);
// Route::post('/sudoku/hint', [SudokuController::class, 'getHint']);
Route::post('/sudoku/generate-complete', [SudokuController::class, 'generateComplete']);

Route::get('/test', function () {
    return response()->json(['status' => 'ok']);
});