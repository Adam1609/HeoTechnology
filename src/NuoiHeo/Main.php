<?php

namespace NuoiHeo;

use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\command\{Command, CommandSender, ConsoleCommandSender};
use pocketmine\event\player\{PlayerJoinEvent, PlayerQuitEvent};
use pocketmine\scheduler\ClosureTask;

class Main extends PluginBase implements Listener
{
    public $prefix = "PIG TECHNOLOGY";
    public $money, $coin, $no, $ta, $lv;
    public function onEnable()
    {
        $this->getLogger()->info("PIG TECHNOLOGY ON");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->money = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
        $this->coin = $this->getServer()->getPluginManager()->getPlugin("PointAPI");
       @mkdir($this->getDataFolder());
        $this->no = new Config($this->getDataFolder() . "no.yml", Config::YAML);
        $this->ta = new Config($this->getDataFolder() . "thucan.yml", Config::YAML);
        $this->lv = new Config($this->getDataFolder() . "level.yml", Config::YAML);
    }

    public function onJoin(PlayerJoinEvent $ev)
    {
        if (!$this->no->exists($ev->getPlayer()->getName())) {
            $this->no->set($ev->getPlayer()->getName(), 0);
            $this->no->save();
        }
        if (!$this->ta->exists($ev->getPlayer()->getName())) {
            $this->ta->set($ev->getPlayer()->getName(), 0);
            $this->ta->save();
        }
        if (!$this->lv->exists($ev->getPlayer()->getName())) {
            $this->lv->set($ev->getPlayer()->getName(), 1);
            $this->lv->save();
        }
    }

    public function onBreak(BlockBreakEvent $ev)
    {
        $p = $ev->getPlayer();
        $a = $this->myLevelHeo($p) * 1000;
        $b = $this->myLevelHeo($p) * 100;
        $c = $this->myLevelHeo($p) * 1 - 1;
        $rand = mt_rand(1, 100);
        if ($this->myLevelHeo($p) > 1) {
            switch ($rand) {
                case 5:
                    $p->sendMessage("§6Bạn Đã Nhận Được§a" . $a . "§6$ Khi Mine(Xu Này Từ Heo Của Bạn)");
                    $this->money->addMoney($p, $a);
                    break;
                case 20:
                    $p->sendMessage("§6Bạn Đã Nhận Được§a" . $b . "§6$ Khi Mine(Xu Này Từ Heo Của Bạn)");
                    $this->money->addMoney($p, $a);
                    break;
                case 50:
                    $p->sendMessage("§6Bạn Đã Nhận Được§a" . $b . "§6$ Khi Mine(Xu Này Từ Heo Của Bạn)");
                    $this->money->addMoney($p, $b);
                    break;
                case 70:
                    $this->coin->addPoint($p, $c);
                    $p->sendMessage("§6Bạn Đã Nhận Được§a" . $c . "§6Point Khi Mine(Point Này Từ Heo Của Bạn)");
                    break;
                default:
                    break;
            }
        }
    }

