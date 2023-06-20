<?php

namespace ArmorTrimUI\Manager;

use ArmorTrimUI\Utils\TrimUtils;
use pocketmine\item\Armor;

class ArmorTrimManager {
    // Author: KRUNCHSHooT
    
    public static function create(Armor $armor, string $material, string $pattern) : Armor {
        $nbt = $armor->getNamedTag();
        TrimUtils::createNbtTrim($nbt, $material, $pattern);
        $armor->setNamedTag($nbt);
        return $armor;
    }
}