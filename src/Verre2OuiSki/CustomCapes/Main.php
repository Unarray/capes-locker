<?php

namespace Verre2OuiSki\CustomCapes;

use Exception;
use pocketmine\entity\Skin;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Verre2OuiSki\CustomCapes\Commands\Capes;
use Verre2OuiSki\CustomCapes\Commands\ManageCapes;
use Verre2OuiSki\CustomCapes\Commands\PlayersCapesCleaner;

class Main extends PluginBase{

    private static $_instance;

    /** @var array $capes */
    private $capes;
    private $default_capes = [];
    /** @var Config $players_capes */
    private $players_capes;

    public static function getInstance() : self{
        return self::$_instance;
    }

    public function onEnable() : void {

        if(!extension_loaded("gd")){
            $this->getServer()->getLogger()->error("Please enable GB");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }

        self::$_instance = $this;
        
        foreach($this->getResources() as $file){
            $this->saveResource($file->getFilename());
        }

        $this->capes = (new Config( $this->getDataFolder() . "capes.json", Config::JSON ))->get("capes", []);

        foreach( $this->capes as $cape_id => $cape ){
            if($cape["default"]){
                $this->default_capes[$cape_id] = $cape;
            }
        }

        $this->players_capes = new Config( $this->getDataFolder() . "players_capes.yml", Config::YAML );

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
    public function getCapeById(string $cape_id){
        return $this->capes[$cape_id] ?? NULL;
    }

    /**
     * Unlock a cape for a specific player
     * @param Player $player Player to unlock a cape
     * @param string $cape_id The ID of the cape to unlock
     * @return void
     */
    public function unlockCape( Player $player, string $cape_id ) : void{

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
    public function lockCape( Player $player, string $cape_id) : void{

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
    public function setPlayerCape( Player $player, string $cape_id = null ){

        $old_skin = $player->getSkin();

        if(is_null($cape_id)){
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
    public function hasCape( Player $player, string $cape_id ) : bool{

        if( $this->capes[$cape_id]["default"] ) return true;

        $this->players_capes->reload();
        $player_capes = $this->players_capes->get(
            $player->getUniqueId()->toString()
        );
        return $player_capes ? in_array($cape_id, $player_capes) : false;
    }

    /**
     * Return unlocked player's capes (default capes isn't include)
     * @param Player $player Player to get his capes
     * @return array
     */
    public function getPlayerCapes(Player $player) : array {
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

    public function getPlayersCapes(){
        return $this->players_capes;
    }

}