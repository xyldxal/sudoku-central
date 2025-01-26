from grid_generator import GridGenerator
import random
import copy

class GameGenerator:
    def __init__(self):
        self.grid_generator = GridGenerator()

    def generate(self, difficulty):
        #generate full grid
        grid = self.grid_generator.generate()

        #remove cells
        puzzle = copy.deepcopy(grid)

        cells_removed = {
            'easy': 30,
            'medium': 40,
            'hard': 50,
            'expert': 60
        }

        count = cells_removed[difficulty]

        while count > 0:
            row = random.randint(0, 8)
            col = random.randint(0, 8)

            if puzzle[row][col] != 0:
                puzzle[row][col] = 0
                count -= 1

        return {
            'puzzle': puzzle,
            'solution': grid
        }