<?php

namespace Hive;

// game state
class Game {
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
    public array $board = [];

    // current tiles in hand for both players
    // contains an associative array for each player which maps tile types,
    // given as a single character, to the number of that type of tile the
    // player has in hand
    // valid tile types are Q for queen bee, B for beetle, S for spider,
    // A for soldier ant and G for grasshopper
    public array $hand = [
        0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3],
        1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]
    ];

    // current player; 0 for white, 1 for black
    public int $player = 0;

    // store the current state as a string
    public function __toString(): string {
        return json_encode([$this->board, $this->hand, $this->player]);
    }

    // load a state from a string
    public static function fromString(string $serialized): self {
        $self = new self();
        [$self->board, $self->hand, $self->player] = json_decode($serialized, true);
        return $self;
    }
}
