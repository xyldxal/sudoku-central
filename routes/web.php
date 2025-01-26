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
    try {
        return response()->json([
            'message' => 'Welcome to Sudoku Central',
            'env' => [
                'app_name' => config('app.name'),
                'app_env' => config('app.env'),
                'app_debug' => config('app.debug'),
                'python_path' => env('PYTHON_PATH')
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => explode("\n", $e->getTraceAsString())
        ], 500);
    }
});