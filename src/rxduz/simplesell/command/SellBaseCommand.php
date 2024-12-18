<?php

namespace rxduz\simplesell\command;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use rxduz\simplesell\database\DataBase;
use rxduz\simplesell\Main;
use rxduz\simplesell\registry\ProfileRegistry;
use rxduz\simplesell\registry\SellItemRegistry;
use rxduz\simplesell\translation\Translation;
use rxduz\simplesell\utils\FormUtils;

class SellBaseCommand extends BaseCommand
{

    private const ARGUMENT_SELL_TYPE = 'sellType';

    public function __construct(private Main $plugin)
    {
        parent::__construct($plugin, 'sell', 'Sell your Items', ['vender']);
    }

    public function prepare(): void
    {
        $this->setPermission('sell.command');

        $this->registerArgument(0, new RawStringArgument(self::ARGUMENT_SELL_TYPE));

        $this->addConstraint(new InGameRequiredConstraint($this));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {

        assert($sender instanceof Player);

        $sellType = $args[self::ARGUMENT_SELL_TYPE];

        switch (strtolower($sellType)) {
            case 'hand':
                $item = $sender->getInventory()->getItemInHand();

                if ($item->isNull()) {
                    $sender->sendMessage(Translation::getInstance()->getMessage('SELL_HAND_ITEM_NULL'));

                    return;
                }

                if (!SellItemRegistry::getInstance()->exists($item->getName())) {
                    $sender->sendMessage(Translation::getInstance()->getMessage('SELL_HAND_ITEM_INVALID'));

                    return;
                }

                FormUtils::sendSellHandConfirmMenu($sender, $item);
                break;
            case 'all':
                FormUtils::sendSellAllConfirmMenu($sender);
                break;
            case 'auto':
                $profile = ProfileRegistry::getInstance()->getProfile($sender->getName());

                if ($profile === null) {
                    $sender->sendMessage(TextFormat::RED . 'Error code 1');
                    return;
                }

                $enabled = $profile->isAutoSell();

                $message = $enabled ? 'AUTO_SELL_DISABLED' : 'AUTO_SELL_ENABLED';

                $profile->setAutoSell(!$enabled);

                DataBase::getInstance()->insert($sender, $profile->isAutoSell());

                $sender->sendMessage(Translation::getInstance()->getMessage($message));
                break;
            case 'help':
            default:
                $sender->sendMessage(TextFormat::RED . 'Usage /' . $aliasUsed . ' <hand|all|auto>');
                break;
        }
    }
}
