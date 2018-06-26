<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/26
 * Time: 上午11:32
 */

namespace app\common\models;

use app\common\traits\HasProcessTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use app\common\exceptions\AppException;
use app\common\services\PayFactory;
use app\frontend\modules\order\services\OrderService;
use app\frontend\modules\payType\BasePayType;
use app\frontend\modules\payType\CreditPay;
use app\frontend\modules\payType\Remittance;
use app\frontend\modules\payment\managers\OrderPaymentTypeManager;

/**
 * Class OrderPay
 * @package app\common\models
 * @property int id
 * @property int uid
 * @property int status
 * @property string pay_sn
 * @property int pay_type_id
 * @property Carbon pay_time
 * @property Carbon refund_time
 * @property float amount
 * @property array order_ids
 * @property Collection orders
 * @property Collection payOrder
 * @property Collection allStatus
 * @property PayType payType
 * @property Member member
 * @property string pay_type_name
 * @property string status_name
 */
class OrderPay extends BaseModel
{
    use HasProcessTrait;

    public $table = 'yz_order_pay';
    protected $guarded = ['id'];
    protected $search_fields = ['pay_sn'];
    protected $casts = ['order_ids' => 'json'];
    protected $dates = ['pay_time', 'refund_time'];
    protected $appends = ['status_name', 'pay_type_name'];
    protected $attributes = [
        'status' => 0,
        'pay_type_id' => 0,
    ];
    const STATUS_UNPAID = 0;
    const STATUS_PAID = 1;
    const STATUS_REFUNDED = 2;

    /**
     * 根据paysn查询支付方式
     *
     * @param $pay_sn
     * @return mixed
     */
    public function get_paysn_by_pay_type_id($pay_sn)
    {
        return self::select('pay_type_id')
            ->where('pay_sn', $pay_sn)
            ->value('pay_type_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class,'uid');
    }

    public function getStatusNameAttribute()
    {
        return $this->allStatus[$this->status];
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function getAllStatusAttribute(){
        return collect([
            self::STATUS_UNPAID => '未支付',
            self::STATUS_PAID => '已支付',
            self::STATUS_REFUNDED => '已退款',
        ]);
    }
    public function getPayTypeNameAttribute()
    {
        return $this->payType->name;
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, (new OrderPayOrder)->getTable(), 'order_pay_id', 'order_id');
    }

    public function payType()
    {
        return $this->belongsTo(PayType::class);
    }

    public function getPaymentTypes()
    {
        /**
         * @var OrderPaymentTypeManager $orderPaymentTypeManager
         */
        $orderPaymentTypeManager = app('PaymentManager')->make('OrderPaymentTypeManager');
        $paymentTypes = $orderPaymentTypeManager->getOrderPaymentTypes($this);
        return $paymentTypes;
    }

    /**
     * 支付
     * @param int $payTypeId
     * @throws AppException
     */
    public function pay($payTypeId = null)
    {

        if (!is_null($payTypeId)) {
            $this->pay_type_id = $payTypeId;
        }
        $this->validate();

        $this->status = self::STATUS_PAID;
        $this->pay_time = time();
        $this->save();

        $this->orders->each(function ($order) {
            if (!OrderService::orderPay(['order_id' => $order->id, 'order_pay_id' => $this->id, 'pay_type_id' => $this->pay_type_id])) {
                throw new AppException('订单状态改变失败,请联系客服');
            }
        });
    }

    /**
     * 校验
     * @throws AppException
     */
    private function validate()
    {
        if (is_null($this->pay_type_id)) {
            throw new AppException('请选择支付方式');
        }
        if ($this->status > self::STATUS_UNPAID) {
            throw new AppException('(ID' . $this->id . '),此流水号已支付');
        }

        if ($this->orders->isEmpty()) {
            throw new AppException('(ID:' . $this->id . ')未找到对应订单');
        }

        $this->orders->each(function (\app\common\models\Order $order) {
            if ($order->status > Order::WAIT_PAY) {
                throw new AppException('(ID:' . $order->id . ')订单已付款,请勿重复付款');
            }
            if ($order->status == Order::CLOSE) {
                throw new AppException('(ID:' . $order->id . ')订单已关闭,无法付款');
            }
        });
    }

    public function applyPay()
    {
        return $this->getPayType()->applyPay();
    }
    public function payOrder(){
        return $this->hasMany(PayOrder::class,'out_order_no','pay_sn');
    }
    /**
     * 获取支付参数
     * @param int $payTypeId
     * @param array $payParams
     * @return mixed
     * @throws AppException
     */
    public function getPayResult($payTypeId = null, $payParams = [])
    {
        if (!is_null($payTypeId)) {
            $this->pay_type_id = $payTypeId;
        }
        $this->validate();
        // 从丁哥的接口获取统一的支付参数

        $query_str = $this->getPayType()->getPayParams($payParams);

        $pay = PayFactory::create($this->pay_type_id);
        $result = $pay->doPay($query_str, $this->pay_type_id);
        if (!isset($result)) {
            throw new AppException('获取支付参数失败');
        }
        return $result;
    }

    /**
     * 获取支付类型对象
     * @return BasePayType
     */
    private function getPayType()
    {
        if (!$this->payType instanceof BasePayType) {
            if ($this->pay_type_id == PayType::CREDIT) {
                $payType = CreditPay::find($this->pay_type_id);
            } elseif ($this->pay_type_id == PayType::REMITTANCE) {
                $payType = Remittance::find($this->pay_type_id);

            } else {
                $payType = BasePayType::find($this->pay_type_id);
            }
            /**
             * @var BasePayType $payType
             */
            $payType->setOrderPay($this);
            $this->setRelation('payType', $payType);
        }
        return $this->payType;
    }
}