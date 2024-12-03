<?php

namespace Hive\Core;

/**
 * Represents the status of the game.
 */
enum GameStatus: string
{
    case ONGOING = 'ongoing';
    case WHITE_WINS = 'white_wins';
    case BLACK_WINS = 'black_wins';
    case DRAW = 'draw';
}
