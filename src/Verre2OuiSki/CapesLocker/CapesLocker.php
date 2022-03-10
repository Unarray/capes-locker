<?php

namespace Verre2OuiSki\CapesLocker;

use Exception;
use pocketmine\entity\Skin;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Verre2OuiSki\CapesLocker\Commands\Capes;
use Verre2OuiSki\CapesLocker\Commands\ManageCapes;
use Verre2OuiSki\CapesLocker\Commands\PlayersCapesCleaner;
use Verre2OuiSki\CapesLocker\Listeners\SetCape;

class CapesLocker extends PluginBase{

    private static $_instance;

    /** @var array $capes */
    private $capes;
    private $default_capes = [];
    /** @var Config */
    private $players_capes;
    /** @var Config */
    private $wearing_cape;

    public static function getInstance() : self{
        return self::$_instance;
    }

    public function onEnable() : void {
        self::$_instance = $this;
        
        foreach($this->getResources() as $file){
            $this->saveResource($file->getFilename());
        }

        $this->capes = (new Config( $this->getDataFolder() . "capes.json", Config::JSON ))->getAll();

        $perm_manager = PermissionManager::getInstance();
        foreach( $this->capes as $cape_id => $cape ){
            if($cape["default"]){
                $this->default_capes[$cape_id] = $cape;
            }else{
                $permission = new Permission(
                    "capeslocker.cape." . $cape_id,
                    "Allow players to use \" " . $cape["name"] . " \" cape"
                );
                $perm_manager->addPermission($permission);
                $perm_manager->getPermission(DefaultPermissions::ROOT_OPERATOR)->addChild($permission->getName(), true);
            }
        }

        $this->players_capes = new Config( $this->getDataFolder() . "players_capes.yml", Config::YAML );
        $this->wearing_cape = new Config( $this->getDataFolder() . "wearing_cape.yml", Config::YAML );

        $this->getServer()->getPluginManager()->registerEvents( new SetCape($this), $this);

        $this->getServer()->getCommandMap()->register( $this->getName(), new Capes($this) );
        $this->getServer()->getCommandMap()->register( $this->getName(), new ManageCapes($this) );
        $this->getServer()->getCommandMap()->register( $this->getName(), new PlayersCapesCleaner($this) );
    }

    private function capeIdToCapeData( string $cape_id ){

        $cape_file = $this->capes[$cape_id]["cape"];
        $path = $this->getDataFolder() . $cape_file . ".png";

        $image = @imagecreatefrompng($path);
		if ($image === false) {
			throw new Exception("Couldn't load image");
		}

		$size = @imagesx($image) * @imagesy($image) * 4;
		if ($size !== 64 * 32 * 4) {
			throw new Exception("Invalid cape size");
		}

		$cape_data = "";
		for ($y = 0, $height = imagesy($image); $y < $height; $y++) {
            for ($x = 0, $width = imagesx($image); $x < $width; $x++) {
                $color = imagecolorat($image, $x, $y);
                $cape_data .= pack("c", ($color >> 16) & 0xFF) //red
                    . pack("c", ($color >> 8) & 0xFF) //green
                    . pack("c", $color & 0xFF) //blue
                    . pack("c", 255 - (($color & 0x7F000000) >> 23)); //alpha
            }
        }    

		imagedestroy($image);
		return $cape_data;
    }
   


// - - - PLUGIN API

    /**
     * Return all capes
     * @return array
     */
    public function getCapes(){
        return $this->capes;
    }

    /**
     * Return all default capes
     * @return array
     */
    public function getDefaultCapes(){
        return $this->default_capes;
    }

    /**
     * Return cape info
     * @param string $cape_id
     * @return NULL|array
     */
    public function getCapeById($cape_id){
        return $this->capes[$cape_id] ?? NULL;
    }

    /**
     * Return unlocked player's capes (default capes isn't include)
     * @param Player $player Player to get his capes
     * @return array
     */
    public function getPlayerCapes($player){
        $this->players_capes->reload();
        $player_capes_id = $this->players_capes->get(
            $player->getUniqueId()->toString(),
            []
        );

        $player_capes = [];
        foreach ($player_capes_id as $cape_id) {
            $cape = $this->getCapeById($cape_id);
            if($cape){
                $player_capes[$cape_id] = $cape;
            }
        }
        return $player_capes;
    }

