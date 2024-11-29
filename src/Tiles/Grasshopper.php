<?php

namespace Hive\Tiles;

use Hive\Core\GameBoard;
use Hive\Util;

/**
 * Represents a Grasshopper tile.
 */
class Grasshopper extends Tile
{
    /**
     * The type of the tile (G for Grasshopper).
     *
     * @var TileType
     */
    protected TileType $type = TileType::Grasshopper;

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
        $positions = [];

        [$q, $r] = Util::parsePosition($pos);
        foreach (Util::OFFSETS as $qr) {
            $tempQ = $q + $qr[0];
            $tempR = $r + $qr[1];

            // Check if there is at least one tile.
            if (!$board->hasTile("$tempQ,$tempR")) {
                continue;
            }

            // Find the next empty position.
            while ($board->hasTile("$tempQ,$tempR")) {
                $tempQ += $qr[0];
                $tempR += $qr[1];
            }

            $positions[] = "$tempQ,$tempR";
        }

        return $positions;
    }
}
