<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/2/28
 * Time: 下午1:44
 */

namespace app\frontend\modules\goods\services\models;

use app\common\models\Goods;
use app\common\models\OrderGoods;

use app\frontend\modules\discount\services\DiscountService;
use app\frontend\modules\dispatch\services\DispatchService;
use app\frontend\modules\order\services\models\PreGeneratedOrderModel;


class PreGeneratedOrderGoodsModel extends OrderGoodsModel
{
    /**
     * app\frontend\modules\order\services\models\PreGeneratedOrderModel的实例
     * @var
     */
    private $Order;
    /**
     * app\common\models\Goods的实例
     * @var Goods
     */
    private $Goods;


    /**
     * PreGeneratedOrderGoodsModel constructor.
     * @param Goods $goods_model
     * @param int $total
     */
    public function __construct(Goods $goods_model, $total = 1)
    {
        $this->Goods = $goods_model;
        $this->total = $total;
        parent::__construct();

    }
    protected function setGoodsDiscount()
    {
        $this->GoodsDiscount = DiscountService::getPreOrderGoodsDiscountModel($this);
    }
    protected function setGoodsDispatch()
    {
        $this->GoodsDispatch = DispatchService::getPreOrderGoodsDispatchModel($this);
    }

    /**
     * 为订单model提供的方法 ,设置所属的订单model
     * @param PreGeneratedOrderModel $Order
     */
    public function setOrder(PreGeneratedOrderModel $Order)
    {
        $this->Order = $Order;

    }

    /**
     * 显示商品数据
     * @return array
     */
    public function toArray()
    {

        return $data = array(
            'goods_id' => $this->Goods->id,
            'goods_sn' => $this->Goods->goods_sn,
            'price' => $this->getPrice(),
            'total' => $this->total,
            'title' => $this->Goods->title,
            'thumb' => $this->Goods->thumb,
            'discount_details' => $this->getDiscountDetails(),
            'dispatch_details' => $this->getDispatchDetails(),

        );
        return $data;
    }

    /**
     * 获取商品数量
     * @return int
     */
    public function getTotal()
    {
        return $this->total;

    }
    public function getGoodsPrice()
    {
        return $this->total * $this->Goods->price;

    }
    /**
     * 订单商品插入数据库
     * @param PreGeneratedOrderModel|null $order_model
     * @return static
     */
    public function generate(PreGeneratedOrderModel $order_model = null)
    {
        if (isset($order_model)) {
            $this->setOrder($order_model);
        }

        $data = array(
            'uniacid' => $this->Order->getShopModel()->uniacid,
            'order_id' => $this->Order->id,
            'goods_id' => $this->Goods->id,
            'goods_sn' => $this->Goods->goods_sn,
            'uid' => $this->Order->getMemberModel()->uid,
            'goods_price' => $this->getGoodsPrice(),
            'price' => $this->getPrice(),
            'total' => $this->getTotal(),
            'title' => $this->Goods->title,
            'thumb' => $this->Goods->thumb,
            'discount_details' => $this->getDiscountDetails(),
            'dispatch_details' => $this->getDispatchDetails(),
        );
        echo '订单商品插入数据为';
        dd($data);
        //return;
        return OrderGoods::create($data);
    }

    /**
     * @param $name
     * @return null
     */
    //todo 在确认没有其他类调用后,删除这个方法
    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        return null;
    }
}