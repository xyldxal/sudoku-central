<?php

namespace App\Services;

class SudokuService
{
    private $pythonPath;

    public function __construct()
    {
        $this->pythonPath = base_path('python');
    }

    public function generateComplete(): array {
        $command = "python \"{$this->pythonPath}/main.py\" generate_complete";
        \Log::info("Executing command: " . $command);

        $output = shell_exec($command);
        \Log::info("Python output: " . $output);

        $result = json_decode($output, true);

        if(!$result){
            throw new \Exception('Failed to generate complete grid');
        }

        return $result;
    }

    public function generateGame(string $difficulty): array {
        $command = "python \"{$this->pythonPath}/main.py\" generate_game {$difficulty}";
        
        \Log::info("Executing command: " . $command);
        $output = shell_exec($command);
        \Log::info("Python output: " . $output);
        
        $result = json_decode($output, true);
        
        if (!$result) {
            throw new \Exception('Failed to generate game');
        }
        
        return $result;
    }

    public function validateGrid(array $current, array $solution): bool {
        for ($i = 0; $i < 9; $i++){
            for ($j = 0; $j < 9; $j++){
                if($current[$i][$j] != $solution[$i][$j]){
                    return false;
                }
            }
        }
        return true;
    }

    public function solvePuzzle(array $grid): array{
        $gridJson = escapeshellarg(json_encode($grid));
        $command = "python \"{$this->pythonPath}/main.py\" solve {$gridJson}";
        $output = shell_exec($command);
        return json_decode($output, true);
    }
}