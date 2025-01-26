<!DOCTYPE html>
<html>
<head>
    <title>Sudoku Central</title>
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

        .menu {
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: center;
            margin-top: 40px;
        }

        .btn {
            padding: 15px 30px;
            font-size: 18px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.1s, background-color 0.3s;
            width: 200px;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        .btn:active {
            transform: scale(0.95);
        }

        .btn.generate {
            background-color: #2196F3;
        }

        .btn.generate:hover {
            background-color: #1976D2;
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
        }

        .cell:nth-child(3n) {
            border-right: 2px solid #333;
        }

        .grid > div:nth-child(27n) {
            border-bottom: 2px solid #333;
        }

        .description {
            margin: 20px auto;
            max-width: 600px;
            color: #666;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="title">Sudoku Central</h1>
        
        <p class="description">
            A sudoku central for all things sudoku-related.
        </p>

        <div class="menu">
            <a href="{{ route('sudoku.game') }}" class="btn">Start Game</a>
            <button onclick="generateGrid()" class="btn generate">Generate Complete Grid</button>
        </div>

        <div id="grid" class="grid">
            <!-- Grid will be populated by JavaScript -->
        </div>
    </div>

    <script>
        function generateGrid() {
            fetch('/sudoku/generate-complete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    renderGrid(data.grid);
                } else {
                    console.error('Failed to generate grid:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function renderGrid(grid) {
            const gridElement = document.getElementById('grid');
            gridElement.innerHTML = '';

            for (let i = 0; i < 9; i++) {
                for (let j = 0; j < 9; j++) {
                    const cell = document.createElement('div');
                    cell.className = 'cell';
                    cell.textContent = grid[i][j];
                    gridElement.appendChild(cell);
                }
            }
        }

        // Optional: Generate initial grid when page loads
        // generateGrid();
    </script>
</body>
</html>