<?php
namespace PocketProX\World;

class World {
    private $blocks = [];

    public function placeBlock($x, $y, $z, $blockType) {
        $this->blocks["$x:$y:$z"] = new Block($blockType);
    }

    public function breakBlock($x, $y, $z) {
        if (isset($this->blocks["$x:$y:$z"])) {
            unset($this->blocks["$x:$y:$z"]);
        }
    }

    public function getBlock($x, $y, $z) {
        return $this->blocks["$x:$y:$z"] ?? null;
    }
}