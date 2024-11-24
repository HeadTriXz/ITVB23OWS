<?php

namespace Hive;

use Hive\Core\GameBoard;

/**
 * A utility class for various helper functions.
 */
class Util
{
    /**
     * The offsets from a position to its six neighbours.
     */
    public const array OFFSETS = [[0, 1], [0, -1], [1, 0], [-1, 0], [-1, 1], [1, -1]];

    /**
     * A utility class for various helper functions.
     */
    private function __construct()
    {
    }

    /**
     * Check whether two positions are neighbours.
     *
     * @param string $a The first position.
     * @param string $b The second position.
     * @return bool Whether the two positions are neighbours.
     */
    public static function isNeighbour(string $a, string $b): bool
    {
        $a = self::parsePosition($a);
        $b = self::parsePosition($b);

        foreach (self::OFFSETS as $qr) {
            $q = $a[0] + $qr[0];
            $r = $a[1] + $qr[1];

            if ($q == $b[0] && $r == $b[1]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the positions of the neighbours of a given position.
     * This does NOT check if the neighbours are on the board.
     *
     * @param string $pos The position to get the neighbours of.
     * @return array The positions of the neighbours of the given position.
     */
    public static function getNeighbours(string $pos): array
    {
        [$q, $r] = self::parsePosition($pos);

        $neighbours = [];
        foreach (self::OFFSETS as $qr) {
            $neighbours[] = ($qr[0] + $q) . ',' . ($qr[1] + $r);
        }

        return $neighbours;
    }

    /**
     * Check whether a position has a neighbour already on the board.
     *
     * @param string $a The position to check.
     * @param GameBoard $board The current board state.
     * @return bool Whether the position has a neighbour already on the board.
     */
    public static function hasNeighbour(string $a, GameBoard $board): bool
    {
        foreach ($board->keys() as $b) {
            if (self::isNeighbour($a, $b)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check whether all neighbours of a position belong to the same player.
     *
     * @param int $player The player to check for.
     * @param string $a The position to check.
     * @param GameBoard $board The current board state.
     * @return bool Whether all neighbours of a position belong to the same player.
     */
    public static function neighboursAreSameColor(int $player, string $a, GameBoard $board): bool
    {
        foreach ($board->toArray() as $b => $stack) {
            if (!$stack) {
                continue;
            }

            $c = $stack[0]->getPlayer();
            if ($c != $player && self::isNeighbour($a, $b)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check whether the hive is currently split.
     *
     * @param GameBoard $board The current board state.
     * @return bool Whether the hive is currently split.
     */
    public static function hasMultipleHives(GameBoard $board): bool
    {
        $all = $board->keys();
        $queue = [array_shift($all)];
        while ($queue) {
            $next = self::parsePosition(array_shift($queue));
            foreach (self::OFFSETS as $qr) {
                [$q, $r] = $qr;
                $q += $next[0];
                $r += $next[1];

                if (in_array("$q,$r", $all)) {
                    $queue[] = "$q,$r";
                    $all = array_diff($all, ["$q,$r"]);
                }
            }
        }
        return !!$all;
    }

    /**
     * Check whether the queen bee must be played.
     *
     * @param array $hand The current player's hand.
     * @return bool Whether the queen bee must be played.
     */
    public static function mustPlayQueen(array $hand): bool
    {
        return array_sum($hand) <= 8 && $hand['Q'];
    }

    /**
     * Parse a position string into an array of two integers.
     *
     * @param string $pos The position as a string.
     * @return int[] The position as an array of two integers.
     */
    public static function parsePosition(string $pos): array
    {
        [$q, $r] = explode(',', $pos);

        return [(int)$q, (int)$r];
    }

    /**
     * Check whether a slide between two positions is valid.
     * This is used by all tiles except the grasshopper.
     *
     * @param GameBoard $board The current board state.
     * @param string $from The position to slide from.
     * @param string $to The position to slide to.
     * @return bool Whether the slide is valid.
     */
    public static function isValidSlide(GameBoard $board, string $from, string $to): bool
    {
        if (!self::hasNeighbour($to, $board)) {
            return false;
        }

        if (!self::isNeighbour($from, $to)) {
            return false;
        }

        // find the two common neighbours of the origin and target tiles
        // there are always two, because the two tiles are neighbours
        $b = self::parsePosition($to);
        $common = [];
        foreach (self::OFFSETS as $qr) {
            $q = $b[0] + $qr[0];
            $r = $b[1] + $qr[1];

            if (self::isNeighbour($from, "$q,$r")) {
                $common[] = "$q,$r";
            }
        }

        // find the stacks at the four positions
        $from = $board->getTiles($from);
        $to = $board->getTiles($to);
        $a = $board->getTiles($common[0]);
        $b = $board->getTiles($common[1]);

        // if none of these four stacks contain tiles, the tile would be disconnected from
        // the hive during the move and the slide would therefore be invalid
        if (!$a && !$b && !$from && !$to) {
            return false;
        }

        // the rules are unclear on when exactly a slide is valid, especially when considering stacked tiles
        // the following equation attempts to clarify which slides are valid
        // essentially, a slide is valid if the highest of the stacks at origin and target are at least as
        // high as the lowest stack at the two common neighbours, because that would allow the moving tile
        // to physically slide to the target location without having to squeeze between two tiles
        return min(count($a), count($b)) <= max(count($from), count($to));
    }
}
