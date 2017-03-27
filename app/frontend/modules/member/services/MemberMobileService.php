<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 下午4:12
 */

namespace app\frontend\modules\member\services;

use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\member\models\MemberModel;

class MemberMobileService extends MemberService
{
    public function login()
    {
        $memberdata= \YunShop::request()->memberdata;
        $mobile   = $memberdata['mobile'];
        $password = $memberdata['password'];

        $uniacid  = \YunShop::app()->uniacid;

        if (\YunShop::app()->ispost
                                  && MemberService::validate($mobile, $password)) {
            $has_mobile = MemberModel::checkMobile($uniacid, $mobile);

            if (!empty($has_mobile)) {
                $password = md5($password. $has_mobile['salt'] . \YunShop::app()->config['setting']['authkey']);

                $member_info = MemberModel::getUserInfo($uniacid, $mobile, $password)->first();

            } else {
                return show_json(0, "用户不存在");
            }

            if(!empty($member_info)){
                $this->save($member_info, $uniacid);
            } else{
                return show_json(0, "手机号或密码错误");
            }
        } else {
            return show_json(-1, "手机号或密码错误");
        }

    }


}