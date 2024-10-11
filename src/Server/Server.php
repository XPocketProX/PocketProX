<?php

namespace PocketProX\Server;

use PocketProX\Player\Player;
use PocketProX\World\World;
use PocketProX\Console\Console;
use PocketProX\Network\NetworkSession;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\DisconnectPacket;
use Exception;

class Server {
    private array $players = [];
    private array $bannedPlayers = []; // List of banned players
    private int $console;
    private int $running;
    private int $world;
    private int $socket; // Network communication socket
    private int $properties; // Server properties

    public function __construct(World $world) {
        $this->world = $world;
        $this->console = new Console($this);
        $this->running = true;
    }

    public function start() {
        $this->loadConfiguration();
        $this->initializeSocket();
        $this->console->log("Server started on " . $this->properties['server-ip'] . ":" . $this->properties['server-port']);
        $this->listenForConnections();
    }

    public function stop() {
        $this->running = false;
        $this->console->log("Server stopped.");
        fclose($this->socket);
    }

    private function loadConfiguration() {
        try {
            $this->properties = parse_ini_file("server.properties");
            if (!$this->properties) {
                throw new Exception("server.properties could not be loaded.");
            }
        } catch (Exception $e) {
            $this->console->error("Failed to load configuration: " . $e->getMessage());
        }
    }

    private function initializeSocket() {
        $serverIp = $this->properties['server-ip'] ?? '0.0.0.0';
        $serverPort = $this->properties['server-port'] ?? 19132;

        // Create socket for network communication using IP and port from server.properties
        $this->socket = stream_socket_server("tcp://$serverIp:$serverPort", $errno, $errstr);
        if (!$this->socket) {
            $this->console->error("Failed to create socket: $errstr ($errno)");
            exit(1);
        }
    }

    private function listenForConnections() {
        while ($this->running) {
            $client = @stream_socket_accept($this->socket, -1);
            if ($client) {
                $this->handleClient($client);
            }
        }
    }

    private function handleClient($client) {
        // Create a new NetworkSession for the client
        $session = new NetworkSession($this);

        // Read and handle packet data from the client
        $packet = $this->getNextPacket($client);
        if ($packet) {
            $session->handlePacket($packet);
        }
        
        // Close the client connection after processing
        fclose($client);
    }

    private function getNextPacket($client) {
        // Read data from the client socket
        $data = fread($client, 1024);
        if ($data) {
            // Process the raw data into a packet using your network logic
            $packet = $this->decodePacket($data);
            return $packet;
        }
        return null;
    }

    private function decodePacket($data) {
        // Decode packet data based on your specific protocol
        $packet = null;

        // Implement decoding logic based on the protocol
        if (strpos($data, "Login") !== false) {
            $packet = new LoginPacket();
            $packet->decode($data);
        } elseif (strpos($data, "AddPlayer") !== false) {
            $packet = new AddPlayerPacket();
            $packet->decode($data);
        } elseif (strpos($data, "Disconnect") !== false) {
            $packet = new DisconnectPacket();
            $packet->decode($data);
        }

        return $packet;
    }

    public function addPlayer(Player $player) {
        $this->players[$player->getUUID()] = $player;

        // Create and send AddPlayerPacket to all players
        $addPlayerPacket = new AddPlayerPacket();
        $addPlayerPacket->uuid = $player->getUUID();
        $addPlayerPacket->position = $player->getPosition();
        $this->broadcastPacket($addPlayerPacket);
    }

    public function removePlayer($uuid) {
        // Remove player from the players list by UUID
        if (isset($this->players[$uuid])) {
            unset($this->players[$uuid]);

            // Optionally, you can send a disconnect packet or other notification to all clients
            $disconnectPacket = new DisconnectPacket();
            $disconnectPacket->reason = "Player has left the server.";
            $this->broadcastPacket($disconnectPacket);
        }
    }

    public function broadcastPacket($packet) {
        // Send a packet to all connected players
        foreach ($this->players as $player) {
            $player->sendPacket($packet);
        }
    }

    public function handleCommand($command) {
        $args = explode(" ", $command);
        $cmd = strtolower($args[0]);

        switch ($cmd) {
            case "stop":
                $this->stop();
                break;

            case "restart":
                $this->stop();
                $this->start();
                break;

            case "kick":
                if (isset($args[1])) {
                    $this->kickPlayer($args[1]);
                } else {
                    $this->console->warn("Usage: kick <player>");
                }
                break;

            case "ban":
                if (isset($args[1])) {
                    $this->banPlayer($args[1]);
                } else {
                    $this->console->warn("Usage: ban <player>");
                }
                break;

            case "unban":
                if (isset($args[1])) {
                    $this->unbanPlayer($args[1]);
                } else {
                    $this->console->warn("Usage: unban <player>");
                }
                break;

            case "give":
                if (isset($args[1], $args[2])) {
                    $this->giveItem($args[1], $args[2], $args[3] ?? 1);
                } else {
                    $this->console->warn("Usage: give <player> <item> [amount]");
                }
                break;

            case "teleport":
                if (isset($args[1], $args[2], $args[3], $args[4])) {
                    $this->teleportPlayer($args[1], $args[2], $args[3], $args[4]);
                } else {
                    $this->console->warn("Usage: teleport <player> <x> <y> <z>");
                }
                break;

            default:
                $this->console->warn("Unknown command: " . $command);
        }
    }

    private function kickPlayer($playerName) {
        foreach ($this->players as $player) {
            if ($player->getName() === $playerName) {
                $player->kick("Kicked by an operator");
                $this->removePlayer($player->getUUID());
                $this->console->log("Player $playerName has been kicked.");
                return;
            }
        }
        $this->console->warn("Player $playerName not found.");
    }

    private function banPlayer($playerName) {
        foreach ($this->players as $player) {
            if ($player->getName() === $playerName) {
                $this->bannedPlayers[] = $player->getUUID();
                $player->kick("Banned by an operator");
                $this->removePlayer($player->getUUID());
                $this->console->log("Player $playerName has been banned.");
                return;
            }
        }
        $this->console->warn("Player $playerName not found.");
    }

    private function unbanPlayer($playerName) {
        foreach ($this->players as $player) {
            if (in_array($player->getUUID(), $this->bannedPlayers)) {
                $key = array_search($player->getUUID(), $this->bannedPlayers);
                unset($this->bannedPlayers[$key]);
                $this->console->log("Player $playerName has been unbanned.");
                return;
            }
        }
        $this->console->warn("Player $playerName is not banned.");
    }

    private function giveItem($playerName, $item, $amount) {
        foreach ($this->players as $player) {
            if ($player->getName() === $playerName) {
                // Assuming the player class has an addItem method
                $player->addItem($item, $amount);
                $this->console->log("Gave $amount of $item to $playerName.");
                return;
            }
        }
        $this->console->warn("Player $playerName not found.");
    }

    private function teleportPlayer($playerName, $x, $y, $z) {
        foreach ($this->players as $player) {
            if ($player->getName() === $playerName) {
                $player->teleport($x, $y, $z);
                $this->console->log("Teleported $playerName to ($x, $y, $z).");
                return;
            }
        }
        $this->console->warn("Player $playerName not found.");
    }
}