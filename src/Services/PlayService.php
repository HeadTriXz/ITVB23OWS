<?php

namespace Hive\Services;

use Hive\Core\Game;
use Hive\Repositories\MoveRepository;
use Hive\Session;
use Hive\Tiles\Tile;
use Hive\Tiles\TileType;

/**
 * Represents the service for playing a tile.
 */
class PlayService
{
    /**
     * Represents the service for playing a tile.
     *
     * @param Session $session The session instance.
     * @param MoveRepository $moves The moves repository.
     */
    public function __construct(protected Session $session, protected MoveRepository $moves)
    {
    }

    /**
     * Plays a tile on the board.
     *
     * @param Game $game The current game state.
     * @param string $piece The piece to play.
     * @param string $to The position to place the piece.
     */
    public function play(Game $game, string $piece, string $to): void
    {
        // Add the new tile to the board.
        $tile = Tile::from(TileType::from($piece), $game->player);
        $game->board->addTile($to, $tile);

        // Remove the tile from the player's hand.
        $game->hand[$game->player][$piece]--;

        // Switch players.
        $game->player = 1 - $game->player;

        // Save the game state.
        $moveId = $this->moves->create('play', $piece, $to);
        $this->session->set('last_move', $moveId);
    }
}
