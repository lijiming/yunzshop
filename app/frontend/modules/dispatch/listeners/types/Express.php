<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/14
 * Time: 上午10:40
 */

namespace app\frontend\modules\dispatch\listeners\types;

use app\common\events\dispatch\OnDispatchTypeInfoDisplayEvent;
use app\common\events\order\OrderCreatedEvent;

use app\common\models\OrderAddress;
use app\frontend\modules\member\models\MemberAddress;

class Express
{
    private $event;

    public function onSave(OrderCreatedEvent $even)
    {
        $this->event = $even;
        if (!$this->needDispatch()) {
            return;
        }
        //保存信息
        $this->saveExpressInfo();

        return;
    }

    public function onDisplay(OnDispatchTypeInfoDisplayEvent $event)
    {
        $this->event = $event;
        if (!$this->needDispatch()) {
            return;
        }
        //返回信息 todo 需要获取用户当前默认地址

        $data = $event->getOrderModel()->getMember()->defaultAddress;
        if(!isset($data)){
            return;
        }

        $event->addMap('default_member_address', $data);
        return;
    }

    private function needDispatch()
    {
        return true;
    }

    private function saveExpressInfo()
    {
        $address = \YunShop::request()->get('address');
        /*$address =['address' => '云霄路188-1',
            'mobile' => '18545571024',
            'username' => '高启',
            'province' => '广东省',
            'city' => '广州市',
            'district' => '白云区',];*/
        $member_address = new MemberAddress($address);

        $order_address = new OrderAddress();

        $order_address->order_id = $this->event->getOrderModel()->id;
        $order_address->address = implode(' ', [$member_address->province, $member_address->city, $member_address->district, $member_address->address]);
        $order_address->mobile = $member_address->mobile;
        $order_address->realname = $member_address->username;
        $order_address->save();
        return true;
    }

    public function subscribe($events)
    {
        $events->listen(
            OnDispatchTypeInfoDisplayEvent::class,
            Express::class . '@onDisplay'
        );
        $events->listen(
            \app\common\events\order\AfterOrderCreatedEvent::class,
            Express::class . '@onSave'
        );
    }

}