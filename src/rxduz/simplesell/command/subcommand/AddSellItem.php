<?php

namespace rxduz\simplesell\command\subcommand;

use CortexPE\Commando\args\FloatArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use rxduz\simplesell\registry\SellItemRegistry;

class AddSellItem extends BaseSubCommand
{

    private const ARGUMENT_PRICE = 'price';

    public function prepare(): void
    {
        $this->setPermission('adminsell.command.additem');

        $this->registerArgument(0, new FloatArgument(self::ARGUMENT_PRICE));

        $this->addConstraint(new InGameRequiredConstraint($this));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        assert($sender instanceof Player);

        $item = $sender->getInventory()->getItemInHand();

        /** @var float $price */
        $price = $args[self::ARGUMENT_PRICE];

        if ($item->isNull()) {
            $sender->sendMessage(TextFormat::RED . 'Please, use a valid item.');

            return;
        }

        $itemName = $item->getName();

        if (SellItemRegistry::getInstance()->exists($itemName)) {
            $sender->sendMessage(TextFormat::RED . 'This item already exists.');

            return;
        }

        $data = SellItemRegistry::DEFAULT_DATA;
        $id = StringToItemParser::getInstance()->lookupAliases($item)[0] ?? 'air';

        $data['id'] = $id;
        $data['price'] = $price;

        SellItemRegistry::getInstance()->create($itemName, $data);
        $sender->sendMessage(TextFormat::GREEN . 'Register new ' . TextFormat::YELLOW . $itemName . TextFormat::GREEN . ' Item to sell for ' . TextFormat::YELLOW . '$' . number_format($price) . TextFormat::GREEN . '.');
    }
}
