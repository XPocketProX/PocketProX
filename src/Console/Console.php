<?php
namespace PocketProX\Console;

use PocketProX\Server\Server;

class Console {
    private $server;

    public function __construct(Server $server) {
        $this->server = $server;
    }

    public function log($message) {
        echo "[Console]: $message\n";
    }

    public function handleInput($input) {
        $this->server->handleCommand(trim($input));
    }
}