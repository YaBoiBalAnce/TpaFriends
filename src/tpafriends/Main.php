<?php
namespace tpafriends;

use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\level\Position;
use pocketmine\scheduler\CallbackTask;

class main extends PluginBase {
	private $friendsapi;
	private $config;
	private $requests;
	public function onEnable(){
		$this->getLogger()->info("Loaded!");
		$this->friendsapi = $this->getServer()->getPluginManager()->getPlugin("Friends");
		$this->config = new Config($this->getDataFolder()."config.yml",Config::YAML,array(
				"usefriendapi" => false,
				"tpdelay" => 3
		));
		$this->config->save();
	}
	public function onCommand(CommandSender $sender,Command $command, $label,array $args){
		if ($sender instanceof Player){
		switch ($command->getName()){
			case "tpaccept":
				if(in_array($sender->getName(), $this->requests)){
					foreach ($this->requests as $requester => $accepter){
						if ($accepter === $sender->getName()){
							$pos = $sender->getPosition();
							$sender->sendMessage(TextFormat::GREEN."TPA request accepted!");
							$requester = $this->getServer()->getPlayer($requester);
							$requester->sendMessage("§e========");
							$requester->sendMessage("§b " . $sender->getName() . " §7has accepted your TPA Request!");
							$requester->sendMessage("§7Teleporting...");
							$requester->sendMessage("§e========");
							$task = new teleport($this, $sender, $pos);
							$this->getServer()->getScheduler()->scheduleDelayedTask($task, 20 * $this->getConfig()->get("tpdelay"));
							$this->removeRequest($requester);
						}
					}
				}else{
					$sender->sendMessage("§e========");
					$sender->sendMessage("§7No pending TPA requests!");
					$sender->sendMessage("§e========");
				}
			break;
			case "tpa":
				if (isset($args[0])){
					$requestp = $this->getServer()->getPlayer($args[0]);
					if ($requestp instanceof Player){
							$sender->sendMessage("Sent tpa request!");
							$this->addRequest($sender, $requestp);
						}else{
							$sender->sendMessage("§e========");
							$sender->sendMessage("§aSucessfully sent TPA Request!");
							$sender->sendMessage("§e========");
							$this->addRequest($sender, $requestp);
						}
					}else{
						$sender->sendMessage("§e========");
						$sender->sendMessage("§7That player is not online. Check §b/list§7!");
						$sender->sendMessage("§e========");
					}
				}else{
					$sender->sendMessage("§e========");
					$sender->sendMessage("§4USAGE:§b /tpa [name]");
					$sender->sendMessage("§e========");
				}
			break;
			
		}}
	}
	
	public function teleportPlayer(Player $player,Position $pos){
		$player->teleport($pos,$player->getYaw(),$player->getPitch());
		$player->sendMessage("§e========");
		$player->sendMessage("§aTeleported!");
		$player->sendMessage("§e========");
	}
	
	public function addRequest(Player $player,Player $request){
		$this->requests[$player->getName()] = $request->getName();
		$request->sendMessage("§e========");
		$request->sendMessage("§b" . $player->getName() . " §7has requested to teleport to you!");
		$request->sendMessage("§aACCEPT: §b/tpaccept");
		$request->sendMessage("§4DENY: §bAt the moment you have to ignore, but in the future you will be able to do /tpdeny!");
		$request->sendMessage("§e========");
		$task = new removeRequest($this, $player);
		$this->getServer()->getScheduler()->scheduleDelayedTask($task, 10*20);
	}
	public function removeRequest(Player $player){
		if(isset($this->requests[$player->getName()])){
		unset($this->requests[$player->getName()]);
		}
	}
}
