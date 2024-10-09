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

class ClayBlock extends SolidBlock{
	public function __construct(){
		parent::__construct(CLAY_BLOCK, 0, "Clay Block");
	}

	public function getDrops(Item $item, Player $player){
		return array(
			array(CLAY, 0, 4),
		);
	}
}