    /**
     * Return cape player wearing
     * @param Player $player
     * @return null|string
     */
    public function getWearingCapeId( Player $player ) : null|string{
        $this->wearing_cape->reload();
        return $this->wearing_cape->get($player->getUniqueId()->toString(), null);
    }

    /**
     * Return all wearing capes
     * @return Config
     */
    public function getWearingCapes() : Config{
        return $this->wearing_cape;
    }

    /**
     * Return permitted player's capes (default capes isn't include)
     * @param Player $player Player to get his capes
     * @return array
     */
    public function getPlayerPermittedCapes($player){
        
        $player_capes = [];
        $capes = array_diff_key( $this->capes, $this->default_capes );

        foreach($capes as $cape_id => $cape){
            if( $player->hasPermission( "capeslocker.cape." . $cape_id ) ){
                $player_capes[$cape_id] = $cape;
            }
        }

        return $player_capes;
    }

    /**
     * Get alls players capes (default capes and permitted capes isn't include)
     * @return Config
     */
    public function getPlayersCapes(){
        return $this->players_capes;
    }

    /**
     * Unlock a cape for a specific player
     * @param Player $player Player to unlock a cape
     * @param string $cape_id The ID of the cape to unlock
     * @return void
     */
    public function unlockCape( $player, $cape_id ){

        if($this->hasCape($player, $cape_id)) return;

        $player_uuid = $player->getUniqueId()->toString();

        $this->players_capes->reload();

        if($this->players_capes->exists($player_uuid)){

            $player_capes = $this->players_capes->get($player_uuid);
            array_push($player_capes, $cape_id);
            $this->players_capes->set( $player_uuid, $player_capes );

        }else{
            $this->players_capes->set( $player_uuid, [$cape_id] );
        }

        $this->players_capes->save();
    }

    /**
     * lock a cape for a specific player
     * @param Player $player Player to lock a cape
     * @param string $cape_id The ID of the cape to lock
     * @return void
     */
    public function lockCape( $player, $cape_id){

        // if cape is unlock by default OR player doesn't have this cape
        if($this->capes[$cape_id]["default"] || !$this->hasCape($player, $cape_id)) return;

        $player_uuid = $player->getUniqueId()->toString();
        
        $this->players_capes->reload();
        $player_capes = $this->players_capes->get($player_uuid);

        $cape_id_index = array_search($cape_id, $player_capes);
        unset($player_capes[$cape_id_index]);

        if(empty($player_capes)){
            $this->players_capes->remove($player_uuid);
        }else{
            $this->players_capes->set( $player_uuid, $player_capes);
        }
        $this->players_capes->save();
    }

    /**
     * Unlock a cape for a specific player
     * @param Player $player Player to equip the cape with
     * @param string $cape_id The ID of the cape to be equipped
     * @return void
     */
    public function setPlayerCape( $player, $cape_id = null ){

        $old_skin = $player->getSkin();
        $this->wearing_cape->reload();

        if(is_null($cape_id)){

            $wear_cape = $this->wearing_cape->get($player->getUniqueId()->toString());

            if($wear_cape){
                $this->wearing_cape->remove($player->getUniqueId()->toString());
                $this->wearing_cape->save();
            }

            $player->setSkin(
                new Skin(
                    $old_skin->getSkinId(),
                    $old_skin->getSkinData(),
                    "",
                    $old_skin->getGeometryName(),
                    $old_skin->getGeometryData()
                )
            );
            $player->sendSkin();
            return;
        }


        $this->wearing_cape->set($player->getUniqueId()->toString(), $cape_id);
        $this->wearing_cape->save();

        $cape_data = $this->capeIdToCapeData($cape_id);

        $new_skin = new Skin(
            $old_skin->getSkinId(),
            $old_skin->getSkinData(),
            $cape_data,
            $old_skin->getGeometryName(),
            $old_skin->getGeometryData()
        );

        $player->setSkin($new_skin);
        $player->sendSkin();
    }

    /**
     * Check if a player have a specific cape
     * @param Player $player Player to check their capes
     * @param string $cape_id The ID of the cape to check
     * @return bool
     */
    public function hasCape( $player, $cape_id ){

        if( $this->capes[$cape_id]["default"] ) return true;
        if( $player->hasPermission( "capeslocker.cape." . $cape_id ) ) return true;

        $this->players_capes->reload();
        $player_capes = $this->players_capes->get(
            $player->getUniqueId()->toString()
        );
        return $player_capes ? in_array($cape_id, $player_capes) : false;
    }

}