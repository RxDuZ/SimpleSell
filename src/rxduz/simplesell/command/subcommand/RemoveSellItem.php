<?php

namespace rxduz\simplesell\command\subcommand;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use rxduz\simplesell\utils\FormUtils;

class RemoveSellItem extends BaseSubCommand
{

    public function prepare(): void
    {
        $this->setPermission('adminsell.command.removeitem');

        $this->addConstraint(new InGameRequiredConstraint($this));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        assert($sender instanceof Player);

        FormUtils::sendAdminSellRemoveForm($sender);
    }
}
