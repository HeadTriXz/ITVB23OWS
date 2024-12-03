<?php

namespace Hive\Tiles;

use Hive\Core\GameBoard;

/**
 * Represents a tile on the board.
 */
abstract class Tile
{
    /**
     * The type of the tile (e.g. "S" for Spider).
     *
     * @var TileType
     */
    protected TileType $type;

    /**
     * Represents a tile on the board.
     *
     * @param int $player The player that owns the tile.
     */
    public function __construct(protected int $player)
    {
    }

    /**
     * Returns the player that owns the tile.
     *
     * @return int The player that owns the tile.
     */
    public function getPlayer(): int
    {
        return $this->player;
    }

    /**
     * Returns the type of the tile (e.g. "S" for Spider).
     *
     * @return TileType The type of the tile.
     */
    public function getType(): TileType
    {
        return $this->type;
    }

    /**
     * Returns the tile as an array.
     *
     * @return array The tile as an array.
     */
    public function toArray(): array
    {
        return [$this->player, $this->type->value];
    }

    /**
     * Creates a new tile.
     *
     * @param TileType $type The type of the tile.
     * @param int $player The player that owns the tile.
     * @return Tile The created tile.
     */
    public static function from(TileType $type, int $player): Tile
    {
        $class = match ($type) {
            TileType::Beetle => Beetle::class,
            TileType::Grasshopper => Grasshopper::class,
            TileType::QueenBee => QueenBee::class,
            TileType::Spider => Spider::class,
            TileType::SoldierAnt => SoldierAnt::class
        };

        return new $class($player);
    }

    /**
     * Creates a new tile from an array.
     *
     * @param array $data The data to create the tile from.
     * @return Tile The created tile.
     */
    public static function fromArray(array $data): Tile
    {
        return self::from(TileType::from($data[1]), $data[0]);
    }

    /**
     * Returns the valid moves for the tile.
     *
     * @param GameBoard $board The current board state.
     * @param string $pos The current position of the tile.
     * @return array The valid moves for the tile.
     */
    abstract public function getValidMoves(GameBoard $board, string $pos): array;
}
