<?php

namespace Hive\Tiles;

use Hive\Core\GameBoard;
use Hive\Util;

/**
 * Represents a SoldierAnt tile.
 */
class SoldierAnt extends Tile
{
    /**
     * The type of the tile (A for Soldier**A**nt).
     *
     * @var TileType
     */
    protected TileType $type = TileType::SoldierAnt;

    /**
     * Returns the valid moves for the tile.
     *
     * @param GameBoard $board The current board state.
     * @param string $pos The current position of the tile.
     *
     * @return array The valid moves for the tile.
     */
    public function getValidMoves(GameBoard $board, string $pos): array
    {
        // Temporarily remove the tile from the board to check for valid moves.
        $tile = $board->removeTile($pos);

        $positions = [$pos];
        $this->dfs($board, $pos, $positions);

        // Remove the current position from the list of valid moves.
        array_shift($positions);

        $board->addTile($pos, $tile);
        return $positions;
    }

    /**
     * Depth-first search to find valid moves for the tile.
     *
     * @param GameBoard $board The current board state.
     * @param string $pos The current position of the tile.
     * @param array $positions The valid positions.
     */
    protected function dfs(GameBoard $board, string $pos, array &$positions): void
    {
        foreach (Util::getNeighbours($pos) as $neighbour) {
            if (in_array($neighbour, $positions)) {
                continue;
            }

            if ($board->hasTile($neighbour)) {
                continue;
            }

            if (!Util::isValidSlide($board, $pos, $neighbour)) {
                continue;
            }

            $positions[] = $neighbour;
            $this->dfs($board, $neighbour, $positions);
        }
    }
}
