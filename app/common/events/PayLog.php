<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2017/3/28
 * Time: 下午8:40
 */

namespace app\common\events;


class PayLog extends Event
{
    protected $pay_request_params;

    protected $pay;

    public function __construct($params, PayLog $pay)
    {
        $this->pay_request_params = $params;

        $this->pay = $pay;
    }

    /**
     * 支付请求参数
     * @return mixed
     */
    public function getPayRequestParams()
    {
        return $this->pay_request_params;
    }

    public function getPayObject()
    {
        return $this->pay;
    }
}