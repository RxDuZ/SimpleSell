<?php

namespace rxduz\simplesell\translation;

use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use rxduz\simplesell\Main;

class Translation
{

    use SingletonTrait;

    /** @var array $messages */
    private array $messages;

    /** @var Config $data */
    private Config $data;

    public function load()
    {
        $this->data = new Config(Main::getInstance()->getDataFolder() . '/messages.yml', Config::YAML);

        $this->messages = $this->data->getAll();
    }

    /**
     * @param string $key
     * @param array $replace
     * @return string
     */
    public function getMessage(string $key, array $replace = []): string
    {
        $message = $this->messages[$key] ?? 'This message does not exist or was deleted, please update /messages.yml';

        if (!empty($replace)) {
            foreach ($replace as $k => $v) {
                $message = str_replace($k, strval($v), $message);
            }
        }

        return TextFormat::colorize($message);
    }
}
