<?php
namespace PocketProX\Player;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use PocketProX\Network\NetworkSession;

class Player {
    private $name;
    private $uuid;
    private $position;
    private $session;

    public function __construct($name) {
        $this->name = $name;
        $this->uuid = uniqid();
        $this->position = new Vector3(0, 64, 0);
        $this->session = new NetworkSession();
    }

    public function getUUID() {
        return $this->uuid;
    }

    public function getName() {
        return $this->name;
    }

    public function getPosition() {
        return $this->position;
    }

    public function sendPacket($packet) {
        $this->session->sendDataPacket($packet);
    }
    
    public function kick() {
        
    }
    
    public function teleport() {
        
    }
}