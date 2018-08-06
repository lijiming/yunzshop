<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/1
 * Time: 下午5:01
 */

namespace app\frontend\modules\order\operations;

use app\frontend\models\Order;

abstract class OrderOperation
{
    const PAY = 1; // 支付
    const COMPLETE = 5; // 确认收货
    const EXPRESS = 8; // 查看物流
    const CANCEL = 9; // 取消订单
    const COMMENT = 10; // 评论
    const DELETE = 12; // 删除订单
    const REFUND = 13; // 申请退款
    const REFUND_INFO = 18; // 已退款/退款中
    const COMMENTED = 19; // 已评价
    const STORE_PAY = 20; // 确认核销(核销员)
    const REMITTANCE_RECORD = 21; // 转账信息

    /**
     * @var Order
     */
    protected $order;
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
    abstract public function enable();

    /**
     * @return string
     */
    abstract public function getName();
    /**
     * @return string
     */
    abstract public function getValue();
}