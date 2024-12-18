<?php

namespace rxduz\simplesell\object;

use pocketmine\player\Player;
use pocketmine\Server;

class LocalProfile
{

    public function __construct(
        private readonly string $playerName,
        private bool $autosell = false
    ) {}

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->playerName;
    }

    /**
     * @return Player|null
     */
    public function getInstance(): Player|null
    {
        return Server::getInstance()->getPlayerExact($this->playerName);
    }

    /**
     * @param bool $enabled
     */
    public function setAutoSell(bool $enabled): void
    {
        $this->autosell = $enabled;
    }

    /**
     * @return bool
     */
    public function isAutoSell(): bool
    {
        return $this->autosell;
    }

    /**
     * Used to load an existing profile
     * 
     * @param array $row
     * @return LocalProfile
     */
    public static function read(array $row): self
    {
        $name = $row['playerName'];

        $autosell = $row['autosell'] === '1' || $row['autosell'] === 1 || is_bool($row['autosell']) && $row['autosell'];

        return new self($name, $autosell);
    }
}
