<?php

namespace Hive\Core;

/**
 * Represents the status of the game.
 */
enum GameStatus
{
    case ONGOING;
    case WHITE_WINS;
    case BLACK_WINS;
    case DRAW;
}
