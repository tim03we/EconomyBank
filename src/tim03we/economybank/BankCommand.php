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

use onebone\economyapi\EconomyAPI;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class BankCommand extends Command {

    public function __construct(Main $plugin)
    {
        parent::__construct("bank", "Bank Command", "/bank <add | remove | show | see> [money or player]");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player) {
            if(isset($args[0])) {
                if($args[0] ===  "show") {
                    $msg = $this->plugin->cfg->getNested("messages.show");
                    $msg = str_replace("{money}", $this->plugin->bank->get(strtolower($sender->getName())), $msg);
                    $sender->sendMessage($msg);
                } else if($args[0] === "see") {
                    if(isset($args[1])) {
                        if($this->plugin->bank->exists(strtolower($args[1]))) {
                            $msg = $this->plugin->cfg->getNested("messages.see");
                            $msg = str_replace("{player}", strtolower($args[1]), $msg);
                            $msg = str_replace("{money}", $this->plugin->bank->get(strtolower($args[0])), $msg);
                            $sender->sendMessage($msg);
                        } else {
                            $sender->sendMessage($this->plugin->cfg->getNested("messages.not-found"));
                        }
                    } else {
                        $sender->sendMessage($this->getUsage());
                    }
                } else if($args[0] === "add") {
                    if(isset($args[1])) {
                        if(is_numeric($args[1])) {
                            if((EconomyAPI::getInstance()->myMoney($sender) - $args[1]) > -1) {
                                EconomyAPI::getInstance()->reduceMoney($sender, $args[1]);
                                $this->plugin->bank->set(strtolower($sender->getName()), $this->plugin->bank->get(strtolower($sender->getName())) + $args[1]);
                                $this->plugin->bank->save();
                                $msg = $this->plugin->cfg->getNested("messages.add");
                                $msg = str_replace("{money}", $args[1], $msg);
                                $sender->sendMessage($msg);
                            } else {
                                $sender->sendMessage($this->plugin->cfg->getNested("messages.little-money"));
                            }
                        } else {
                            $sender->sendMessage($this->plugin->cfg->getNested("messages.not-numeric"));
                        }
                    } else {
                        $sender->sendMessage($this->getUsage());
                    }
                } else if($args[0] === "remove") {
                    if(isset($args[1])) {
                        if(is_numeric($args[1])) {
                            if(($this->plugin->bank->get(strtolower($sender->getName())) - $args[1]) > -1) {
                                EconomyAPI::getInstance()->addMoney($sender, $args[1]);
                                $this->plugin->bank->set(strtolower($sender->getName()), $this->plugin->bank->get(strtolower($sender->getName())) - $args[1]);
                                $this->plugin->bank->save();
                                $msg = $this->plugin->cfg->getNested("messages.remove");
                                $msg = str_replace("{money}", $args[1], $msg);
                                $sender->sendMessage($msg);
                            } else {
                                $sender->sendMessage($this->plugin->cfg->getNested("messages.little-bank-money"));
                            }
                        } else {
                            $sender->sendMessage($this->plugin->cfg->getNested("messages.not-numeric"));
                        }
                    } else {
                        $sender->sendMessage($this->getUsage());
                    }
                }
            } else {
                $sender->sendMessage($this->getUsage());
            }
        } else {
            $sender->sendMessage("Run this command InGame!");
        }
    }
}