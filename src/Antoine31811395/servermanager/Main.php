<?php

//© copryright 2021 Antoine31811395

namespace Antoine31811395\servermanager;

//pocketmine\base
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;

//pocketmine\level
use pocketmine\level\Level;
use pocketmine\event\entity\EntityLevelChangeEvent;

//pocketmine\event
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\Listener;

//pocketmine\command
use pocketmine\command\Command;
use pocketmine\command\CommandSender;


class Main extends PluginBase implements Listener{

	public $config;
	
	public function onEnable(){
		$this->config = $this->getConfig();
		$this->saveDefaultConfig();
		@mkdir($this->getDataFolder());	
		$this->getResource("config.yml");
		$this->getLogger()->info("§a[ServerManager] loaded, plugin by Antoine31811395 !");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onDisable(){
		$this->getLogger()->info("§c[ServerManager] unloaded, plugin by Antoine31811395 !");
    }

    public function onPreLogin(PlayerPreLoginEvent $event){

		$player = $event->getPlayer();
		$name = $player->getName();

		if(!$player->isWhitelisted()) {

			/*
			@Var initialization
			*/
			$whitelistmessage = $this->config->get("whitelistMessage");

			$event->setKickMessage($whitelistmessage);
          	$event->setCancelled(true);
		}
	}

    public function onJoin(PlayerJoinEvent $event){

    	$player = $event->getPlayer();
		$name = $player->getName();
		//disable join message
		$event->setJoinMessage("");

		/*
			@Var initialization
		*/
		$setgamemode = $this->config->get("setGamemode");

		$setfood = $this->config->get("setFood");

		$sethealth = $this->config->get("setHealth");

		$setmaxhealth = $this->config->get("setMaxHealth");

		$clearinventory = $this->config->get("clearInventory");

		$cleararmorinventory = $this->config->get("clearAmorInventory");

		$cleareffect = $this->config->get("clearEffect");

		$joinmessagebroadcast = $this->config->get("JoinMessageBroadcastType");

		$joinmessagebroadcasttip = $this->config->get("JoinMessageBroadcastTipType");

		$setjoinmessage = $this->config->get("setJoinMessage");

		/*
			@Var->add($name)
		*/
		$setjoinmessage = str_replace("{PLAYER}", ($name), $setjoinmessage);

		/*
			join settings
		*/
		$player->setGamemode($setgamemode);

		$player->setFood($setfood);

		$player->setHealth($sethealth);

		$player->setMaxHealth($setmaxhealth);

		if($clearinventory == "true"){
			$player->getInventory()->clearAll();
		}

		if($cleararmorinventory == "true"){
			$player->getArmorInventory()->clearAll();
		}

		if($cleareffect == "true"){
			$player->removeAllEffects();
		}

		if($joinmessagebroadcast == "true"){
			$this->getServer()->broadcastMessage($setjoinmessage);	
		}

		if($joinmessagebroadcasttip == "true"){
			$this->getServer()->broadcastTip($setjoinmessage);	
		}
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{

    	$getpermission = $this->config->get("TeleportWorldPermission");
		
		if($cmd == "tpw"){

			if($sender Instanceof Player){

				if($sender->hasPermission($getpermission)){

					if(!empty($args[0])){

						if(file_exists($this->getServer()->getDataPath() . "/worlds/" . $args[0])) {

							$this->getServer()->loadLevel($args[0]);
							$sender->teleport($this->getServer()->getLevelByName($args[0])->getSafeSpawn(), 10, 10);
							$sender->sendMessage("§a[ServerManager] you have been teleported !");
						}else{
							$sender->sendMessage("§c[ServerManager] it's not a world !, please enter a valid world.");
						}
					}else{
						$sender->sendMessage("§ecommand: /tpw <worldname>");
					}
				}
			}else{
				$sender->sendMessage("§cyou dont have the permission to use this command !");
			}
		}else{
			$sender->sendMessage("§c[ServerManager] please run this command in game and by a player !");

		}
		return true;
	}

	public function onKick(PlayerKickEvent $event){

		$player = $event->getPlayer();
		$reason = $event->getReason();
		$name = $player->getName();

		/*
			@Var initialization
		*/
		$kicknoreasonmessage = $this->config->get("kickNoReasonMessage");

		$kickmessage = $this->config->get("kickMessage");

		/*
			@Var->add($name)
		*/
		$kicknoreasonmessage = str_replace("{PLAYER}", ($name), $kicknoreasonmessage);

		$kickmessage = str_replace("{PLAYER}", ($name), $kickmessage);

		$kickmessage = str_replace("{REASON}", ($reason), $kickmessage);

		if($reason == null){
			$this->getServer()->broadcastMessage($kicknoreasonmessage);
		}else{
			$this->getServer()->broadcastMessage($kickmessage);
		}
	}

	public function onHunger(PlayerExhaustEvent $event){

    	$player = $event->getPlayer();

    	/*
			@Var initialization
		*/
		$nofoodlose = $this->config->get("noFoodLose");

		if($nofoodlose == "true"){
    		$event->setCancelled(true);
    	}
	}

	public function onDropItem(PlayerDropItemEvent $event){
		
		$player = $event->getPlayer();

		/*
			@Var initialization
		*/
		$nodropitem = $this->config->get("noDropItem");
		
		if($nodropitem == "true"){
			$event->setCancelled(true);
		}
	}

	public function onQuit(PlayerQuitEvent $event){
		
		$player = $event->getPlayer();
		$name = $player->getName();
		//disable the default message
		$event->setQuitMessage("");

		/*
			@Var initialization
		*/
		$quitmessagebroadcast = $this->config->get("QuitMessageBroadcastType");

		$quitmessagebroadcasttip = $this->config->get("QuitMessageBroadcastTipType");

		$setquitmessage = $this->config->get("setQuitMessage");

		/*
			@Var->add($name)
		*/
		$setquitmessage = str_replace("{PLAYER}", ($name), $setquitmessage);

		if($quitmessagebroadcast == "true"){
			$this->getServer()->broadcastMessage($setquitmessage);	
		}

		if($quitmessagebroadcasttip == "true"){
			$this->getServer()->broadcastTip($setquitmessage);	
		}
	}
}
