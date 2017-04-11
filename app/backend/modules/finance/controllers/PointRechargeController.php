<?php
/**
 * Created by PhpStorm.
 * User: yangyang
 * Date: 2017/4/11
 * Time: 上午10:23
 */

namespace app\backend\modules\finance\controllers;


use app\backend\modules\finance\services\PointService;
use app\backend\modules\member\models\Member;
use app\common\components\BaseController;
use app\common\helpers\Url;

class PointRechargeController extends BaseController
{
    public function index()
    {
        $member_id = \YunShop::request()->id;

        $member = Member::getMemberInfoById($member_id);

        $point = \YunShop::request()->point;
        if ($point) {
            $data = [
                'point_income_type' => 1,
                'point_mode'        => 5,
                'member_id'         => $member_id,
                'point'             => $point,
                'remark'            => '后台充值积分',
                'uniacid'           => \YunShop::app()->uniacid
            ];
            $point_service = new PointService($data);
            $point_model = $point_service->changePoint();
            if ($point_model) {
                return $this->message('充值成功!', Url::absoluteWeb('finance.point-recharge'));
            }
        }

        return view('finance.point.point_recharge', [
            'memberInfo'    => $member,
            'rechargeMenu'  => $this->getRechargeMenu()
        ])->render();
    }

    private function getRechargeMenu()
    {
        return array(
            'title'     => '积分充值',
            'name'      => '粉丝',
            'profile'   => '会员信息',
            'old_value' => '当前积分',
            'charge_value' => '充值积分',
            'type'      => 'balance'
        );
    }
}