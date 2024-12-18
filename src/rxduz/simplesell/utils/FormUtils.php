<?php

namespace rxduz\simplesell\utils;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\ModalForm;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use rxduz\simplesell\registry\SellItemRegistry;
use rxduz\simplesell\translation\Translation;

class FormUtils
{

    /**
     * @param Player $player
     * @param Item $item
     */
    public static function sendSellHandConfirmMenu(Player $player, Item $item): void
    {
        $form = new ModalForm(
            Translation::getInstance()->getMessage('SELL_HAND_FORM_TITLE'),
            Translation::getInstance()->getMessage(
                'SELL_HAND_FORM_TEXT',
                [
                    '{COUNT}' => strval($item->getCount()),
                    '{ITEM_NAME}' => $item->getName()
                ]
            ),
            function (Player $player, bool $choice) use ($item): void {
                if ($choice) {
                    SellItemRegistry::getInstance()->processSellHand($player, $item);
                }
            }
        );

        $player->sendForm($form);
    }

    /**
     * @param Player $player
     */
    public static function sendSellAllConfirmMenu(Player $player): void
    {
        $form = new ModalForm(
            Translation::getInstance()->getMessage('SELL_ALL_FORM_TITLE'),
            Translation::getInstance()->getMessage('SELL_ALL_FORM_TEXT'),
            function (Player $player, bool $choice): void {
                if ($choice) {
                    SellItemRegistry::getInstance()->processSellAll($player);
                }
            }
        );

        $player->sendForm($form);
    }

    /**
     * @param Player $player
     */
    public static function sendAdminSellRemoveForm(Player $player): void
    {
        $items = SellItemRegistry::getInstance()->getSellItems();

        $form = new CustomForm(
            'Remove Item Sell',
            [
                new Dropdown('itemName', 'Select Item Name', array_keys($items))
            ],
            function (Player $player, CustomFormResponse $response) use ($items): void {
                $index = $response->getInt('itemName');

                $sellItem = array_values($items)[$index] ?? null;

                if ($sellItem !== null) {
                    $itemName = $sellItem->getName();

                    SellItemRegistry::getInstance()->remove($itemName);

                    $player->sendMessage(TextFormat::GREEN . 'Item ' . TextFormat::YELLOW . $itemName . TextFormat::GREEN . ' was remove to sell.');
                }
            },
            function (Player $player): void {
                $player->sendMessage(TextFormat::RED . 'The delete process was canceled.');
            }
        );

        $player->sendForm($form);
    }

    /**
     * @param Player $player
     */
    public static function sendAdminSellEditForm(Player $player): void
    {
        $items = SellItemRegistry::getInstance()->getSellItems();

        $form = new CustomForm(
            'Edit Item Sell',
            [
                new Dropdown('itemName', 'Select Item Name', array_keys($items)),
                new Input('price', 'Write new Price')
            ],
            function (Player $player, CustomFormResponse $response) use ($items): void {
                $index = $response->getInt('itemName');

                $sellItem = array_values($items)[$index] ?? null;

                if ($sellItem !== null) {
                    $strPrice = $response->getString('price');

                    if (!is_numeric($strPrice)) {
                        $player->sendMessage(TextFormat::RED . 'Price value is invalid.');

                        return;
                    }

                    $itemName = $sellItem->getName();

                    $price = floatval($strPrice);

                    $data = SellItemRegistry::DEFAULT_DATA;

                    $data['id'] = $sellItem->getId();

                    $data['price'] = $price;

                    $sellItem->setPrice($price);

                    SellItemRegistry::getInstance()->save($itemName, $data);

                    $player->sendMessage(TextFormat::GREEN . 'Item ' . TextFormat::YELLOW . $itemName . TextFormat::GREEN . ' update price to ' . TextFormat::YELLOW . $strPrice . TextFormat::GREEN . '.');
                }
            },
            function (Player $player): void {
                $player->sendMessage(TextFormat::RED . 'The update process was canceled.');
            }
        );

        $player->sendForm($form);
    }
}
