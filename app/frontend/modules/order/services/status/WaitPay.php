<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/2
 * Time: 下午4:55
 */

namespace app\frontend\modules\order\services\status;


use app\common\models\Order;

class WaitPay extends Status
{
    private $order;
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function getStatusName()
    {
        return '待付款';
    }

    public function getButtonModels()
    {
        $result =
            [
                [
                    'name' => '付款',
                    'api' => 'order.operation.pay',
                    'value' => static::PAY
                ],
                [
                    'name' => '取消订单',
                    'api' => 'order.operation.close',
                    'value' => static::CANCEL //todo
                ],
            ];
        return $result;
    }
}