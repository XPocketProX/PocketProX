<?php

/*

           -
         /   \
      /         \
   /   PocketMine  \
/          MP         \
|\     @shoghicp     /|
|.   \           /   .|
| ..     \   /     .. |
|    ..    |    ..    |
|       .. | ..       |
\          |          /
   \       |       /
      \    |    /
         \ | /

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.


*/

class TorchBlock extends FlowableBlock{
	public function __construct($meta = 0){
		parent::__construct(TORCH, $meta, "Torch");
	}
	
	public function onUpdate($type){
		if($type === BLOCK_UPDATE_NORMAL){
			$side = $this->getMetadata();
			$faces = array(
					1 => 4,
					2 => 5,
					3 => 2,
					4 => 3,
					5 => 0,
					6 => 0,
					0 => 0,
			);

			if($this->getSide($faces[$side])->isTransparent === true and !($side === 0 and $this->getSide(0)->getID() === FENCE)){ //Replace wit common break method
				ServerAPI::request()->api->entity->drop($this, BlockAPI::getItem($this->id, 0, 1));
				$this->level->setBlock($this, new AirBlock(), false);
				return BLOCK_UPDATE_NORMAL;
			}
		}
		return false;
	}

	public function place(Item $item, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz){
		if($target->isTransparent === false and $face !== 0){
			$faces = array(
				1 => 5,
				2 => 4,
				3 => 3,
				4 => 2,
				5 => 1,
			);
			$this->meta = $faces[$face];
			$this->level->setBlock($block, $this);
			return true;
		}elseif($this->getSide(0)->isTransparent === false or $this->getSide(0)->getID() === FENCE){
			$this->meta = 0;
			$this->level->setBlock($block, $this);
			return true;
		}
		return false;
	}
	public function getDrops(Item $item, Player $player){
		return array(
			array($this->id, 0, 1),
		);
	}
}