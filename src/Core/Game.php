<?php

namespace Hive\Core;

use Hive\Util;

/**
 * Represents the current state of the game.
 */
class Game
{
    /**
     * The current state of the board.
     *
     * @var GameBoard
     */
    public GameBoard $board;

    /**
     * The current tiles in the hand of both players.
     *
     * @var array
     */
    public array $hand;

    /**
     * The current player. `0` for white, `1` for black.
     *
     * @var int
     */
    public int $player = 0;

    /**
     * The current status of the game.
     *
     * @var GameStatus
     */
    public GameStatus $status = GameStatus::ONGOING;

    /**
     * Initialize the game state.
     */
    public function __construct()
    {
        $this->board = new GameBoard();
        $this->hand = [
            0 => ['Q' => 1, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3],
            1 => ['Q' => 1, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3]
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
        $data = json_decode($serialized, true);

        $self->board->fromArray($data[0]);
        $self->hand = $data[1];
        $self->player = $data[2];
        $self->status = $data[3];

        return $self;
    }

    /**
     * Get the positions adjacent to the current board state.
     *
     * @return array The adjacent positions.
     */
    public function getAdjacentPositions(): array
    {
        $to = [];
        foreach (Util::OFFSETS as $qr) {
            foreach ($this->board->keys() as $pos) {
                [$x, $y] = Util::parsePosition($pos);
                $to[] = ($qr[0] + $x) . ',' . ($qr[1] + $y);
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
        if (Util::mustPlayQueen($this->hand[$player])) {
            return ['Q'];
        }

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
        if ($this->hand[$player]['Q'] > 0) {
            return []; // Queen bee has not been played yet
        }

        $from = [];
        foreach ($this->board->keys() as $pos) {
            $tile = $this->board->getTiles($pos)[0];
            if ($tile->getPlayer() != $player) {
                continue;
            }

            $this->board->removeTile($pos);
            $hasSplit = Util::hasMultipleHives($this->board);
            $this->board->addTile($pos, $tile);

            if ($hasSplit) {
                continue;
            }

            $validMoves = $tile->getValidMoves($this->board, $pos);
            if (count($validMoves) > 0) {
                $from[] = $pos;
            }
        }

        return $from;
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
        $hand = $this->hand[$player];
        foreach ($this->getAdjacentPositions() as $pos) {
            if ($this->board->hasTile($pos)) {
                continue;
            }

            if (!$this->board->isEmpty() && !Util::hasNeighbour($pos, $this->board)) {
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
     * Check whether the game has ended.
     *
     * @return bool Whether the game has ended.
     */
    public function hasEnded(): bool
    {
        return $this->status != GameStatus::ONGOING;
    }

    /**
     * Check whether the player's queen is surrounded.
     *
     * @param int $player The player to check for.
     * @return bool Whether the player's queen is surrounded.
     */
    public function isQueenSurrounded(int $player): bool
    {
        $queenPos = Util::getQueenPosition($this->board, $player);
        if (!$queenPos) {
            return false;
        }

        $neighbours = Util::getNeighbours($queenPos);
        foreach ($neighbours as $pos) {
            if (!$this->board->hasTile($pos)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Update the status of the game.
     * This should be called after every move.
     */
    public function updateStatus(): void
    {
        $whiteSurrounded = $this->isQueenSurrounded(0);
        $blackSurrounded = $this->isQueenSurrounded(1);

        if ($whiteSurrounded && $blackSurrounded) {
            $this->status = GameStatus::DRAW;
        } elseif ($whiteSurrounded) {
            $this->status = GameStatus::BLACK_WINS;
        } elseif ($blackSurrounded) {
            $this->status = GameStatus::WHITE_WINS;
        }
    }

    /**
     * Store the game state as a string.
     *
     * @return string The serialized game state.
     */
    public function __toString(): string
    {
        return json_encode([
            $this->board->toJSON(),
            $this->hand,
            $this->player,
            $this->status
        ]);
    }
}
