<?php
namespace PocketProX\Player;

class Inventory {
    private $items = [];

    public function addItem($item) {
        $this->items[] = $item;
    }

    public function removeItem($item) {
        foreach ($this->items as $key => $storedItem) {
            if ($storedItem === $item) {
                unset($this->items[$key]);
                break;
            }
        }
    }

    public function getItems() {
        return $this->items;
    }
}