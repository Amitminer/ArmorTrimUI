<?php

declare(strict_types = 1);

namespace ArmorTrimUI\Manager;

use jojoe77777\FormAPI\SimpleForm;
use ArmorTrimUI\Types\TrimsType;
use ArmorTrimUI\Types\MaterialType;
use ArmorTrimUI\Manager\ArmorTrimManager;
use ArmorTrimUI\Manager\EconomyManager;
use pocketmine\item\Armor;
use pocketmine\player\Player;
use ReflectionClass;

class FormManager {
    
    public function getTrimsUI(Player $player) {
        $form = new SimpleForm(function (Player $player, $data) {
            if ($data === null) {
                return true;
            }
            $option = $this->getTrimsType($data);
            $item = $player->getInventory()->getItemInHand();
            if ($option !== null) {
                if ($item !== null || !$item instanceof Armor) {
                    $this->getMaterialTypeUI($player, $option, $item);
                } else {
                    $player->sendMessage(
                        "§cPlease hold an Armor in your hand to apply a Armortrim."
                    );
                }
            }
        });
        $form->setTitle("§a§lArmorTrims!");
        foreach ($this->getTrimsTypeConstants() as $trim) {
            $form->addButton($this->getRandomColor() . strtoupper($trim));
        }
        $form->addButton("TODO:");
        $player->sendForm($form);
    }
    public function getMaterialTypeUI(Player $player, string $trimType, $item)
    {
    $form = new SimpleForm(function (Player $player, $data) use ($trimType, $item) {
        if ($data === null) {
            return true;
        }
        $option = $this->getMaterialType($data);
        if ($option !== null) {
            if (!$item instanceof Armor) {
                $player->sendMessage("§cPlease hold an Armor in your hand to apply an Armor trim.");
                return;
            }
            $trimItem = ArmorTrimManager::create($item, $option, $trimType);
            if ($trimItem !== null) {
                $this->reducemoney($player,$trimItem);
            } else {
                $player->sendMessage("§cFailed to create the Armor trim. Please try again.");
            }
        }
    });
    $form->setTitle("§a§lArmorTrims!");
    foreach ($this->getMaterialTypeConstants() as $material) {
        $form->addButton($this->getRandomColor() . strtoupper($material));
    }
    $player->sendForm($form);
    }
    
    public function reducemoney(Player $player, $trimItem): void {
    EconomyManager::getPriceBasedOnMoney($player, function($amount) use ($player, $trimItem) {
        if ($amount === null) {
            $player->sendMessage("§cYou don't have enough money to purchase this item. (required more than 300k)");
            return;
        }

        EconomyManager::reduceMoney($player, $amount, function($success) use ($player, $trimItem) {
            if ($success) {
                $player->sendMessage("§aPayment successful! You purchased the item.");
                $player->getInventory()->addItem($trimItem);
            } else {
                $player->sendMessage("§cPayment failed. Please try again.");
            }
        });
    });
}

    public function getRandomColor()
{
    $colors = [
        "§l§0", "§l§1", "§l§2", "§l§3", "§l§4", "§l§5", "§l§6", "§l§7", "§l§8",
        "§l§9", "§l§a", "§l§b", "§l§c", "§l§d", "§l§e", "§l§f", "§l", "§l§l",
        "§l§m", "§l§n", "§l§o", "§l§r"
    ];
    $randomColor = $colors[array_rand($colors)];
    return $randomColor;
}

    public function getMaterialType($index): ?string
    {
        $reflection = new ReflectionClass(MaterialType::class);
        $constants = $reflection->getConstants();
        $option = array_values($constants)[$index] ?? null;
        return $option;
    }

    public function getMaterialTypeConstants(): array
    {
        $reflection = new ReflectionClass(MaterialType::class);
        $constants = $reflection->getConstants();
        return $constants;
    }

    public static function getTrimsType($index): ?string
    {
        $reflection = new ReflectionClass(TrimsType::class);
        $constants = $reflection->getConstants();
        $option = array_values($constants)[$index] ?? null;
        return $option;
    }

    public static function getTrimsTypeConstants(): array
    {
        $reflection = new ReflectionClass(TrimsType::class);
        $constants = $reflection->getConstants();
        return $constants;
    }
}
