<?php
namespace CompassTeleport;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerInteractEvent;

class CompassTeleport extends PluginBase implements Listener{
    /** @var int */
    public $itemId;
    /** @var int */
    public $itemDamage;

    /** @var int */
    public $maxBlock;

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->datainit();
    }

    public function datainit(){
        $this->saveResource("setting.yml");

        $config = new Config($this->getDataFolder()."setting.yml",Config::YAML);
        $itemdata = explode(":",$config->get("item"));
        $this->itemId = (int) $itemdata[0];
        $this->itemDamage = (int) $itemdata[1];
        $this->maxBlock = (int) $config->get("maxBlock");
    }
    
    public function PlayerInteract(PlayerInteractEvent $event){
        if(!$event->getPLayer()->isOP()) return;
        if($event->getBlock()->x == 0&&$event->getBlock()->y == 0&&$event->getBlock()->z == 0){
            if($event->getItem()->getId() !== $this->itemId||$event->getItem()->getDamage() !== $this->itemDamage){
                return;
            }
            $player = $event->getPlayer();
            $level = $player->getLevel();
            $directionVector = $player->getDirectionVector();
            $tmpVector3 = $player->add(0,$player->getEyeHeight(),0);
            $id = null;
            for($i = 1; $i <= $this->maxBlock; $i++){//
                if(($id = $level->getBlockIdAt($tmpVector3->x,$tmpVector3->y,$tmpVector3->z)) !== 0){
                    if($level->getBlockIdAt($tmpVector3->x,$tmpVector3->y,$tmpVector3->z) !== 0){
                        ++$tmpVector3->y;
                    }

                    $yaw = $player->getYaw();
                    $pitch = $player->getPitch();

                    $event->getPlayer()->teleport(new Vector3($tmpVector3->x,(int) $tmpVector3->y,$tmpVector3->z));

                    $player->yaw = $yaw;
                    $player->pitch = $pitch;
                    break;
                }
                
                $tmpVector3->x += $directionVector->x;
                $tmpVector3->y += $directionVector->y;
                $tmpVector3->z += $directionVector->z;

                if(!($tmpVector3->y < 255&&$tmpVector3->y > 0)){//...?
                    break;//...??
                }
            }
        }
    }
}
