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
        $command = "python3 {$this->pythonPath}/main.py generate_complete";
        \Log::info("Executing command: " . $command);

        $output = shell_exec($command);
        \Log::info("Python output: " . $output);

        $result = json_decode($output, true);

        if(!result){
            throw new \Exception('Failed to generate complete grid');
        }

        return $result;
    }
    public function generatePuzzle(string $difficulty = 'medium'): array {
        $command = "python3 {$this->pythonPath}/main.py generate {$difficulty}";
        $output = shell_exec($command);
        return json_decode($output, true);
    }

    public function solvePuzzle(array $grid): array{
        $gridJson = escapeshellarg(json_encode($grid));
        $command = "python3 {$this->pythonPath}/main.py solve {$gridJson}";
        $output = shell_exec($command);
        return json_decode($output, true);
    }
}