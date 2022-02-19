<?php

namespace Verre2OuiSki\CustomCapes\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use Verre2OuiSki\CustomCapes\libs\dktapps\pmforms\FormIcon;
use Verre2OuiSki\CustomCapes\libs\dktapps\pmforms\MenuForm;
use Verre2OuiSki\CustomCapes\libs\dktapps\pmforms\MenuOption;
use Verre2OuiSki\CustomCapes\Main;

class Capes extends Command{

    private $plugin;
    private $players_cooldown = [];

    private $cooldown;
    private $cooldown_message;
    private $locked_cape_message;
    private $cape_equiped_message;
    private $menu_title;
    private $menu_body;

    public function __construct( Main $plugin ){
        $this->plugin = $plugin;

        parent::__construct(
            "capes",
            "Open capes menu !",
            "/capes"
        );
        $this->setPermission("customcapes.command.capes");

        $config = $this->plugin->getConfig();
        $this->cooldown = $config->get("cape_cooldown");
        $this->cooldown_message = str_replace(
            "{cooldown}",
            $this->cooldown,
            $config->get("cooldown_message")
        );
        $this->locked_cape_message = $config->get("locked_cape_message");
        $this->cape_equiped_message = $config->get("cape_equiped_message");
        $this->menu_title = $config->get("menu_title");
        $this->menu_body = $config->get("menu_body");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){

        // Sender doesn't have permission to execute this command
        if(!$sender->hasPermission($this->getPermission())){
            $sender->sendMessage($this->getPermissionMessage()); return;
        }
        
        // Sender isn't a player
        if(!$sender instanceof Player){
            $sender->sendMessage("§cYou must be a player to execute this command"); return;
        }

        $sender->sendForm( $this->capesList($sender) );       
    }



// --- FORMS
    private function capesList(Player $player){

        $options = [
            new MenuOption("§4Remove", new FormIcon("textures/ui/realms_red_x", FormIcon::IMAGE_TYPE_PATH))
        ];
        $options_cape_link = [];


        foreach($this->plugin->getCapes() as $cape_id => $cape){

            array_push(
                $options,
                new MenuOption(
                    $cape["name"],
                    $this->plugin->hasCape($player, $cape_id) ? new FormIcon("textures/ui/icon_unlocked", FormIcon::IMAGE_TYPE_PATH) : new FormIcon("textures/ui/icon_lock", FormIcon::IMAGE_TYPE_PATH)
                )
            );
            $options_cape_link[array_key_last($options)] = $cape_id;
        }

        return new MenuForm(
            $this->menu_title,
            $this->menu_body,
            $options,
            function( Player $submitter, int $selected ) use ($options_cape_link) : void {

                if($selected == 0){

                    $this->plugin->setPlayerCape($submitter);
                    return;
                }

                $submitter->sendForm(
                    $this->capeOptions(
                        $submitter,
                        $options_cape_link[$selected]
                    )
                );
            }
        );
    }

    private function capeOptions(Player $player, $selected_cape){

        $cape = $this->plugin->getCapes()[$selected_cape];
        $player_has_cape = $this->plugin->hasCape($player, $selected_cape);

        return new MenuForm(
            $cape["name"],
            $cape["description"],
            [
                new MenuOption(
                    $player_has_cape ? "Use" : "Use §o(locked)",
                    new FormIcon("textures/ui/dressing_room_capes", FormIcon::IMAGE_TYPE_PATH)
                ),
                new MenuOption(
                    "Go back",
                    new FormIcon("textures/ui/arrow_left", FormIcon::IMAGE_TYPE_PATH)
                )
            ],
            function(Player $submitter, int $choice) use ($selected_cape, $player_has_cape) : void{

                // If player go back
                if($choice == 1){
                    $submitter->sendForm(
                        $this->capesList($submitter)
                    ); return;
                }

                $cape_name = $this->plugin->getCapes()[$selected_cape]["name"];

                // If player doesn't have this cape
                if(!$player_has_cape){
                    $submitter->sendMessage(str_replace(
                        "{cape}",
                        $cape_name,
                        $this->locked_cape_message
                    )); return;
                };

                $player_cooldown = $this->players_cooldown[$submitter->getName()] ?? false;
                
                if( $player_cooldown ){
                    $submitter->sendMessage( $this->cooldown_message ); return;
                }

                $player_name = $submitter->getName();
                $this->players_cooldown[$player_name] = true;
                $this->plugin->getScheduler()->scheduleDelayedTask(
                    new ClosureTask(function() use ($player_name) : void{
                        unset($this->players_cooldown[$player_name]);
                    }),
                    $this->cooldown * 20
                );

                $this->plugin->setPlayerCape($submitter, $selected_cape);
                $submitter->sendMessage(str_replace(
                    "{cape}",
                    $cape_name,
                    $this->cape_equiped_message
                ));
            }
        );

    }

}