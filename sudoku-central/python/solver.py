
class Solver:
    @staticmethod
    def solve(grid):
        solver = Solver()
        if solver._solve_grid(grid):
            return grid
        return None
    
    def _solve_grid(self, grid):
        empty = self._find_empty(grid)
        if not empty:
            return True
        
        row, col = empty

        for num in range(1, 10):
            if self._is_valid(grid, row, col, num):
                grid[row][col] = num
                
                if self._solve_grid(grid):
                    return True
                
                grid[row][col] = 0

        return False
    
    def _find_empty(self, grid):
        for i in range(9):
            for j in range(9):
                if grid[i][j] == 0:
                    return (i, j)
        return None
    
    def _is_valid(self, grid, row, col, num):
        # checks row
        for i in range(9):
            if grid[row][i] == num:
                return False
        
        # checks column
        for i in range(9):
            if grid[i][col] == num:
                return False
        
        # checks 3x3 box
        start_row = row - row % 3
        start_col = col - col % 3
        for i in range(3):
            for j in range(3):
                if grid[i + start_row][j + start_col] == num:
                    return False
        
        return True