<?php
namespace PocketProX\Player;

class PlayerManager {
    private $players = [];

    public function addPlayer($name) {
        $player = new Player($name);
        $this->players[$name] = $player;
    }

    public function removePlayer($name) {
        if (isset($this->players[$name])) {
            unset($this->players[$name]);
        }
    }

    public function getPlayer($name) {
        return $this->players[$name] ?? null;
    }

    public function listPlayers() {
        return array_keys($this->players);
    }
}