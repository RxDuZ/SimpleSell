<?php

namespace rxduz\simplesell\registry;

use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use rxduz\simplesell\Main;
use rxduz\simplesell\object\LocalProfile;
use rxduz\simplesell\database\DataBase;

class ProfileRegistry
{

    use SingletonTrait;

    /** @var array<string, LocalProfile> $profiles */
    private array $profiles = [];

    /**
     * @param Player $player
     */
    public function loadProfile(Player $player): void
    {
        DataBase::getInstance()->get($player)->onCompletion(
            function (?LocalProfile $profile) use ($player) {
                if (!$player->isOnline()) return;

                if ($profile === null) {
                    $profile = new LocalProfile(
                        $player->getName()
                    );
                }

                $this->profiles[strtolower($player->getName())] = $profile;

                Main::getInstance()->getLogger()->notice('Load local profile for ' . $profile->getName());
            },
            function () use ($player) {
                if (!$player->isOnline()) return;

                // Kick the player if the profile failed to load.
                $player->kick(TextFormat::RED . 'Failed to load profile.');
            }
        );
    }

    /**
     * @param string $playerName
     * @return LocalProfile|null
     */
    public function getProfile(string $playerName): LocalProfile|null
    {
        return $this->profiles[strtolower($playerName)] ?? null;
    }
}
