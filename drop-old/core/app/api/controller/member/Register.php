<?php
namespace app\api\controller\member;
@session_start();
use app\api\Request;
use app\api\YZ;
class Register extends YZ
{

    public function index()
    {
        $validate_messages = $this->_validatePara();
        if (!empty($validate_messages)) {
            $this->returnError($validate_messages);
        }

        $para = $this->getPara();

        $code_message = $this->_checkCode($para['code'],$para['mobile']);
        if(!empty($code_message)){
            $this->returnError($code_message);
        }
        if (D('Member')->has(array_part('mobile,uniacid',$para))) {
            $this->returnError('该手机号已被注册！');
        }
        if(Request::has('referral') && D("Sysset")->appReferral($para['uniacid'])){
            $referral = D('Member')->has(array(
                'uniacid' => $para['uniacid'],
                'referralsn' =>$para['referralsn']
            ));
            if (empty($referral)) {
                $this->returnError('推荐码无效！');
            }
        }
        $open_id = $this->_createMember($para['uniacid'],$para['mobile'],$para['pwd']);
        if(!empty($referral)){
            $this->_saveReferralInfo($para['uniacid'],$para['mobile'],$referral);
        }
        $this->_setSessionAndCookie($para['uniacid'],$para['mobile'],$open_id);

        $this->returnSuccess();
    }
    private function _validatePara(){
        $validate_fields = array(
            'mobile' => array(
                'type' => '',
                'describe' => '手机号'
            ),
            'pwd' => array(
                'type' => '',
                'describe' => '密码'
            ),
            'uniacid' => array(
                'type' => '',
                'describe' => '公众号id'
            ),
            'code' => array(
                'type' => '',
                'describe' => '公众号id'
            ),
            'referral' => array(
                'required' => false,
                'type' => '',
                'describe' => '推荐码'
            ),
        );
        Request::filter($validate_fields);
        $validate_messages = Request::validate($validate_fields);
        return $validate_messages;
    }
    private function _saveReferralInfo($uniacid,$mobile,$referral){
        $member = pdo_fetch('select * from ' . tablename('sz_yi_member') . ' where mobile=:mobile and pwd!="" and uniacid=:uniacid limit 1', array(
            ':uniacid' => $uniacid,
            ':mobile' => $mobile
        ));
        if (!$member['agentid']) {
            $m_data = array(
                'agentid' => $referral['id'],
                'agenttime' => time(),
                'status' => 1,
                'isagent' => 1
            );
            if($referral['id'] != 0){
                $this->upgradeLevelByAgent($referral['id']);
            }
            pdo_update('sz_yi_member', $m_data, array("mobile" => $mobile, "uniacid" => $uniacid));
            m('member')->responseReferral($this->yzShopSet, $referral, $member);
        }
    }
    private function _setSessionAndCookie($uniacid,$mobile,$openid){
        $lifeTime = 24 * 3600 * 3;
        session_set_cookie_params($lifeTime);
        @session_start();
        $cookieid = "__cookie_sz_yi_userid_{$uniacid}";
        setcookie('member_mobile', $mobile);
        setcookie($cookieid, base64_encode($openid));
    }
    private function _createMember($uniacid,$mobile,$password){
        $openid = pdo_fetchcolumn('select openid from ' . tablename('sz_yi_member') . ' where mobile=:mobile and uniacid=:uniacid limit 1', array(
            ':uniacid' => $uniacid,
            ':mobile' => $mobile
        ));
        if (empty($openid)) {
            $member_data = array(
                'uniacid' => $uniacid,
                'uid' => 0,
                'openid' => 'u'.md5($mobile),
                'mobile' => $mobile,
                'pwd' => md5($password),   //md5
                'createtime' => time(),
                'status' => 0,
                'regtype' => 2,
            );

            if (is_app()) {
                $member_data['bindapp'] = 1;
            }

            if (!is_weixin()) {
                $member_data['nickname'] = $mobile;
                $member_data['avatar'] = "http://".$_SERVER ['HTTP_HOST']. '/addons/sz_yi/template/mobile/default/static/images/photo-mr.jpg';
            }

            pdo_insert('sz_yi_member', $member_data);
            $openid = $member_data['openid'];
        } else {
            $member_data = array(
                'pwd' => md5($password),   //md5
                'regtype' => 1,
                'isbindmobile' => 1
            );
            pdo_update('sz_yi_member', $member_data, array("mobile" => $mobile, "uniacid" => $uniacid));
        }
        return $openid;
    }
    private function _checkCode($code,$mobile){
        $message = '';
        if (($_SESSION['codetime']+60*5) < time()) {
            $message = '验证码已过期,请重新获取';
        }
        if ($_SESSION['code'] != $code) {
            $message = '验证码错误,请重新获取';
        }
        if ($_SESSION['code_mobile'] != $mobile) {
            $message = '注册手机号与验证码不匹配！';
        }
        return $message;
    }

    private function _setCookie($openid,$mobile){
        global $_W;
        //var_dump($_W['uniacid']);
        if (is_app()) {
            $lifeTime = 24 * 3600 * 3 * 100;
        } else {
            $lifeTime = 24 * 3600 * 3;
        }
        session_set_cookie_params($lifeTime);
        $cookieid = "__cookie_sz_yi_userid_{$_W['uniacid']}";
        if (is_app()) {
            setcookie($cookieid, base64_encode($openid), time()+3600*24*7);
        } else {
            setcookie($cookieid, base64_encode($openid));
        }
        setcookie('member_mobile', $mobile);
    }

