<?php
namespace PocketProX\Utils;

class RCON {
    private $socket;
    private $password;

    public function __construct($host, $port, $password) {
        $this->socket = fsockopen($host, $port);
        $this->password = $password;
    }

    public function authenticate() {
        fwrite($this->socket, $this->createPacket(3, $this->password));
        $response = fread($this->socket, 4096);
        return $this->validateResponse($response);
    }

    public function executeCommand($command) {
        if ($this->authenticate()) {
            fwrite($this->socket, $this->createPacket(2, $command));
            return fread($this->socket, 4096);
        }
        return null;
    }

    private function createPacket($type, $body) {
        $id = 1; 
        $packet = pack('VV', $id, $type) . $body . "\x00\x00";
        return pack('V', strlen($packet)) . $packet;
    }

    private function validateResponse($response) {
        $parts = unpack('V1Size/V1Id/V1Type/a*Body', $response);
        return $parts['Id'] == 1;
    }

    public function close() {
        fclose($this->socket);
    }
}