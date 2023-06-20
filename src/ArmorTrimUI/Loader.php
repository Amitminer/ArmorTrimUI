<?php

declare(strict_types = 1);

namespace ArmorTrimUI;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\player\Player;
use pocketmine\command\CommandSender;
use ArmorTrimUI\Manager\FormManager;

class Loader extends PluginBase
{
    public function onLoad(): void
    {
        $this->getLogger()->info("§aEnabled ArmorTrimUI!");
    }
    public function onCommand(CommandSender $sender,Command $command,string $label,array $args): bool {
        switch ($command->getName()) {
            case "trims":
                $formM = new FormManager();
                $formM->getTrimsUI($sender);
                return true;
        }
    }
}