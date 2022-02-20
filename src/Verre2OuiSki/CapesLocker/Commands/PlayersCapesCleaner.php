<?php

namespace Verre2OuiSki\CapesLocker\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Verre2OuiSki\CapesLocker\CapesLocker;

class PlayersCapesCleaner extends Command{

    private $plugin;

    public function __construct(CapesLocker $plugin){
        $this->plugin = $plugin;
        parent::__construct(
            "playerscapescleaner",
            "WARNING ! This command remove all undefined capes in 'capes.json' from capes lockers of players.",
            "/playerscapescleaner"
        );
        $this->setPermission("capeslocker.command.playerscapescleaner");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){

        $capes = array_keys($this->plugin->getCapes());
        $players_capes = $this->plugin->getPlayersCapes()->getAll();

        foreach ($players_capes as $player_uuid => $capes_id) {
            foreach($capes_id as $cape_id){
                if( !in_array($cape_id, $capes) ){
                    unset(
                        $players_capes[$player_uuid][array_search($cape_id, $players_capes[$player_uuid])]
                    );
                }
            }
            $players_capes[$player_uuid] = array_values($players_capes[$player_uuid]);
        }

        $this->plugin->getPlayersCapes()->reload();
        $this->plugin->getPlayersCapes()->setAll($players_capes);
        $this->plugin->getPlayersCapes()->save();
        $sender->sendMessage("§cplayers_capes.yml has been cleaned up");
    }
}