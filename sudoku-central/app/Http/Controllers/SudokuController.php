<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Sudoku;

class SudokuController extends Controller
{
    private $sudokuService;

    public function __construct(Sudoku $sudokuService)
    {
        $this->sudokuService = $sudokuService;
    }

    // Main page
    public function index(){
        return view('sudoku.index');
    }

    // Generate a new puzzle
    public function newGame(Request $request){
        try {
            $difficulty = $request->input('difficulty', 'medium');

            if (!in_array($difficulty, ['easy', 'medium', 'hard', 'expert'])) {
                return response()->json([
                    'error' => 'Invalid difficulty level'
                ], 400);
            }

            $result = $this->sudokuService->generatePuzzle($difficulty);

            if (!$result){
                throw new \Exception('Failed to generate puzzle');
            }

            return response()->json([
                'status' => 'success',
                'puzzle' => $result['puzzle'],
                'solution' => $result['solution']
            ]);
        } catch (\Exception $e) {
            \Log::error('Generation error:' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to generate puzzle',
                'message' => $e->getMessage()
            ], 500);

        }
    }

    // validate move
    public function validateMove(Request $request){
        try {
            $validated = $request->validate([
                'row' => 'required|integer|between:0,8',
                'column' => 'required|integer|between:0,8',
                'value' => 'required|integer|between:1,9',
                'solution' => 'required|array'    
            ]);

            $isValid = $validated['solution'][$validated['row']][$validated['column']] === $validated['value'];

            return response()->json([
                'status' => 'success',
                'valid' => $isValid
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Invalid move',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // check if puzzle is solved
    public function checkCompletion(Request $request){
        try {
            $validated = $request->validate([
                'current' => 'required|array',
                'solution' => 'required|array'    
            ]);

            $current = $validated['current'];
            $solution = $validated['solution'];

            $isComplete = true;
            for ($i = 0; $i < 9; $i++){
                for ($j = 0; $j < 9; $j++){
                    if (!isset($current[$i][$j]) || $current[$i][$j] !== $solution[$i][$j]){
                        $isComplete = false;
                        break 2;
                    }
                }
            }

            return response()->json([
                'status' => 'success',
                'complete' => $isComplete
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Wrong solution',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function generateComplete(){
        try{
            $result = $this->sudokuService->generateComplete();

            return response->json([
                'status' => 'success',
                'grid' => $result['grid']
            ]);
        } catch (\Exception $e) {
            \Log::error('Complete grid generation error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to generate complete grid',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /*
     hint
    
    public function getHint(Request $request)
    {
        try {
            $validated = $request->validate([
                'currentState' => 'required|array',
                'solution' => 'required|array'
            ]);

            $currentState = $validated['currentState'];
            $solution = $validated['solution'];

            // Find a random empty or incorrect cell
            $hints = [];
            for ($i = 0; $i < 9; $i++) {
                for ($j = 0; $j < 9; $j++) {
                    if (!isset($currentState[$i][$j]) || 
                        $currentState[$i][$j] !== $solution[$i][$j]) {
                        $hints[] = [
                            'row' => $i,
                            'col' => $j,
                            'value' => $solution[$i][$j]
                        ];
                    }
                }
            }

            if (empty($hints)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Puzzle is already complete!'
                ]);
            }

            // Return a random hint
            $hint = $hints[array_rand($hints)];
            return response()->json([
                'status' => 'success',
                'hint' => $hint
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate hint',
                'message' => $e->getMessage()
            ], 400);
        }
    }
    */
}
