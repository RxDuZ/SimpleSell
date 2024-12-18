<?php

namespace rxduz\simplesell;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use rxduz\simplesell\registry\ProfileRegistry;
use rxduz\simplesell\registry\SellItemRegistry;
use rxduz\simplesell\translation\Translation;

class EventListener implements Listener
{

    /**
     * @param PlayerJoinEvent $ev
     * 
     * @priority NORMAL
     */
    public function onJoin(PlayerJoinEvent $ev): void
    {
        ProfileRegistry::getInstance()->loadProfile($ev->getPlayer());
    }

    /**
     * @param BlockBreakEvent $ev
     * 
     * @priority HIGHEST
     */
    public function onBreak(BlockBreakEvent $ev): void
    {
        // Prevent events if they are cancelled.
        if ($ev->isCancelled()) return;

        $player = $ev->getPlayer();

        // Prevent handle event if the player not is online
        if (!$player->isOnline()) return;

        $profile = ProfileRegistry::getInstance()->getProfile($player->getName());

        $autoCollectItems = Main::getInstance()->getConfig()->get('auto_collect_items', true);

        $autoCollectXp = Main::getInstance()->getConfig()->get('auto_collect_xp', true);

        if ($autoCollectXp) {
            $player->getXpManager()->addXp($ev->getXpDropAmount());

            $ev->setXpDropAmount(0);
        }

        if ($autoCollectItems) {
            foreach ($ev->getDrops() as $drop) {
                if (!$player->getInventory()->canAddItem($drop)) {
                    $player->sendTip(Translation::getInstance()->getMessage('INVENTORY_FULL'));

                    break; // only alert the player once
                }

                $player->getInventory()->addItem($drop);

                $ev->setDrops([]);
            }

            if ($profile !== null and $profile->isAutoSell()) SellItemRegistry::getInstance()->processAutoSell($player, true);

            return;
        }

        if ($profile !== null and $profile->isAutoSell()) {
            SellItemRegistry::getInstance()->processAutoSell($player, false, $ev->getDrops());

            $ev->setDrops([]);
        }
    }
}
