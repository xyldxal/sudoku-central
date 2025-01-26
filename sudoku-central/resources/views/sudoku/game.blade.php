<!-- resources/views/sudoku/game.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Sudoku Game</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .container {
            max-width: 800px;
            margin: 40px auto;
            text-align: center;
            font-family: Arial, sans-serif;
        }

        .title {
            font-size: 2.5em;
            color: #333;
            margin-bottom: 30px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(9, 1fr);
            gap: 1px;
            background-color: #333;
            border: 2px solid #333;
            width: 450px;
            margin: 20px auto;
        }

        .cell {
            background-color: white;
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            position: relative;
        }

        .cell input {
            width: 100%;
            height: 100%;
            border: none;
            text-align: center;
            font-size: 20px;
            outline: none;
        }

        .cell input::-webkit-outer-spin-button,
        .cell input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .cell.initial {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .cell:nth-child(3n) {
            border-right: 2px solid #333;
        }

        .grid > div:nth-child(27n) {
            border-bottom: 2px solid #333;
        }

        .controls {
            margin: 20px 0;
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 24px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.1s, background-color 0.3s;
        }

        .btn:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        .btn:active {
            transform: scale(0.95);
        }

        .btn.difficulty {
            background-color: #2196F3;
        }

        .btn.difficulty:hover {
            background-color: #1976D2;
        }

        .btn.difficulty.active {
            background-color: #1565C0;
            transform: scale(1.05);
        }

        .btn.home {
            background-color: #757575;
        }

        .btn.home:hover {
            background-color: #616161;
        }

        #win-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
            z-index: 1000;
            text-align: center;
        }

        #win-modal h2 {
            color: #4CAF50;
            margin-bottom: 20px;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        .game-info {
            margin: 20px 0;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 8px;
            display: inline-block;
        }

        .invalid-input {
            background-color: #ffebee;
        }

        .valid-input {
            background-color: #e8f5e9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="title">Sudoku Game</h1>

        <div class="controls">
            <a href="{{ route('index') }}" class="btn home">Home</a>
            <button onclick="startGame('easy')" class="btn difficulty" data-difficulty="easy">Easy</button>
            <button onclick="startGame('medium')" class="btn difficulty" data-difficulty="medium">Medium</button>
            <button onclick="startGame('hard')" class="btn difficulty" data-difficulty="hard">Hard</button>
            <button onclick="startGame('expert')" class="btn difficulty" data-difficulty="expert">Expert</button>
        </div>

        <div class="game-info">
            Current Difficulty: <span id="current-difficulty">Medium</span>
        </div>

        <div id="grid" class="grid"></div>
    </div>

    <div id="win-modal">
        <h2>🎉 Congratulations! 🎉</h2>
        <p>You've successfully solved the puzzle!</p>
        <button onclick="startNewGame()" class="btn">Play Again</button>
        <button onclick="window.location.href='{{ route('index') }}'" class="btn home">Main Menu</button>
    </div>

    <div class="overlay" id="overlay"></div>

    <script>
        let currentPuzzle = null;
        let currentSolution = null;
        let currentDifficulty = 'medium';

        function startGame(difficulty) {
            // Update active difficulty button
            document.querySelectorAll('.btn.difficulty').forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.difficulty === difficulty) {
                    btn.classList.add('active');
                }
            });

            currentDifficulty = difficulty;
            document.getElementById('current-difficulty').textContent = 
                difficulty.charAt(0).toUpperCase() + difficulty.slice(1);

            fetch('/sudoku/start-game', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ difficulty })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    currentPuzzle = data.puzzle;
                    currentSolution = data.solution;
                    renderGame(data.puzzle);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function renderGame(puzzle) {
            const grid = document.getElementById('grid');
            grid.innerHTML = '';

            for (let i = 0; i < 9; i++) {
                for (let j = 0; j < 9; j++) {
                    const cell = document.createElement('div');
                    cell.className = 'cell';
                    
                    if (puzzle[i][j] === 0) {
                        const input = document.createElement('input');
                        input.type = 'number';
                        input.min = 1;
                        input.max = 9;
                        input.dataset.row = i;
                        input.dataset.col = j;
                        input.addEventListener('input', handleInput);
                        cell.appendChild(input);
                    } else {
                        cell.textContent = puzzle[i][j];
                        cell.classList.add('initial');
                    }
                    
                    grid.appendChild(cell);
                }
            }
        }

        function handleInput(event) {
            const input = event.target;
            const value = parseInt(input.value);
            const row = parseInt(input.dataset.row);
            const col = parseInt(input.dataset.col);

            if (value >= 1 && value <= 9) {
                currentPuzzle[row][col] = value;
                
                // Check if this move is valid
                const isValid = currentSolution[row][col] === value;
                input.classList.remove('valid-input', 'invalid-input');
                input.classList.add(isValid ? 'valid-input' : 'invalid-input');

                validateGrid();
            } else {
                currentPuzzle[row][col] = 0;
                input.classList.remove('valid-input', 'invalid-input');
            }
        }

        function validateGrid() {
            fetch('/sudoku/validate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    grid: currentPuzzle,
                    solution: currentSolution
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && data.complete) {
                    showWinModal();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function showWinModal() {
            document.getElementById('win-modal').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function startNewGame() {
            document.getElementById('win-modal').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
            startGame(currentDifficulty);
        }

        // Start with a medium difficulty game
        startGame('medium');
    </script>
</body>
</html>