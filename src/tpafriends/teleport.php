<?php
namespace tpafriends;

use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\Plugin;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\level\Position;

class teleport extends PluginTask{
	private $player;
	private $pos;
	public function __construct(Plugin $owner, Player $player,Position $pos){
		parent::__construct($owner);
		$this->player = $player;
		$this->pos = $pos;
	}

	public function onRun($currentTick){
		$this->getOwner()->teleportPlayer($this->player, $this->pos);
	}
}