    /**
     * 小程序登陆
     */
    public function wx_app_login()
    {
        \sy_debug\Debug::__init();
        include "./addons/sz_yi/core/inc/plugin/vendor/wechat/wxBizDataCrypt.php";
        include "./framework/model/mc.mod.php";

        global $_W;

        session_start();
        load()->func('communication');

        $setdata = pdo_fetch("select * from " . tablename('sz_yi_wxapp') . ' where uniacid=:uniacid limit 1', array(
            ':uniacid' => $_W['uniacid']
        ));

        $appid = $setdata['appid'];
        $secret = $setdata['secret'];

        $para = $this->getPara();

        $data = array(
            'appid' => $appid,
            'secret' => $secret,
            'js_code' => $para['code'],
            'grant_type' => 'authorization_code',
        );

        $url = 'https://api.weixin.qq.com/sns/jscode2session';
        $res = ihttp_request($url, $data);

        $user_info = json_decode($res['content'], true);

        $data = '';  //json

        if (!empty($para['info'])) {
            $json_data = json_decode($para['info'], true);

            $pc = new \WXBizDataCrypt($appid, $user_info['session_key']);
            $errCode = $pc->decryptData($json_data['encryptedData'], $json_data['iv'], $data);
        }

        if ($errCode == 0) {
            $json_user = json_decode($data, true);
        } else {
            $this->returnError('登录认证失败');
        }
        $fan = mc_fansinfo($json_user['openId']);

        if (empty($fan)) {
            $member_info = $this->regitsterAccount($json_user);
        }

        //小程序session处理
        $random = $this->wx_app_session($user_info);

        $result = array('session' => $random, 'wx_token' =>session_id(), 'uid' => $member_info['uid']);

        $this->returnSuccess($result);
    }

    private function _createAppMember($uniacid,$openid){
        $member_data = array(
            'uniacid' => $uniacid,
            'uid' => 0,
            'openid' => $openid,
            'mobile' => '',
            'pwd' => md5(''),   //md5
            'createtime' => time(),
            'status' => 0,
            'regtype' => 2,
        );

        if (is_app()) {
            $member_data['bindapp'] = 1;
        }

        if (!is_weixin()) {
            $member_data['nickname'] = $openid;
            $member_data['avatar'] = "http://".$_SERVER ['HTTP_HOST']. '/addons/sz_yi/template/mobile/default/static/images/photo-mr.jpg';
        }

        pdo_insert('sz_yi_member', $member_data);
        $openid = $member_data['openid'];

    }

    /**
     * 注册用户
     *
     * mc_members mapping_fans sz_yi_member
     *
     * @param $json_user
     */
    public function regitsterAccount($json_user)
    {
        global $_W;

        if(is_error($json_user) && empty($json_user)) {
            $this->returnError('登录认证失败！');
        }

        $record = array(
            'openid' => $json_user['openId'],
            'uid' => 0,
            'acid' => $_W['acid'],
            'uniacid' => $_W['uniacid'],
            'salt' => random(8),
            'updatetime' => TIMESTAMP,
            'unionid' => $json_user['unionId'],
            'nickname' => stripslashes($json_user['nickName']),
            'follow' => 0,
            'followtime' => '',
            'unfollowtime' => 0,
            'tag' => base64_encode(iserializer($json_user))
        );

        if (!isset($unisetting['passport']) || empty($unisetting['passport']['focusreg'])) {
            $default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' .tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $_W['uniacid']));
            $data = array(
                'uniacid' => $_W['uniacid'],
                'email' => md5($json_user['openId']).'@yunzshop.com',
                'groupid' => $default_groupid,
                'createtime' => TIMESTAMP,
                'nickname' => stripslashes($json_user['nickName']),
                'avatar' => $json_user['avatarUrl'],
                'gender' => $json_user['gender'],
                'nationality' => $json_user['country'],
                'resideprovince' => $json_user['province'] . '省',
                'residecity' => $json_user['city'] . '市',
            );

            $data['salt']  = random(8);

            $data['password'] = md5($data['email'] . $data['salt'] . $_W['config']['setting']['authkey']);

            //mc_members
            pdo_insert('mc_members', $data);
            $uid = pdo_insertid();
            $record['uid'] = $uid;
            $_SESSION['uid'] = $uid;
        }

        //mc_mapping_fans
        pdo_insert('mc_mapping_fans', $record);
        $_SESSION['openid'] = $record['openid'];
        $_W['fans'] = $record;
        $_W['fans']['from_user'] = $record['openid'];

        //sz_yi_member
        $member = array(
            'uniacid' => $_W['uniacid'],
            'uid' => $uid,
            'openid' => $json_user['openId'],
            'realname' =>  '',
            'mobile' => '',
            'nickname' => $json_user['nickName'],
            'avatar' => $json_user['avatarUrl'],
            'gender' => $json_user['gender'],
            'province' => $json_user['province'],
            'city' => $json_user['city'],
            'area' => '',
            'createtime' => time(),
            'status' => 1
        );

        pdo_insert('sz_yi_member', $member);
        return $member;
    }

    /**
     * 小程序登录态
     *
     * @param $user_info
     * @return string
     */
    function wx_app_session($user_info)
    {
        if (empty($user_info['session_key']) || empty($user_info['openid'])) {
            $this->returnError('登录认证失败！');
        }

        $random = md5(uniqid(mt_rand()));

        $_SESSION['wx_app'] = array($random => iserializer(array('session_key'=>$user_info['session_key'], 'openid'=>$user_info['openid'])));

        return $random;
    }
}