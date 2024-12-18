<?php

namespace rxduz\simplesell\registry;

use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use rxduz\simplesell\Main;
use rxduz\simplesell\object\SellItem;
use rxduz\simplesell\translation\Translation;

class SellItemRegistry
{

    use SingletonTrait;

    /** @var array */
    public const DEFAULT_DATA = [
        'id' => '',
        'price' => 0
    ];

    /** @var Config $data */
    private Config $data;

    /** @var array<string, SellItem> */
    private array $items = [];

    /**
     * Load all Items to sell.
     */
    public function load(): void
    {
        $this->data = new Config(Main::getInstance()->getDataFolder() . '/sellitems.yml', Config::YAML);

        foreach ($this->data->getAll() as $k => $v) {
            if (!is_string($k)) {
                continue;
            }

            if (!is_array($v)) {
                continue;
            }

            $this->items[strtolower($k)] = new SellItem($k, $v['id'], $v['price']);
        }
    }

    /**
     * @return array<string, SellItem>
     */
    public function getSellItems(): array
    {
        return $this->items;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function exists(string $name): bool
    {
        return $this->get($name) !== null;
    }

    /**
     * @param string $name
     * @return SellItem|null
     */
    public function get(string $name): SellItem|null
    {
        return $this->items[strtolower($name)] ?? null;
    }

    /**
     * @param string $name
     * @param array $data
     */
    public function create(string $name, array $data): void
    {
        $this->save($name, $data);

        $this->items[strtolower($name)] = new SellItem($name, $data['id'], $data['price']);
    }

    /**
     * @param string $name
     */
    public function remove(string $name): void
    {
        $this->data->remove($name);

        $this->data->save();

        unset($this->items[strtolower($name)]);
    }

    /**
     * Save Item data to Config.
     * 
     * @param string $k
     * @param array $v
     */
    public function save(string $k, array $v): void
    {
        $this->data->set($k, $v);

        $this->data->save();
    }

    /**
     * Sell ​item in player hand
     * 
     * @param Player $player
     * @param Item $item
     */
    public function processSellHand(Player $player, Item $item): void
    {
        $itemName = $item->getName();

        $id = StringToItemParser::getInstance()->lookupAliases($item)[0] ?? 'air';

        $sellItem = $this->get($itemName);

        if ($sellItem === null or $sellItem->getId() !== $id) {
            $player->sendMessage(Translation::getInstance()->getMessage('SELL_HAND_ITEM_INVALID'));

            return;
        }

        $price = $sellItem->getPrice();

        $count = $item->getCount();

        $total = $price * $count;

        Main::getInstance()->getEconomyProvider()->giveMoney($player, $total, function (bool $success) use ($player, $item, $count, $itemName, $total): void {
            if ($success) {
                $player->getInventory()->removeItem($item);

                $player->sendMessage(Translation::getInstance()->getMessage(
                    'SELL_HAND_SUCCESS',
                    [
                        '{COUNT}' => strval($count),
                        '{ITEM_NAME}' => $itemName,
                        '{PRICE}' => strval($total)
                    ]
                ));
            }
        });
    }

    /**
     * Sell all inventory of player.
     * 
     * @param Player $player
     */
    public function processSellAll(Player $player): void
    {
        $count = 0;

        $total = 0;

        foreach ($player->getInventory()->getContents() as $item) {
            $itemName = $item->getName();

            $id = StringToItemParser::getInstance()->lookupAliases($item)[0] ?? 'air';

            $sellItem = $this->get($itemName);

            if ($sellItem !== null and $sellItem->getId() === $id) {
                $count += $item->getCount();

                $total += ($item->getCount() * $sellItem->getPrice());

                $player->getInventory()->removeItem($item);
            }
        }

        if ($total === 0) {
            $player->sendMessage(Translation::getInstance()->getMessage('SELL_ALL_EMPTY'));

            return;
        }

        Main::getInstance()->getEconomyProvider()->giveMoney($player, $total, function (bool $success) use ($player, $count, $total): void {
            if ($success) {
                $player->sendMessage(Translation::getInstance()->getMessage(
                    'SELL_ALL_SUCCESS',
                    [
                        '{COUNT}' => strval($count),
                        '{PRICE}' => $total
                    ]
                ));
            }
        });
    }

    /**
     * This function is called by BlockBreakEvent
     * 
     * If $autoCollectItems is true will use the items collected in the inventory
     * 
     * @param Player $player
     * @param bool $autoCollectItems
     * @param Item[] $drops
     */
    public function processAutoSell(Player $player, bool $autoCollectItems = false, array $drops = []): void
    {
        $count = 0;

        $total = 0;

        if ($autoCollectItems) { // Change drops for inventory items
            $drops = $player->getInventory()->getContents();
        }

        foreach ($drops as $item) {
            $itemName = $item->getName();

            $id = StringToItemParser::getInstance()->lookupAliases($item)[0] ?? 'air';

            $sellItem = $this->get($itemName);

            if ($sellItem !== null and $sellItem->getId() === $id) {
                $count += $item->getCount();

                $total += ($item->getCount() * $sellItem->getPrice());

                if ($autoCollectItems) $player->getInventory()->removeItem($item);
            }
        }

        if ($total > 0) {
            Main::getInstance()->getEconomyProvider()->giveMoney($player, $total, function (bool $success) use ($player, $count, $total): void {
                if ($success) {
                    $player->sendTip(Translation::getInstance()->getMessage(
                        'SELL_ALL_SUCCESS',
                        [
                            '{COUNT}' => strval($count),
                            '{PRICE}' => $total
                        ]
                    ));
                }
            });
        }
    }
}
