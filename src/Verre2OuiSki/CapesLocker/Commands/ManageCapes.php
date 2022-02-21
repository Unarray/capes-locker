<?php

namespace Verre2OuiSki\CapesLocker\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Verre2OuiSki\CapesLocker\libs\dktapps\pmforms\FormIcon;
use Verre2OuiSki\CapesLocker\libs\dktapps\pmforms\MenuForm;
use Verre2OuiSki\CapesLocker\libs\dktapps\pmforms\MenuOption;
use Verre2OuiSki\CapesLocker\CapesLocker;

class ManageCapes extends Command{

    private $plugin;

    public function __construct( CapesLocker $plugin ){
        $this->plugin = $plugin;

        parent::__construct(
            "managecapes",
            "Manage capes of a player",
            "/mcapes <player> [cape id] [lock|unlock]",
            ["mcapes"]
        );
        $this->setPermission("capeslocker.command.managecapes");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        
        // Sender doesn't have permission to execute this command
        if(!$sender->hasPermission($this->getPermission())){
            $sender->sendMessage($this->getPermissionMessage()); return;
        }
        
        $player = $this->plugin->getServer()->getPlayerByPrefix($args[0] ?? "😋");
        $cape_id = $args[1] ?? null;
        $action_type = $args[2] ?? null;

        
        // Sender isn't a player and didn't specify args OR player isn't specify
        if((!$sender instanceof Player && !$action_type) || !isset($args[0])){
            $sender->sendMessage($this->getUsage()); return;
        }

        // If player selected isn't connected
        if(!$player){
            $sender->sendMessage("§cCan't find player : $args[0]"); return;
        }

        // If sender use command
        if( $action_type ){

            $cape = $this->plugin->getCapeById($cape_id);
            // If cape selected doesn't exist
            if(!$cape){
                $sender->sendMessage("§cThere is no cape with this ID : $cape_id"); return;
            }

            // if action isn't lock or unlock
            $action_type = strtolower($action_type);
            if($action_type !== "lock" && $action_type !== "unlock"){
                $sender->sendMessage("§cInvalid action : $cape_id\nuse 'lock' or 'unlock' action"); return;
            }

            if($action_type === "lock"){
                $this->plugin->lockCape($player, $cape_id);
                $sender->sendMessage("$cape_id has been removed from " . $player->getName() . "'s capes");
                $player->sendMessage($cape["name"] . " §rhas been removed from your capes by " . $sender->getName());
                return;
            }

            $this->plugin->unlockCape($player, $cape_id);
            $sender->sendMessage("$cape_id has been added to " . $player->getName() . "'s capes");
            $player->sendMessage($cape["name"] . " §rhas been added to your capes by " . $sender->getName());
            return;
        }

        // Send manage capes form
        if($sender instanceof Player){
            $sender->sendForm($this->playerCapeList($player));
        }

    }

    
    private function playerCapeList(Player $player_capes){

        $options = [];
        $options_cape_link = [];

        $unlocked_capes = $this->plugin->getPlayerCapes($player_capes);
        $locked_capes = array_diff_key($this->plugin->getCapes(), array_merge($this->plugin->getDefaultCapes(), $unlocked_capes, $this->plugin->getPlayerPermittedCapes($player_capes)));

        // Set player capes at top of the menu
        foreach ($unlocked_capes as $cape_id => $cape) {
            array_push(
                $options,
                new MenuOption(
                    $cape["name"],
                    new FormIcon("textures/ui/icon_unlocked", FormIcon::IMAGE_TYPE_PATH)
                )
            );
            $options_cape_link[array_key_last($options)] = $cape_id;

        }

        // Locked capes after unlocked capes
        foreach($locked_capes as $cape_id => $cape){

            array_push(
                $options,
                new MenuOption(
                    $cape["name"],
                    new FormIcon("textures/ui/icon_lock", FormIcon::IMAGE_TYPE_PATH)
                )
            );
            $options_cape_link[array_key_last($options)] = $cape_id;
        }

        return new MenuForm(
            $player_capes->getName() . "'s capes",
            "",
            $options,
            function( Player $submitter, int $selected ) use ($options_cape_link, $player_capes) : void {

                $submitter->sendForm(
                    $this->capeOptions(
                        $player_capes,
                        $options_cape_link[$selected]
                    )
                );
            }
        );
    }

    private function capeOptions(Player $player_capes, $selected_cape){

        $cape = $this->plugin->getCapes()[$selected_cape];

        return new MenuForm(
            $cape["name"],
            $cape["description"],
            [
                new MenuOption(
                    "Remove from " . $player_capes->getName() . "'s capes",
                    new FormIcon("textures/ui/icon_trash", FormIcon::IMAGE_TYPE_PATH)
                ),
                new MenuOption(
                    "Add to " . $player_capes->getName() . "'s capes",
                    new FormIcon("textures/ui/download_backup", FormIcon::IMAGE_TYPE_PATH)
                ),
                new MenuOption(
                    "Go back",
                    new FormIcon("textures/ui/arrow_left", FormIcon::IMAGE_TYPE_PATH)
                )
            ],
            function(Player $submitter, int $choice) use ($selected_cape, $player_capes) : void{

                // If player go back
                if($choice == 2){
                    $submitter->sendForm(
                        $this->playerCapeList($player_capes)
                    ); return;
                }

                $cape_name = $this->plugin->getCapes()[$selected_cape]["name"];

                // If the cape has been removed
                if($choice == 0){

                    $this->plugin->lockCape($player_capes, $selected_cape);
                    $submitter->sendMessage("$selected_cape has been removed from " . $player_capes->getName() . "'s capes");
                    $player_capes->sendMessage("$cape_name §rhas been removed from your capes by " . $submitter->getName());
                    return;
                }

                // If the cape has been added
                $this->plugin->unlockCape($player_capes, $selected_cape);
                $submitter->sendMessage("$selected_cape has been added to " . $player_capes->getName() . "'s capes");
                $player_capes->sendMessage("$cape_name §rhas been added to your capes by " . $submitter->getName());
            }
        );
    }

}