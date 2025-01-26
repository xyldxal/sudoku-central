<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SudokuService;

class SudokuController extends Controller
{
    private $sudokuService;

    public function __construct(SudokuService $sudokuService)
    {
        $this->sudokuService = $sudokuService;
    }

    // main page
    public function index(){
        return view('index');
    }

    // game page
    public function game(){
        return view('sudoku.game');
    }

    // create a new game
    public function startGame(Request $request)
    {
        try {
            $difficulty = $request->input('difficulty', 'medium');
            $result = $this->sudokuService->generateGame($difficulty);
            
            return response()->json([
                'status' => 'success',
                'puzzle' => $result['puzzle'],
                'solution' => $result['solution'],
                'difficulty' => $result['difficulty']
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Game generation error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate game'
            ], 500);
        }
    }
    
    // check validity of move
    public function validateMove(Request $request)
    {
        try {
            $currentGrid = $request->input('grid');
            $solution = $request->input('solution');
            
            $isComplete = $this->sudokuService->validateGrid($currentGrid, $solution);
            
            return response()->json([
                'status' => 'success',
                'complete' => $isComplete
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed'
            ], 500);
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

            return response()->json([
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
