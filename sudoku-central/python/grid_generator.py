import random

class GridGenerator:
    def __init__(self):
        self.grid = [[0 for _ in range(9)] for _ in range(9)]

    def generate(self):
        # diagonals
        for i in range(0, 9, 3):
            self.fill_box(i, i)
        
        # remaining cells
        self.solve_grid()
        return self.grid

    def fill_box(self, row, col):
        nums = list(range(1, 10))
        random.shuffle(nums)

        for i in range(3):
            for j in range(3):
                self.grid[row + i][col + j] = nums[i * 3 + j]
    
    def solve_grid(self):
        empty = self.find_empty()
        if not empty:
            return True
        
        row, col = empty

        for num in range(1,10):
            if self.is_valid(row, col, num):
                self.grid[row][col] = num

                if self.solve_grid():
                    return True

                self.grid[row][col] = 0
        
        return False
    
    def find_empty(self):
        for i in range(9):
            for j in range(9):
                if self.grid[i][j] == 0:
                    return (i, j)
        return None
    
    def is_valid(self, row, col, num):
        # checks row
        for i in range(9):
            if self.grid[row][i] == num:
                return False
        
        # checks column
        for i in range(9):
            if self.grid[i][col] == num:
                return False
        
        # checks 3x3 box
        start_row = row - row % 3
        start_col = col - col % 3
        for i in range(3):
            for j in range(3):
                if self.grid[i + start_row][j + start_col] == num:
                    return False
        
        return True