<?php

namespace Verre2OuiSki\CapesLocker\Listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use Verre2OuiSki\CapesLocker\CapesLocker;

class SetCape implements Listener{

    private $plugin;

    public function __construct( CapesLocker $plugin ){
        $this->plugin = $plugin;
    }


    // Set cape if player disconnect with a cape
    public function onPlayerJoin( PlayerJoinEvent $event ){

        $player = $event->getPlayer();
        $cape = $this->plugin->getWearingCapeId( $player );

        $wearing_capes = $this->plugin->getWearingCapes();

        if( !$this->plugin->getCapeById( $cape ) ) {
            $wearing_capes->remove($player->getUniqueId()->toString());
            $wearing_capes->save();
            return;
        }

        if($cape) $this->plugin->setPlayerCape( $player, $cape );
    }

}