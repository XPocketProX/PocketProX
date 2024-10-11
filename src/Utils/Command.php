<?php
namespace PocketProX\Utils;

class Command {
    private $commands = [];

    public function __construct() {
        $this->registerCommand("say", [$this, "sayCommand"]);
        $this->registerCommand("give", [$this, "giveCommand"]);
        $this->registerCommand("stop", [$this, "stopCommand"]);
        $this->registerCommand("restart", [$this, "restartCommand"]);
    }

    private function registerCommand($name, callable $callback) {
        $this->commands[$name] = $callback;
    }

    public function execute($name, $params) {
        if (isset($this->commands[$name])) {
            call_user_func($this->commands[$name], $params);
        }
    }

    public function sayCommand($params) {
        echo "Message to all players: " . implode(" ", $params) . "\n";
    }

    public function giveCommand($params) {
        $playerName = $params[0];
        $itemName = $params[1];
        echo "$playerName has been given $itemName\n";
    }

    public function stopCommand() {
        exit("Server stopped.");
    }

    public function restartCommand() {
        echo "Server restarting...\n";
        exec("php index.php"); // Restarts the server
        exit();
    }
}