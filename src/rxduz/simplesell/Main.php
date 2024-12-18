<?php

/**
 * This plugin has been created with the aim of helping and improving 
 * the experience of all players and server administrators. 
 * Plugin Feature: Its purpose is so that players can sell items 
 * collected either from Mines or Survival worlds.
 * 
 * Author: iRxDuZ
 * Github: https://github.com/RxDuZ
 */

namespace rxduz\simplesell;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\PacketHooker;
use DaPigGuy\libPiggyEconomy\libPiggyEconomy;
use DaPigGuy\libPiggyEconomy\providers\EconomyProvider;
use dktapps\pmforms\BaseForm;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use rxduz\simplesell\command\AdminSellBaseCommand;
use rxduz\simplesell\command\SellBaseCommand;
use rxduz\simplesell\database\DataBase;
use rxduz\simplesell\registry\SellItemRegistry;
use rxduz\simplesell\translation\Translation;

class Main extends PluginBase
{

    use SingletonTrait;

    /** @var EconomyProvider $economyProvider */
    private EconomyProvider $economyProvider;

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    protected function onEnable(): void
    {
        foreach (
            [
                'Commando' => BaseCommand::class,
                'libPiggyEconomy' => libPiggyEconomy::class,
                'pmforms' => BaseForm::class
            ] as $virion => $class
        ) {
            if (!class_exists($class)) {
                $this->getLogger()->error($virion . ' virion not found. Please download the needed virions and try again.');

                $this->getServer()->getPluginManager()->disablePlugin($this);

                return;
            }
        }

        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }

        $this->saveDefaultConfig();

        $this->saveResource('/messages.yml');

        $this->saveResource('/sellitems.yml');

        libPiggyEconomy::init();

        $this->economyProvider = libPiggyEconomy::getProvider($this->getConfig()->get('economy'));

        DataBase::getInstance()->load($this);

        Translation::getInstance()->load();

        SellItemRegistry::getInstance()->load();

        $this->getServer()->getCommandMap()->registerAll('SimpleSell', [
            new SellBaseCommand($this),
            new AdminSellBaseCommand($this)
        ]);

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

        $this->getServer()->getLogger()->info(TextFormat::GREEN . 'Plugin enabled made by iRxDuZ :3');
    }

    protected function onDisable(): void
    {
        DataBase::getInstance()->shutdown();
    }

    /**
     * @return EconomyProvider
     */
    public function getEconomyProvider(): EconomyProvider
    {
        return $this->economyProvider;
    }
}
