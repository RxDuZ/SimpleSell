<?php

namespace rxduz\simplesell\database;

use InvalidArgumentException;
use pocketmine\player\Player;
use pocketmine\promise\Promise;
use pocketmine\promise\PromiseResolver;
use pocketmine\utils\SingletonTrait;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;
use RuntimeException;
use rxduz\simplesell\Main;
use rxduz\simplesell\object\LocalProfile;
use Symfony\Component\Filesystem\Path;

class DataBase
{
    use SingletonTrait;

    /** @var DataConnector $dataConnector */
    private DataConnector $dataConnector;

    /**
     * Load the database configuration and initialize the data connector.
     * 
     * @param Main $plugin
     */
    public function load(Main $plugin): void
    {
        $dbData = $plugin->getConfig()->get('database', []);

        if (!is_array($dbData) or empty($dbData)) {
            throw new InvalidArgumentException('The database config is invalid.');
        }

        $this->dataConnector = libasynql::create(
            $plugin,
            $dbData,
            [
                'sqlite' => Path::join('database', 'sqlite.sql'),
                'mysql' => Path::join('database', 'mysql.sql')
            ]
        );

        $this->dataConnector->executeGeneric('tables.players');

        $this->dataConnector->waitAll();
    }

    /**
     * Insert or replace the player in the database.
     * 
     * @param Player $player
     * @param bool $autosell
     */
    public function insert(Player $player, bool $autosell = false): void
    {
        $dataConnector = $this->dataConnector;

        if (!$dataConnector instanceof DataConnector) {
            throw new RuntimeException('Data connector is not initialized.');
        }

        $name = $player->getName();

        $dataConnector->executeInsert(
            'request.insert',
            [
                'playerName' => $name,
                'autosell' => $autosell
            ],
            fn() => Main::getInstance()->getLogger()->notice('Update data for ' . $name)
        );
    }

    /**
     * Get a player's database to register them.
     * 
     * @param Player $player
     * @return Promise
     */
    public function get(Player $player): Promise
    {
        $dataConnector = $this->dataConnector;

        if (!$dataConnector instanceof DataConnector) {
            throw new RuntimeException('Data connector is not initialized.');
        }

        $name = $player->getName();

        $promiseResolver = new PromiseResolver();

        $dataConnector->executeSelect(
            'request.get',
            [
                'playerName' => $name
            ],
            function (array $rows) use ($name, $promiseResolver) {
                if (count($rows) === 0) {
                    $promiseResolver->resolve(null);

                    return;
                }

                $promiseResolver->resolve(LocalProfile::read($rows[0]));
            }
        );

        return $promiseResolver->getPromise();
    }

    /**
     * Close the database when shutting down the server
     */
    public function shutdown(): void
    {
        $dataConnector = $this->dataConnector;

        if (!$dataConnector instanceof DataConnector) return;

        $dataConnector->waitAll();

        $dataConnector->close();
    }
}
