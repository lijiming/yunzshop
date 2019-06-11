<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/4/24
 * Time: 下午3:10
 */

namespace app\payment\controllers;

use app\common\helpers\Url;
use app\common\models\AccountWechats;
use app\common\services\Pay;
use app\payment\PaymentController;
use Yunshop\ConvergePay\services\NotifyService;
use app\common\events\withdraw\WithdrawSuccessEvent;

class ConvergepayController extends PaymentController
{
    private $attach = [];
    private $parameter = [];

    public function __construct()
    {
        parent::__construct();

        $this->parameter = $_GET;

        if (empty(\YunShop::app()->uniacid)) {
            $this->attach = explode(':', $_GET['r2_OrderNo']);

            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->attach[1];

            AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));
        }
    }

    public function notifyUrlWechat()
    {
        $this->log($this->parameter, '微信支付-HJ');

        if($this->getSignResult()) {
            if ($_GET['r6_Status'] == '100') {
                \Log::debug('------微信支付-HJ 验证成功-----');

                $data = $this->data('微信支付-HJ', '28');

                $this->payResutl($data);
                \Log::debug('----微信支付-HJ 结束----');

                echo 'success';
            } else {
                //其他错误
                \Log::debug('------微信支付-HJ 其他错误-----');
                echo 'fail';
            }
        } else {
            //签名验证失败
            \Log::debug('------微信支付-HJ 签名验证失败-----');
            echo 'fail1';
        }
    }

    public function returnUrlWechat()
    {
        $trade = \Setting::get('shop.trade');

        if (!is_null($trade) && isset($trade['redirect_url']) && !empty($trade['redirect_url'])) {
            return redirect($trade['redirect_url'])->send();
        }

        if (0 == $_GET['state'] && $_GET['errorDetail'] == '成功') {
            redirect(Url::absoluteApp('member/payYes', ['i' => $_GET['attach']]))->send();
        } else {
            redirect(Url::absoluteApp('member/payErr', ['i' => $_GET['attach']]))->send();
        }
    }

    public function notifyUrlAlipay()
    {
        $this->log($this->parameter, '支付宝支付-HJ');

        if($this->getSignResult()) {
            if ($_GET['r6_Status'] == '100') {
                \Log::debug('------支付宝支付-HJ 验证成功-----');

                $data = $this->data('支付宝支付', '29');
                $this->payResutl($data);

                \Log::debug('----支付宝支付-HJ 结束----');
                echo 'success';
            } else {
                //其他错误
                \Log::debug('------支付宝支付-HJ 其他错误-----');
                echo 'fail';
            }
        } else {
            //签名验证失败
            \Log::debug('------支付宝支付-HJ 签名验证失败-----');
            echo 'fail1';
        }
    }

    /**
     * 签名验证
     *
     * @return bool
     */
    public function getSignResult()
    {
        $pay = \Setting::get('plugin.convergePay_set');

        $notify = new NotifyService();
        $notify->setKey($pay['hmacVal']);

        return $notify->verifySign();
    }

    /**
     * 支付日志
     *
     * @param $data
     * @param $sign
     */
    public function log($data, $sign)
    {
        $orderNo = explode('HJ', $data['orderNo']);
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog($orderNo[0], $sign, json_encode($data));
    }

    /**
     * 支付回调参数
     *
     * @param $pay_type_id
     * @return array
     */
    public function data($pay_type, $pay_type_id)
    {
        $data = [
            'total_fee'    => floatval($this->parameter['r3_Amount']),
            'out_trade_no' => $this->attach[0],
            'trade_no'     => $this->parameter['r7_TrxNo'],
            'unit'         => 'yuan',
            'pay_type'     => $pay_type,
            'pay_type_id'  => $pay_type_id
        ];

        return $data;
    }

    /**
     * 提现回调
     *
     */
    public function notifyUrlWithdraw()
    {
        $parameter = $_POST;

        $orderNo = explode(':', $parameter['merchantOrderNo']);
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog($orderNo[0], '汇聚提现', json_encode($parameter));

        if($this->checkWithdrawHmac($parameter)) {
            if ($parameter['status'] == '205') {
                \Log::debug('------汇聚打款 成功-----');

                event(new WithdrawSuccessEvent($orderNo[1]));

                \Log::debug('----汇聚打款 结束----');

                return [
                    'statusCode' => 2001,
                    'message' => "成功"
                ];
            } else {
                //其他错误
                \Log::debug('------汇聚打款 '.$parameter['errorCodeDesc'].'-----');
                return [
                    'statusCode' => 2002,
                    'message'    => "受理失败",
                    'errorCode'  => $parameter['errorCode'],
                    'errorDesc'  => $parameter['errorCodeDesc']
                ];
            }
        } else {
            //签名验证失败
            \Log::debug('------汇聚打款 签名验签失败-----');
            return [
                'statusCode' => 2002,
                'message'    => "受理失败",
                'errorCode'  => '300002017',
                'errorDesc'  => '签名验签失败'
            ];
        }
    }

    /**
     * 验证提现签名
     *
     * @param $parameter
     * @return bool
     */
    public function checkWithdrawHmac($parameter)
    {
        $setting = \Setting::get('plugin.convergePay_set');

        return $parameter['hmac'] == md5($parameter['status'] . $parameter['errorCode'] . $parameter['errorCodeDesc'] . $parameter['userNo']
            . $parameter['merchantOrderNo'] . $parameter['platformSerialNo'] . $parameter['receiverAccountNoEnc']
            . $parameter['receiverNameEnc'] . $parameter['paidAmount'] . $parameter['fee'] . $setting['hmacVal']);
    }
}