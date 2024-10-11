<?php
namespace PocketProX\Inventory;

class Inventory {
    private $slots = [];

    public function __construct($size = 36) {
        $this->slots = array_fill(0, $size, null);
    }

    public function addItem($item) {
        foreach ($this->slots as &$slot) {
            if (is_null($slot)) {
                $slot = $item;
                return true;
            }
        }
        return false;
    }

    public function removeItem($item) {
        foreach ($this->slots as &$slot) {
            if ($slot === $item) {
                $slot = null;
                return true;
            }
        }
        return false;
    }

    public function getItems() {
        return array_filter($this->slots);
    }
}