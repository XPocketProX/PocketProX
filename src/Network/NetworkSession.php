<?php
namespace PocketProX\Network;

use pocketmine\network\mcpe\bedrock\protocol\DataPacket;
use pocketmine\network\mcpe\bedrock\protocol\LoginPacket;
use pocketmine\network\mcpe\bedrock\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\bedrock\protocol\DisconnectPacket;

class NetworkSession {
    private $server;
    private $player;
    private $connected;

    public function __construct($server) {
        $this->server = $server;
        $this->connected = false;
    }

    public function handlePacket(DataPacket $packet) {
        switch ($packet->pid()) {
            case LoginPacket::NETWORK_ID:
                $this->handleLogin($packet);
                break;
            case AddPlayerPacket::NETWORK_ID:
                $this->handleAddPlayer($packet);
                break;
            case DisconnectPacket::NETWORK_ID:
                $this->handleDisconnect($packet);
                break;
        }
    }

    private function handleLogin(LoginPacket $packet) {
        $this->connected = true;
        $this->player = new \PocketProX\Player\Player($packet->username);
        $this->server->addPlayer($this->player);
    }

    private function handleAddPlayer(AddPlayerPacket $packet) {
        if ($this->connected) {
            $this->server->broadcastPacket($packet);
        }
    }

    private function handleDisconnect(DisconnectPacket $packet) {
        $this->connected = false;
        $this->server->removePlayer($this->player->getUUID());
    }

    public function sendDataPacket(DataPacket $packet) {
    }
}