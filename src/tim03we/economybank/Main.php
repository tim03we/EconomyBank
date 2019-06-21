<?php

/*
 * Copyright (c) 2019 tim03we  < https://github.com/tim03we >
 * Discord: tim03we | TP#9129
 *
 * This software is distributed under "GNU General Public License v3.0".
 * This license allows you to use it and/or modify it but you are not at
 * all allowed to sell this plugin at any cost. If found doing so the
 * necessary action required would be taken.
 *
 * EconomyBank is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License v3.0 for more details.
 *
 * You should have received a copy of the GNU General Public License v3.0
 * along with this program. If not, see
 * <https://opensource.org/licenses/GPL-3.0>.
 */

namespace tim03we\economybank;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register("bank", new BankCommand($this));
        $this->saveResource("settings.yml");
        $this->cfg = new Config($this->getDataFolder() . "settings.yml", Config::YAML);
        $this->bank = new Config("../ServerFiles/bank.yml", Config::YAML);
        if(!$this->getServer()->getPluginManager()->getPlugin("EconomyAPI")) {
            $this->getLogger()->alert("The plugin was deactivated because the EconomyAPI plugin was not found.");
            $this->getServer()->getPluginManager()->disablePlugin($this->getServer()->getPluginManager()->getPlugin("EconomyAPI"));
        }
    }

    public function onLogin(PlayerLoginEvent $event)
    {
        $player = $event->getPlayer();
        if(!$this->bank->exists(strtolower($player->getName()))) {
            $this->bank->set(strtolower($player->getName()), 0);
            $this->bank->save();
        }
        $this->bank->reload();
    }
}