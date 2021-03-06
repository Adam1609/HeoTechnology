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
use jojoe77777\FormAPI\{SimpleForm, CustomForm, ModalForm};
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
                    $p->sendMessage("??6B???n ???? Nh???n ???????c??a" . $a . "??6$ Khi Mine(Xu N??y T??? Heo C???a B???n)");
                    $this->money->addMoney($p, $a);
                    break;
                case 20:
                    $p->sendMessage("??6B???n ???? Nh???n ???????c??a" . $b . "??6$ Khi Mine(Xu N??y T??? Heo C???a B???n)");
                    $this->money->addMoney($p, $a);
                    break;
                case 50:
                    $p->sendMessage("??6B???n ???? Nh???n ???????c??a" . $b . "??6$ Khi Mine(Xu N??y T??? Heo C???a B???n)");
                    $this->money->addMoney($p, $b);
                    break;
                case 70:
                    $this->coin->addPoint($p, $c);
                    $p->sendMessage("??6B???n ???? Nh???n ???????c??a" . $c . "??6Point Khi Mine(Point N??y T??? Heo C???a B???n)");
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
            case "pig":
                $this->menu($sender);
                return true;
        }
        return true;
    }

    public function menu($sender)
    {
        $form = new SimpleForm(function (Player $sender, $data) {
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
        $form->setTitle("??l??e?????bMenu ??cHeo Technology??e???");
        $form->addButton("??l??e?????cEXITs??e???");
        $form->addButton("??l??e?????aX???p H???ng Heo??e???");
        $form->addButton("??l??e?????eMy Pig??e???");
        $form->sendToPlayer($sender);
    }

    public function heo($sender)
    {
        $form = new SimpleForm(function (Player $sender, $data) {
            $result = $data;
            if ($result === null) {
            }
            switch ($result) {
                case 0:
                    $this->menu($sender);
                    break;
                case 1:
                    //ph??n B??n Cao C???p
                    $ta = $this->ta->get($sender->getPlayer()->getName());
                    if ($ta >= 1) {
                        $kn = $this->no->get($sender->getPlayer()->getName());
                        
                        $ta = $this->ta->get($sender->getPlayer()->getName());
                        $sender->sendMessage("??e?????c???? Cho Heo ??n??e???");
                        $this->no->set($sender->getPlayer()->getName(), ($this->no->get($sender->getPlayer()->getName()) + $ta*2));
                        $this->no->save();
                        $this->ta->set($sender->getPlayer()->getName(), ($this->ta->get($sender->getPlayer()->getName()) - $ta));
                        $this->ta->save();
                    }
                    if ($ta == 0) {
                        $sender->sendMessage("??e?????cB???n ???? H???t Th???c ??n Vui L??ng Mua Th??m??e???");
                    }
                    break;
                case 2:
                    $this->lencap($sender);
                    break;
                     case 3:
                     $money = $this->money->myMoney($sender->getPlayer()->getName());
                    if ($money < 100000){
                        $sender->sendMessage("??e?????cKh??ng ????? ti???n??e???");
                    } else{
                         $this->ta->set($sender->getPlayer()->getName(), (int)$this->ta->get($sender->getPlayer()->getName()) + 5);
            $sender->sendMessage("??6??lMua Th??nh C??ng");
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
        $form->setTitle("??l??e?????b??cMy PIG??e???");
        $form->setContent("??l??e?????cHeo C???a: ??a" . $name . "\n??l??e?????aLevel: ??b" . $cap . "\n??l??e?????bNo: ??a" . $kn . "??6??l/20\n??l??e?????aTh???c ??n ??b" . $ta . "");
        $form->addButton("??l??e?????c EXIT ??e???");
        $form->addButton("??l??e?????a Cho Heo ??n ??e???");
        $form->addButton("??l??e?????a L??n C???p ??e???");
        $form->addButton("??l??e?????a Mua Th???c ??n Cho Heo ??e???");
        $form->sendToPlayer($sender);
    }

    public function lencap($sender)
    {
        $player = $sender->getName();
        // Fix B???i Nguy???n C??ng Danh (Danh Miner) V?? Master Jero.
        $money = $this->money->myMoney($player);
        $no = $this->no->get($sender->getPlayer()->getName());
        if ($money < $this->myLevelHeo($player) * 1000000){
            $sender->sendMessage("??e??? ??fB???n Kh??ng ????? Ti???n ????? L??n C???p Ti???p Theo");
            $sender->sendMessage("??eS??? Ti???n ????? L??n C???p Ti???p Theo L??" . $this->myLevelHeo($player) * 1000000 . "Xu");
        } elseif ($no < 20){
             $sender->sendMessage("??e??? ??fHeo Ch??a No ????? L??n C???p Ti???p Theo");
         } else {
            $this->lv->set($player, (int)$this->lv->get($player) + 1);
            $sender->sendMessage("??6??lL??n C???p Th??nh C??ng B???n ???? ?????t C???p" . $this->myLevelHeo($player) . "!");
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
				$message .= "??l??e????????aX???p H???ng ??e " . $i . "??a Thu???c V?????b" . $name . " ??aV???i??a" . $level . " ??b C???p??e??????\n\n";
				$message1 .= "??l??e????????aX???p H???ng ??e " . $i . "??a Thu???c V?????b" . $name . " ??aV???i??a" . $level . " ??b C???p??e??????\n";
				if($i >= 10){
					break;
				}
				++$i;
			}
		}
		
		$form = new SimpleForm(function (Player $sender, ?int $data = null){
			$result = $data;
			switch($result){
				case 0:
				$this->Menu($sender);
				break;
			}
		});
		$form->setTitle("??l??e?????b TOP HEO ??e???");
		$form->setContent($message);
		$form->addButton("??l??e?????c EXIT ??e???");
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
