<?php

namespace Hive\Tiles;

use Hive\Core\GameBoard;
use Hive\Util;

/**
 * Represents a Spider tile.
 */
class Spider extends Tile
{
    /**
     * The type of the tile (S for Spider).
     *
     * @var TileType
     */
    protected TileType $type = TileType::Spider;

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

        $positions = [];
        $this->dfs($board, $pos, 3, [$pos], $positions);

        $board->addTile($pos, $tile);
        return $positions;
    }

    /**
     * Depth-first search to find valid moves for the tile.
     *
     * @param GameBoard $board The current board state.
     * @param string $pos The current position of the tile.
     * @param int $depth The max depth of the search.
     * @param array $visited The visited positions.
     * @param array $positions The valid positions.
     */
    protected function dfs(GameBoard $board, string $pos, int $depth, array $visited, array &$positions): void
    {
        if ($depth === 0) {
            $positions[] = $pos;
            return;
        }

        foreach (Util::getNeighbours($pos) as $neighbour) {
            if (in_array($neighbour, $visited)) {
                continue;
            }

            if ($board->hasTile($neighbour)) {
                continue;
            }

            if (!Util::hasNeighbour($neighbour, $board)) {
                continue;
            }

            if (!Util::isValidSlide($board, $pos, $neighbour)) {
                continue;
            }

            $visited[] = $neighbour;
            $this->dfs($board, $neighbour, $depth - 1, $visited, $positions);
            array_pop($visited);
        }
    }
}