    ////End///

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch ($command->getName()) {
            case "heo":
                $this->menu($sender);
                return true;
        }
        return true;
    }

    public function menu($sender)
    {
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createSimpleForm(function (Player $sender, $data) {
            $result = $data;
            if ($result === null) {
            }
            switch ($result) {
                case 0:
                    break;
                case 1:
                    $this->Toplevel($sender);
                    break;
                case 2:
                    $this->heo($sender);
                    break;
            }
        });
        $form->setTitle("§l§e༺§bMenu §cHeo Technology§e༻");
        $form->addButton("§l§e༺§cEXITs§e༻");
        $form->addButton("§l§e༺§aXếp Hạng Heo§e༻");
        $form->addButton("§l§e༺§eMy Pig§e༻");
        $form->sendToPlayer($sender);
    }

    public function heo($sender)
    {
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createSimpleForm(function (Player $sender, $data) {
            $result = $data;
            if ($result === null) {
            }
            switch ($result) {
                case 0:
                    $this->menu($sender);
                    break;
                case 1:
                    //phân Bón Cao Cấp
                    $ta = $this->ta->get($sender->getPlayer()->getName());
                    if ($ta >= 1) {
                        $kn = $this->no->get($sender->getPlayer()->getName());
                        
                        $ta = $this->ta->get($sender->getPlayer()->getName());
                        $sender->sendMessage("§e༺§cĐã Cho Heo Ăn§e༻");
                        $this->no->set($sender->getPlayer()->getName(), ($this->no->get($sender->getPlayer()->getName()) + $ta*2));
                        $this->no->save();
                        $this->ta->set($sender->getPlayer()->getName(), ($this->ta->get($sender->getPlayer()->getName()) - $ta));
                        $this->ta->save();
                    }
                    if ($ta == 0) {
                        $sender->sendMessage("§e༺§cBạn Đã Hết Thức Ăn Vui Lòng Mua Thêm§e༻");
                    }
                    break;
                case 2:
                    $this->lencap($sender);
                    break;
                     case 3:
                     $money = $this->money->myMoney($sender->getPlayer()->getName());
                    if ($money < 100000){
                        $sender->sendMessage("§e༺§cKhông đủ tiền§e༻");
                    } else{
                         $this->ta->set($sender->getPlayer()->getName(), (int)$this->ta->get($sender->getPlayer()->getName()) + 5);
            $sender->sendMessage("§6§lMua Thành Công");
                        $this->money->reduceMoney($sender->getPlayer()->getName(), 100000);
            $this->ta->save();
                    }
                    break;

            }
        });
        $name = $sender->getPlayer()->getName();
        $kn = $this->no->get($sender->getPlayer()->getName());
        $cap = $this->lv->get($sender->getPlayer()->getName());
        $ta = $this->ta->get($sender->getPlayer()->getName());
        $maxkn = $cap * 500;
        $form->setTitle("§l§e༺§b§cMy PIG§e༻");
        $form->setContent("§l§e•§cHeo Của: §a" . $name . "\n§l§e•§aLevel: §b" . $cap . "\n§l§e•§bNo: §a" . $kn . "§6§l/20\n§l§e•§aThức ăn §b" . $ta . "");
        $form->addButton("§l§e•§c EXIT §e•");
        $form->addButton("§l§e•§a Cho Heo Ăn §e•");
        $form->addButton("§l§e•§a Lên Cấp §e•");
        $form->addButton("§l§e•§a Mua Thức Ăn Cho Heo §e•");
        $form->sendToPlayer($sender);
    }

    public function lencap($sender)
    {
        $player = $sender->getName();
        // Fix Bởi Nguyễn Công Danh (Danh Miner) Và Master Jero.
        $money = $this->money->myMoney($player);
        $no = $this->no->get($sender->getPlayer()->getName());
        if ($money < $this->myLevelHeo($player) * 1000000){
            $sender->sendMessage("§e⇀ §fBạn Không Đủ Tiền Để Lên Cấp Tiếp Theo");
            $sender->sendMessage("§eSố Tiền Để Lên Cấp Tiếp Theo Là" . $this->myLevelHeo($player) * 1000000 . "Xu");
        } elseif ($no < 20){
             $sender->sendMessage("§e⇀ §fHeo Chưa No Để Lên Cấp Tiếp Theo");
         } else {
            $this->lv->set($player, (int)$this->lv->get($player) + 1);
            $sender->sendMessage("§6§lLên Cấp Thành Công Bạn Đã Đạt Cấp" . $this->myLevelHeo($player) . "!");
            $cs = $this->myLevelHeo($player);
            $this->money->reduceMoney($player, $cs * 1000000);
            $this->lv->save();
            $this->no->set($player, 0);
            $this->no->save();
        }
        }

	public function Toplevel(Player $sender){
		$levelplot = $this->lv->getAll();
		$message = "";
		$message1 = "";
		if(count($levelplot) > 0){
			arsort($levelplot);
			$i = 1;
			foreach($levelplot as $name => $level){
				$message .= "§l§e⊹⊱§aXếp Hạng §e " . $i . "§a Thuộc Về§b" . $name . " §aVới§a" . $level . " §b Cấp§e⊰⊹\n\n";
				$message1 .= "§l§e⊹⊱§aXếp Hạng §e " . $i . "§a Thuộc Về§b" . $name . " §aVới§a" . $level . " §b Cấp§e⊰⊹\n";
				if($i >= 10){
					break;
				}
				++$i;
			}
		}
		
		$formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $formapi->createSimpleForm(function (Player $sender, ?int $data = null){
			$result = $data;
			switch($result){
				case 0:
				$this->Menu($sender);
				break;
			}
		});
		$form->setTitle("§l§e•§b TOP HEO §e•");
		$form->setContent($message);
		$form->addButton("§l§e•§c EXIT §e•");
		$form->sendToPlayer($sender);
		return $form;
	}
    public function myLevelHeo($player) {
        if($player instanceof Player) {
            $player = $player->getName();
        }
        $reincarnated = $this->lv->get($player);
        return $reincarnated;
    }
}
