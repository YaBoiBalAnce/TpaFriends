<?php
namespace tpafriends;

use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\Plugin;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\level\Position;

class removeRequest extends PluginTask{
	private $player;
	public function __construct(Plugin $owner, Player $player){
		parent::__construct($owner);
		$this->player = $player;
	}

	public function onRun($currentTick){
		$this->getOwner()->removeRequest($this->player);
	}
}