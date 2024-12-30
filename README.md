# SimpleSell

**PocketMine plugin that allows you to sell items collected while mining, exploring or playing on your server.**

## Prerequisites

- <a href="https://github.com/poggit/libasynql">libasynql virion</a>
- <a href="https://github.com/CortexPE/Commando">Commando virion</a>
- <a href="https://github.com/DaPigGuy/libPiggyEconomy">libPiggyEconomy virion</a>
- <a href="https://github.com/dktapps-pm-pl/pmforms">pmforms virion</a>
- PMMP 5.23.0+

## Installation & Setup

1. Install the plugin from [Poggit](https://poggit.pmmp.io/ci/RxDuZ/SimpleSell/~).
2. (Optional) Configure `config.yml` auto_collect_items, auto_collect_items and database.
3. Once this is done, turn on the server.
4. With the item in your hand use the command `/adminsell additem <price>` to add Item to sell.
5. Use the command `/adminsell edititem` to edit item price.
6. Use the command `/adminsell removeitem` to remove sell item.

### Implementations

- [x] Collect items thrown to the ground by mining
- [x] Collect experience dropped on the ground by mining
- [x] Automatically sell items when mining

---

### 💾 Config

```yml
#     ▄████████  ▄█    ▄▄▄▄███▄▄▄▄      ▄███████▄  ▄█          ▄████████    ▄████████    ▄████████  ▄█        ▄█
#    ███    ███ ███  ▄██▀▀▀███▀▀▀██▄   ███    ███ ███         ███    ███   ███    ███   ███    ███ ███       ███
#    ███    █▀  ███▌ ███   ███   ███   ███    ███ ███         ███    █▀    ███    █▀    ███    █▀  ███       ███
#    ███        ███▌ ███   ███   ███   ███    ███ ███        ▄███▄▄▄       ███         ▄███▄▄▄     ███       ███
#  ▀███████████ ███▌ ███   ███   ███ ▀█████████▀  ███       ▀▀███▀▀▀     ▀███████████ ▀▀███▀▀▀     ███       ███
#           ███ ███  ███   ███   ███   ███        ███         ███    █▄           ███   ███    █▄  ███       ███
#     ▄█    ███ ███  ███   ███   ███   ███        ███▌    ▄   ███    ███    ▄█    ███   ███    ███ ███▌    ▄ ███▌    ▄
#   ▄████████▀  █▀    ▀█   ███   █▀   ▄████▀      █████▄▄██   ██████████  ▄████████▀    ██████████ █████▄▄██ █████▄▄██
#                                                 ▀                                                ▀         ▀
# SimpleSell made by iRxDuZ :3

# This function will automatically collect items dropped on the ground after breaking a block for the player.
auto_collect_items: true

# This feature will automatically collect the xp dropped on the ground after breaking a block to the player.
auto_collect_xp: true

# Set Economy Plugin to use availables (bedrockeconomy or economyapi)
economy:
  provider: economyapi

# Database configuration
database:
  # The database type. "sqlite" and "mysql" are supported.
  type: sqlite

  # Edit these settings only if you choose "sqlite".
  sqlite:
    # The file name of the database in the plugin data folder.
    # You can also put an absolute path here.
    file: database.sqlite
  # Edit these settings only if you choose "mysql".
  mysql:
    host: 127.0.0.1
    # Avoid using the "root" user for security reasons.
    username: root
    password: ""
    schema: your_schema
  # The maximum number of simultaneous SQL queries
  # Recommended: 1 for sqlite, 2 for MySQL. You may want to further increase this value if your MySQL connection is very slow.
  worker-limit: 1
```

## Permissions

| Permissions                    | Description                          | Default |
| ------------------------------ | ------------------------------------ | ------- |
| `sell.command`                 | Allow to use /sell main command      | `true`  |
| `adminsell.command`            | Allow to use /adminsell main command | `true`  |
| `adminsell.command.additem`    | Allow to add new item to sell        | `op`    |
| `adminsell.command.edititem`   | Allow to edit item price             | `op`    |
| `adminsell.command.removeitem` | Allow to remove item to sell         | `op`    |

### ✔ Credits

| Authors                       | Github                                                            | Lib                                                            |
| ----------------------------- | ----------------------------------------------------------------- | -------------------------------------------------------------- |
| SOFe                          | [SOFe](https://github.com/SOF3)                                   | [libasynql](https://github.com/poggit/libasynql)               |
| marshall                      | [marshall](https://github.com/CortexPE)                           | [Commando](https://github.com/CortexPE/Commando)               |
| DaPigGuy                      | [DaPigGuy](https://github.com/DaPigGuy)                           | [libPiggyEconomy](https://github.com/DaPigGuy/libPiggyEconomy) |
| Dylan's PocketMine-MP Plugins | [Dylan's PocketMine-MP Plugins](https://github.com/dktapps-pm-pl) | [pmforms](https://github.com/dktapps-pm-pl/pmforms)            |
