<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/20
 * Time: 下午3:35
 */

namespace app\frontend\modules\goods\services\models;


use app\frontend\modules\discount\services\DiscountService;
use app\frontend\modules\dispatch\services\DispatchService;
use app\frontend\models\OrderGoods;

class CreatedOrderGoodsModel extends OrderGoodsModel
{
    private $_OrderGoods;

    public function __construct($order, $total = 1)
    {
        $this->_OrderGoods = $order;
        $this->total = $this->_OrderGoods->total;
        parent::__construct();
    }
    public function getOrderGoods(){
        return $this->_OrderGoods;
    }

    protected function getTotal(){
        return $this->_OrderGoods->total;
    }
    public function addChangePriceInfo($price)
    {
        $change_price = $price - $this->_OrderGoods->price;

        $detail = [
            'name' => '订单商品改价',
            'value' => "{$this->_OrderGoods->price}->{$price}",
            'price' => (string)$change_price,
            'plugin' => '0',
        ];
        $this->goodsDiscount->addDiscountDetail($detail);
    }

    public function getGoodsPrice()
    {
        return $this->total * $this->_OrderGoods->goods_price;
    }
    public function getGoodsId(){
        return $this->_OrderGoods->goods_id;
    }
    public function update()
    {
        $data = array(
            'goods_price' => $this->getGoodsPrice(),
            'discount_price' => $this->getDiscountPrice(),
            'price' => $this->getPrice(),
            'total' => $this->getTotal(),
        );
        OrderGoods::save($data);
    }
    protected function getDiscountPrice()
    {
        return $this->_OrderGoods->discount_price;

    }
}