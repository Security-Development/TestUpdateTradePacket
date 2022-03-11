<?php

/**
 * @name TestUpdateTradePacket
 * @author Neo-Developer
 * @main Neo\TestUpdateTradePacket
 * @version 0.1.0
 * @api 4.0.6
 */

 namespace Neo;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\network\mcpe\protocol\UpdateTradePacket;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;


class TestUpdateTradePacket extends PluginBase {

    public function onEnable() : void {
        $this->getServer()->getPluginManager()->registerEvents(new class implements Listener {

            public function onHit(EntityDamageByEntityEvent $event) : void {
                $entity = $event->getEntity();
                $damager = $event->getDamager();

                $tag = CompoundTag::create();

                $tag->setTag("Recipes", new ListTag([
                    CompoundTag::create()
                    ->setTag("buyA", (ItemFactory::getInstance()->get(ItemIds::DIAMOND)->setCount(15))->nbtSerialize()) // 교환 물품 1
                    //->setTag("buyB", ItemFactory::getInstance()->get(ItemIds::BREAD)->nbtSerialize()) // 교환 물품 2
                    ->setTag("sell", ItemFactory::getInstance()->get(ItemIds::IRON_ORE)->nbtSerialize())  // 판매 물품 
                    ->setInt('maxUses', 100) // 최대 사용 횟수
                    ->setByte('rewardExp', 0) // 경험치 보상
                ]));

                if($damager instanceof Player) {
                    $damager->getNetworkSession()->sendDataPacket(UpdateTradePacket::create(
                        1, WindowTypes::TRADING, 0, 3,$entity->getId(), $damager->getId(),"hello", false, true, new CacheableNbt($tag)
                    ));
                }
    
            }
        }, $this);
    }
}
