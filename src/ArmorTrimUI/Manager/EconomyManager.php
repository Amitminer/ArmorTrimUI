<?php

declare(strict_types = 1);

namespace ArmorTrimUI\Manager;

use cooldogedev\BedrockEconomy\libs\cooldogedev\libSQL\context\ClosureContext;
use onebone\economyapi\EconomyAPI;
use pocketmine\player\Player;
use pocketmine\Server;
use Closure;

class EconomyManager {
    
    // LibEco
    //Author: [DavidGlitch04]
    public const ECONOMYAPI = "EconomyAPI";
	
	public const BEDROCKECONOMYAPI = "BedrockEconomyAPI";
	
	public static function randomprice(){
	    //TODO:
	}

	public static function getPriceBasedOnMoney($player, callable $callback): void {
    self::myMoney($player, static function(float $money) use ($callback) {
        if ($money > 800000) {
            $callback(800000);
        } elseif ($money > 750000) {
            $callback(750000);
        } elseif ($money > 650000) {
            $callback(650000);
        } elseif ($money > 600000) {
            $callback(600000);
        } elseif ($money > 550000) {
            $callback(550000);
        } elseif ($money > 450000) {
            $callback(450000);
        } elseif ($money > 350000) {
            $callback(350000);
        } elseif ($money > 250000) {
            $callback(250000);
        } else {
            $callback(null);
        }
    });
}
    private static function getEconomy(): array
    {
        $api = Server::getInstance()->getPluginManager()->getPlugin('EconomyAPI');
        if ($api !== null) {
            return [self::ECONOMYAPI, $api];
        } else {
            $api = Server::getInstance()->getPluginManager()->getPlugin('BedrockEconomy');
            if ($api !== null) {
                return [self::BEDROCKECONOMYAPI, $api];
            } else{
                return [null];
            }
        }
    }
    public static function isInstall(): bool
    {
        return !is_null(self::getEconomy()[0]);
    }
 
    public static function myMoney(Player $player, Closure $callback): void
    {
        if (self::getEconomy()[0] === self::ECONOMYAPI) {
            $money = self::getEconomy()[1]->myMoney($player);
            assert(is_float($money));
            $callback($money);
        } elseif (self::getEconomy()[0] === self::BEDROCKECONOMYAPI) {
            self::getEconomy()[1]->getAPI()->getPlayerBalance($player->getName(), ClosureContext::create(static function (?int $balance) use ($callback): void {
                $callback($balance ?? 0);
            }));
        }
    }

    public static function addMoney(Player $player, int $amount): void
    {
        if (self::getEconomy()[0] === self::ECONOMYAPI) {
            self::getEconomy()[1]->addMoney($player, $amount);
        } elseif (self::getEconomy()[0] === self::BEDROCKECONOMYAPI) {
            self::getEconomy()[1]->getAPI()->addToPlayerBalance($player->getName(), (int) $amount);
        }
    }

    public static function reduceMoney(Player $player, int $amount, Closure $callback): void
    {
        if (self::getEconomy()[0] === self::ECONOMYAPI) {
            $callback(self::getEconomy()[1]->reduceMoney($player, $amount) === EconomyAPI::RET_SUCCESS);
        } elseif (self::getEconomy()[0] === self::BEDROCKECONOMYAPI) {
            self::getEconomy()[1]->getAPI()->subtractFromPlayerBalance($player->getName(), (int) ceil($amount), ClosureContext::create(static function (bool $success) use ($callback): void {
                $callback($success);
            }));
        }
    }
}