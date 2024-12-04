<?php

namespace Hive\Core;

use Hive\Tiles\Tile;

/**
 * Represents the current state of the board.
 */
class GameBoard
{
    /**
     * The current board state.
     *
     * @var array
     */
    protected array $board;

    /**
     * Represents the current state of the board.
     */
    public function __construct()
    {
        $this->board = [];
    }

    /**
     * Adds a tile to the board.
     *
     * @param string $pos The position to add the tile to.
     * @param Tile $tile The tile to add.
     */
    public function addTile(string $pos, Tile $tile): void
    {
        if (!$this->hasTile($pos)) {
            $this->board[$pos] = [];
        }

        array_unshift($this->board[$pos], $tile);
    }

    /**
     * Populates the board from an array.
     *
     * @param array $board The board state to populate from.
     */
    public function fromArray(array $board): void
    {
        $this->board = [];
        foreach ($board as $pos => $stack) {
            foreach ($stack as $tile) {
                $this->addTile($pos, Tile::fromArray($tile));
            }
        }
    }

    /**
     * Returns the stack of tiles at a given position.
     *
     * @param string $pos The position to get the tile from.
     * @return array The stack of tiles at the given position.
     */
    public function getTiles(string $pos): array
    {
        return $this->board[$pos] ?? [];
    }

    /**
     * Returns whether the board is empty.
     *
     * @return bool Whether the board is empty.
     */
    public function isEmpty(): bool
    {
        return empty($this->board);
    }

    /**
     * Returns whether a tile exists at a given position.
     *
     * @param string $pos The position to check.
     * @return bool Whether a tile exists at the given position.
     */
    public function hasTile(string $pos): bool
    {
        return isset($this->board[$pos]);
    }

    /**
     * Returns the positions of all tiles on the board.
     *
     * @return array The positions of all tiles on the board.
     */
    public function keys(): array
    {
        return array_keys($this->board);
    }

    /**
     * Removes a tile from the board.
     *
     * @param string $pos The position to remove the tile from.
     * @return ?Tile The tile that was removed, if any.
     */
    public function removeTile(string $pos): ?Tile
    {
        if (!$this->hasTile($pos)) {
            return null;
        }

        $tile = array_shift($this->board[$pos]);
        if (empty($this->board[$pos])) {
            unset($this->board[$pos]);
        }

        return $tile;
    }

    /**
     * Returns the board state as an array.
     *
     * @return array The board state as an array.
     */
    public function toArray(): array
    {
        return $this->board;
    }

    /**
     * Returns the board state as a JSON array.
     *
     * @return array The board state as a JSON array.
     */
    public function toJSON(): array
    {
        $json = [];
        foreach ($this->board as $pos => $stack) {
            $json[$pos] = [];
            foreach ($stack as $tile) {
                $json[$pos][] = $tile->toArray();
            }
        }
        return $json;
    }
}
