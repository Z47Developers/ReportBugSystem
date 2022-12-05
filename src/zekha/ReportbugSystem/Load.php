<?php

namespace zekha\ReportbugSystem;

use pocketmine\Server;
use pocketmine\player\Player;

use pocketmine\plugin\PluginBase;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TE;

class Load extends PluginBase implements Listener {
    
    public $players = [];
  
    public function onEnable(): void {
        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");
        if($this->getConfig()->get("api")===null) {
          $this->getLogger()->info("unknown api");
          $this->getServer()->getPluginManager()->disablePlugin($this);
        }
    }
    
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args):bool {
        switch($cmd->getName()) {
        case "bug": 
            
            if($sender instanceof Player) {
                $this->bugForm($sender);
            } else {
          $sender->sendMessage("command can extend in-game only");
        }
        break;
    }
    
    return true;
    }
  
    public function bugForm($player) {
        $list = [];
        foreach($this->getServer()->getOnlinePlayers() as $p) {
            $list[] = $p->getName();
        }
        
        $this->players[$player->getName()] = $list;
        
        $form = new CustomForm(function (Player $player, array $data = null){
            if($data === null) {
              $player->sendMessage(TE::RED."Bug Failed");
                return true;
            }
            $web=new Webhook($this->getConfig()->get("api"));
            $msg=new Message();
            $e=new Embed();
            $index=$data[1];
            $e->setTitle("Report Bug");
            $e->setDescription("[Reported By: {$data[1]}]   [Plugin/Feature Name: {$data[2]}]    [Reason: {$data[3]}]");
            $msg->addEmbed($e);
            $web->send($msg);
            $player->sendMessage(TE::GREEN."Bug was sent");
        });
        $form->setTitle("Report Bug");
        $form->addLabel("Report Bug Plugin In The Server");
        $form->addInput("Your Name In Minecraft", "Input Your Name..", "");
        $form->addInput("Plugin Name", "A Plugin Name", "");
        $form->addInput("Reason", "Reason", "");
        $form->sendToPlayer($player);
        $form->addLabel("This Plugin Is By Z47, YT : Zekha47");
        return $form;
    }
    
}
