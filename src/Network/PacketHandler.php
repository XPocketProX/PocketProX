<?php
namespace PocketProX\Network;

use PocketProX\World\World;
use PocketProX\Player\PlayerManager;
use PocketProX\Utils\Command;

class PacketHandler {
    private $world;
    private $playerManager;

    public function __construct(World $world, PlayerManager $playerManager) {
        $this->world = $world;
        $this->playerManager = $playerManager;
    }

    public function handlePacket($packet) {
        $decodedPacket = Protocol::decodePacket($packet);

        $response = [
            'status' => 'error',
            'message' => 'Unknown packet type',
        ];

        switch ($decodedPacket['type']) {
            case Protocol::HANDSHAKE_PACKET:
                $response = $this->handleHandshake($decodedPacket);
                break;
            case Protocol::PLAY_PACKET:
                $response = $this->handlePlay($decodedPacket);
                break;
            case Protocol::BLOCK_PLACE_PACKET:
                $response = $this->handleBlockPlace($decodedPacket);
                break;
            case Protocol::BLOCK_BREAK_PACKET:
                $response = $this->handleBlockBreak($decodedPacket);
                break;
            case Protocol::COMMAND_PACKET:
                $response = $this->handleCommand($decodedPacket);
                break;
            case Protocol::TEXT_PACKET;
                $response = $this->handleText($decodedPacket);
                break;
        }

        return Protocol::encodePacket(Protocol::RESPONSE_PACKET, $response);
    }

    private function handleHandshake($packet) {
        // Simulate handshake acknowledgment
        $playerName = $packet['data']['playerName'];
        if (!$this->playerManager->getPlayer($playerName)) {
            $this->playerManager->addPlayer($playerName);
            return [
                'status' => 'success',
                'message' => "Handshake successful. Welcome, $playerName!",
                'playerUUID' => $this->playerManager->getPlayer($playerName)->getUUID(),
            ];
        } else {
            return [
                'status' => 'error',
                'message' => "Player already exists.",
            ];
        }
    }

    private function handlePlay($packet) {
        $playerName = $packet['data']['playerName'];
        $player = $this->playerManager->getPlayer($playerName);

        if ($player) {
            return [
                'status' => 'success',
                'message' => "$playerName is playing.",
            ];
        } else {
            return [
                'status' => 'error',
                'message' => "Player not found.",
            ];
        }
    }

    private function handleBlockPlace($packet) {
        $x = $packet['data']['x'];
        $y = $packet['data']['y'];
        $z = $packet['data']['z'];
        $blockType = $packet['data']['blockType'];

        $this->world->placeBlock($x, $y, $z, $blockType);

        return [
            'status' => 'success',
            'message' => "Block $blockType placed at ($x, $y, $z).",
        ];
    }

    private function handleBlockBreak($packet) {
        $x = $packet['data']['x'];
        $y = $packet['data']['y'];
        $z = $packet['data']['z'];

        $this->world->breakBlock($x, $y, $z);

        return [
            'status' => 'success',
            'message' => "Block at ($x, $y, $z) broken.",
        ];
    }

    private function handleCommand($packet) {
        $command = $packet['data']['command'];
        $params = explode(' ', $command);

        $commandHandler = new Command();
        $commandHandler->execute($params[0], array_slice($params, 1));

        return [
            'status' => 'success',
            'message' => "Command '$command' executed.",
        ];
    }

    private function handleText($packet) {
      
    }
}