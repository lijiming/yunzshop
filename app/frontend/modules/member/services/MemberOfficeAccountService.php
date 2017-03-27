<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 17/2/22
 * Time: 下午4:44
 */

namespace app\frontend\modules\member\services;

use app\common\models\MemberGroup;
use app\common\models\MemberLevel;
use app\frontend\models\McGroupsModel;
use app\frontend\modules\member\models\McMappingFansModel;
use app\frontend\modules\member\models\MemberModel;
use app\frontend\modules\member\models\MemberUniqueModel;
use app\frontend\modules\member\models\SubMemberModel;

class MemberOfficeAccountService extends MemberService
{
    const LOGIN_TYPE    = '1';

    public function __construct()
    {}

    public function login()
    {
        $uniacid      = \YunShop::app()->uniacid;
        $code         = \YunShop::request()->code;
        $mid          = \YunShop::app()->uniacid ? \YunShop::app()->uniacid : 0;

        $appId        = \YunShop::app()->account['key'];
        $appSecret    = \YunShop::app()->account['secret'];

        $callback     = \YunShop::app()->siteroot . 'app/index.php?' . $_SERVER['QUERY_STRING'];

        $authurl = $this->_getAuthUrl($appId, $callback);
        $tokenurl = $this->_getTokenUrl($appId, $appSecret, $code);

        if (!empty($code)) {
            $redirect_url = session('client_url', $callback);

            $resp     = @ihttp_get($tokenurl);
            $token    = @json_decode($resp['content'], true);

            if (!empty($token) && !empty($token['errmsg']) && $token['errmsg'] == 'invalid code') {
               return show_json(0, array('msg'=>'请求错误'));
            }

            $userinfo_url = $this->_getUserInfoUrl($token['access_token'], $token['openid']);
            $resp_info = @ihttp_get($userinfo_url);
            $userinfo    = @json_decode($resp_info['content'], true);

            if (is_array($userinfo) && !empty($userinfo['unionid'])) {
                $UnionidInfo = MemberUniqueModel::getUnionidInfo($uniacid, $userinfo['unionid'])->first();

                if (!empty($UnionidInfo)) {
                    $UnionidInfo = $UnionidInfo->toArray();
                }

                if (!empty($UnionidInfo['unionid'])) {
                    $types = explode('|', $UnionidInfo['type']);
                    $member_id = $UnionidInfo['member_id'];

                    if (!in_array(self::LOGIN_TYPE, $types)) {
                        //更新ims_yz_member_unique表
                        MemberUniqueModel::updateData(array(
                            'unique_id'=>$UnionidInfo['unique_id'],
                            'type' => $UnionidInfo['type'] . '|' . self::LOGIN_TYPE
                        ));
                    }

                    //更新mc_members
                    $mc_data = array(
                        'nickname' => stripslashes($userinfo['nickname']),
                        'avatar' => $userinfo['headimgurl'],
                        'gender' => $userinfo['sex'],
                        'nationality' => $userinfo['country'],
                        'resideprovince' => $userinfo['province'] . '省',
                        'residecity' => $userinfo['city'] . '市'
                    );
                    MemberModel::updataData($UnionidInfo['member_id'], $mc_data);

                    //更新mapping_fans
                    $record = array(
                        'openid' => $userinfo['openid'],
                        'nickname' => stripslashes($userinfo['nickname']),
                        'tag' => base64_encode(iserializer($userinfo))
                    );
                    McMappingFansModel::updateData($UnionidInfo['member_id'], $record);
                } else {
                    //添加mc_members表
                    $default_groupid = McGroupsModel::getDefaultGroupId();

                    $mc_data = array(
                        'uniacid' => $uniacid,
                        'email' => '',
                        'groupid' => $default_groupid['groupid'],
                        'createtime' => TIMESTAMP,
                        'nickname' => stripslashes($userinfo['nickname']),
                        'avatar' => $userinfo['headimgurl'],
                        'gender' => $userinfo['sex'],
                        'nationality' => $userinfo['country'],
                        'resideprovince' => $userinfo['province'] . '省',
                        'residecity' => $userinfo['city'] . '市',
                        'salt' => '',
                        'password' => ''
                    );
                    $member_id = MemberModel::insertData($mc_data);

                    //添加yz_member表
                    $default_sub_group_id = MemberGroup::getDefaultGroupI()->first();
                    $default_sub_level_id = MemberLevel::getDefaultLevelId()->first();
                    if (!empty($default_sub_group_id)) {
                        $default_subgroup_id = $default_sub_group_id->id;
                    } else {
                        $default_subgroup_id = 0;
                    }

                    if (!empty($default_sub_level_id)) {
                        $default_sublevel_id = $default_sub_level_id->id;
                    } else {
                        $default_sublevel_id = 0;
                    }

                    $sub_data = array(
                        'member_id' => $member_id,
                        'uniacid' => $uniacid,
                        'agent_id' => $mid,
                        'group_id' => $default_subgroup_id,
                        'level_id' => $default_sublevel_id,
                    );
                    SubMemberModel::insertData($sub_data);

                    //添加mapping_fans表
                    $record = array(
                        'openid' => $userinfo['openid'],
                        'uid' => $member_id,
                        'acid' => $uniacid,
                        'uniacid' => $uniacid,
                        'salt' => random(8),
                        'updatetime' => TIMESTAMP,
                        'nickname' => stripslashes($userinfo['nickname']),
                        'follow' => 1,
                        'followtime' => time(),
                        'unfollowtime' => 0,
                        'tag' => base64_encode(iserializer($userinfo))
                    );
                    McMappingFansModel::insertData($record);


                    //添加ims_yz_member_unique表
                    MemberUniqueModel::insertData(array(
                        'uniacid' => $uniacid,
                        'unionid' => $userinfo['unionid'],
                        'member_id' => $member_id,
                        'type' => self::LOGIN_TYPE
                    ));
                }

                session()->put('member_id',$member_id);
            } else {
                redirect($authurl)->send();
                exit;
            }
        } else {
            echo '<pre>';print_r($_SERVER);exit;
            //客户端请求地址
            $client_url   = $_SERVER['HTTP_REFERER'];

            session()->put('client_url',$client_url);
            redirect($authurl)->send();
            exit;
        }
        //$redirect_url;
        redirect('test.yunzshop.com/app/index.php?i=2&c=entry&do=shop&m=sz_yi&route=member.test.login')->send();
    }

    /**
     * 授权 api
     *
     * snsapi_base/snsapi_userinfo
     *
     * @param $appId
     * @param $url
     * @return string
     */
    private function _getAuthUrl($appId, $url)
    {
       return "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appId . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
    }

    /**
     * 获取token api
     *
     * @param $appId
     * @param $appSecret
     * @param $code
     * @return string
     */
    private function _getTokenUrl($appId, $appSecret, $code)
    {
       return "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appId . "&secret=" . $appSecret . "&code=" . $code . "&grant_type=authorization_code";
    }

    /**
     * 获取用户信息 api
     * @param $accesstoken
     * @param $openid
     * @return string
     */
    private function _getUserInfoUrl($accesstoken, $openid)
    {
        return "https://api.weixin.qq.com/sns/userinfo?access_token={$accesstoken}&openid={$openid}&lang=zh_CN";
    }
}