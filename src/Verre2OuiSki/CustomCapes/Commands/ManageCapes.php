<?php

namespace Verre2OuiSki\CustomCapes\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Verre2OuiSki\CustomCapes\Main;

class ManageCapes extends Command{

    private $plugin;

    public function __construct( Main $plugin ){
        $this->plugin = $plugin;

        parent::__construct(
            "managecapes",
            "Manage capes of a player",
            "/mcapes [player] [cape id] [lock|unlock]",
            ["mcapes"]
        );
        $this->setPermission("customcapes.command.managecapes");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        
        // Sender doesn't have permission to execute this command
        if(!$sender->hasPermission($this->getPermission())){
            $sender->sendMessage($this->getPermissionMessage()); return;
        }
        
        $player = $this->plugin->getServer()->getPlayerByPrefix($args[0] ?? "");
        $cape_id = $args[1] ?? null;
        
        // Sender isn't a player
        if(!$sender instanceof Player){


            // If player isn't connected
            if(is_null($player)){
                $sender->sendMessage("§cCan't find player : $args[0]"); return;
            }

            return;
        }

    }

}