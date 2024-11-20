<?php

namespace Hive;

/**
 * Represents the current state of the game.
 */
class Game
{
    // TODO: Add proper documentation to the properties.
    // current board state
    // this is an associative array mapping board positions to stacks of tiles
    // an example is ["0,0" => [["A", 0]], "0,1" => [["Q", 0], ["B", 1]]]
    // in this example, there is a single white soldier ant (type "A" and
    // player 0) at position 0,0 and a stack of two tiles at position 0,1
    // which consists of a white queen bee (type "Q" and player 0) and a
    // black beetle (type "B" and player 1) (the top tile, in this case the beetle,
    // is the last element of the array)
    // board positions consist of two integer coordinates Q and R which represent
    // a position in an axial coordinate system (https://www.redblobgames.com/grids/hexagons/)
    public array $board;

    // current tiles in hand for both players
    // contains an associative array for each player which maps tile types,
    // given as a single character, to the number of that type of tile the
    // player has in hand
    // valid tile types are Q for queen bee, B for beetle, S for spider,
    // A for soldier ant and G for grasshopper
    public array $hand;

    /**
     * The current player. `0` for white, `1` for black.
     *
     * @var int
     */
    public int $player = 0;

    /**
     * Initialize the game state.
     */
    public function __construct()
    {
        $this->board = [];
        $this->hand = [
            0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3],
            1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]
        ];
    }

    /**
     * Load a game state from a string.
     *
     * @param string $serialized The serialized game state.
     * @return self The loaded game state.
     */
    public static function fromString(string $serialized): self
    {
        $self = new self();
        [$self->board, $self->hand, $self->player] = json_decode($serialized, true);
        return $self;
    }

    public function getAdjacentPositions(): array
    {
        $to = [];
        foreach (Util::OFFSETS as $qr) {
            foreach (array_keys($this->board) as $pos) {
                [$x, $y] = Util::parsePosition($pos);
                $to[] = ($qr[0] + $x).','.($qr[1] + $y);
            }
        }

        $to = array_unique($to);
        if (!count($to)) {
            $to[] = '0,0';
        }

        return $to;
    }

    /**
     * Get the unique tiles in the player's hand.
     *
     * @param int $player The player whose tiles to get.
     * @return array The tiles in the player's hand.
     */
    public function getPlaceableTiles(int $player): array
    {
        $tiles = [];
        foreach ($this->hand[$player] as $tile => $count) {
            if ($count > 0) {
                $tiles[] = $tile;
            }
        }

        return $tiles;
    }

    /**
     * Get the tiles that can be moved by the player.
     *
     * @param int $player The player whose tiles to get.
     * @return array The positions of the tiles that can be moved.
     */
    public function getMovableTiles(int $player): array
    {
        $from = [];
        foreach (array_keys($this->board) as $pos) {
            $tile = $this->board[$pos][count($this->board[$pos]) - 1];
            if ($tile[0] != $player) {
                continue;
            }

            $from[] = $pos;
        }

        return $from;
    }

    /**
     * Get the valid positions to move a tile to.
     *
     * @param int $player The player to get the valid positions for.
     * @return array The valid positions to move a tile to.
     */
    public function getValidMovePositions(int $player): array
    {
        return $this->getAdjacentPositions(); // TODO: Implement support for moving tiles
    }

    /**
     * Get the valid positions to place a tile.
     *
     * @param int $player The player to get the valid positions for.
     * @return array The valid positions to place a tile.
     */
    public function getValidPlacePositions(int $player): array
    {
        $to = [];
        $hand = $this->hand[$this->player];
        foreach ($this->getAdjacentPositions() as $pos) {
            if (isset($this->board[$pos])) {
                continue;
            }

            if (count($this->board) > 0 && !Util::hasNeighbour($pos, $this->board)) {
                continue;
            }

            if (array_sum($hand) < 11 && !Util::neighboursAreSameColor($player, $pos, $this->board)) {
                continue;
            }

            $to[] = $pos;
        }

        return $to;
    }

    /**
     * Store the game state as a string.
     *
     * @return string The serialized game state.
     */
    public function __toString(): string
    {
        return json_encode([$this->board, $this->hand, $this->player]);
    }
}
