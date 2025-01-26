
import sys
import json
from game_generator import GameGenerator
from grid_generator import GridGenerator
from solver import Solver

def main():
    command = sys.argv[1] if len(sys.argv) > 1 else 'generate_game'

    if command == 'generate_game':
        difficulty = sys.argv[2] if len(sys.argv) > 2 else 'medium'
        game = GameGenerator()
        result = game.generate(difficulty)
        print(json.dumps(result))
        sys.stdout.flush()


    elif command == 'solve':
        grid = json.loads(sys.argv[2])
        solver = Solver()
        solution = solver.solve(grid)
        print(json.dumps({'solution': solution}))

    elif command == 'generate_complete':
        generator = GridGenerator()
        grid = generator.generate()

        if grid:
            print(json.dumps({'grid': grid}))
        else:
            print(json.dumps({'error': 'Failed to generate grid'}))



if __name__ == '__main__':
    main()