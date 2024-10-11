<?php
namespace PocketProX;

class ServerProperties {
    private $properties = [
        "server-name" => "PocketProX Server",
        "max-players" => 20,
        "xbox-online" => true
    ];

    private $filePath = __DIR__ . "/../server.properties";

    public function loadProperties() {
        if (file_exists($this->filePath)) {
            $this->properties = parse_ini_file($this->filePath);
        }
    }

    public function editProperty($name, $value) {
        if (isset($this->properties[$name])) {
            $this->properties[$name] = $value;
            $this->saveProperties();
        }
    }

    private function saveProperties() {
        $content = "";
        foreach ($this->properties as $key => $value) {
            $content .= "$key=$value\n";
        }
        file_put_contents($this->filePath, $content);
    }

    public function getProperties() {
        return $this->properties;
    }
}