<?php

namespace rxduz\simplesell\command;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use rxduz\simplesell\command\subcommand\AddSellItem;
use rxduz\simplesell\command\subcommand\EditSellItem;
use rxduz\simplesell\command\subcommand\RemoveSellItem;
use rxduz\simplesell\Main;

class AdminSellBaseCommand extends BaseCommand
{

    public function __construct(Main $plugin)
    {
        parent::__construct($plugin, 'adminsell', 'Manage sell Items');
    }

    public function prepare(): void
    {
        $this->setPermission('adminsell.command');

        $this->registerSubCommand(new AddSellItem($this->getOwningPlugin(), 'additem', 'Add Item to sell', ['add']));

        $this->registerSubCommand(new EditSellItem($this->getOwningPlugin(), 'edititem', 'Edit Item to sell', ['edit']));

        $this->registerSubCommand(new RemoveSellItem($this->getOwningPlugin(), 'removeitem', 'Remove Item to sell', ['remove']));

        $this->addConstraint(new InGameRequiredConstraint($this));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $helpMessage = TextFormat::YELLOW . 'SimpleSell admin commands:' . TextFormat::EOL;

        $helpMessage .= TextFormat::GREEN . '/adminsell additem <price> ' . TextFormat::WHITE . 'Add Item to sell' . TextFormat::EOL;

        $helpMessage .= TextFormat::GREEN . '/adminsell edititem ' . TextFormat::WHITE . 'Edit Item to sell' . TextFormat::EOL;

        $helpMessage .= TextFormat::GREEN . '/adminsell removeitem ' . TextFormat::WHITE . 'Remove Item to sell' . TextFormat::EOL;

        $sender->sendMessage($helpMessage);
    }
}
