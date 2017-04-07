<?php
//芸众商城 QQ:913768135
if (!defined('IN_IA')) {
	exit('Access Denied');
}
require IA_ROOT . '/addons/sz_yi/defines.php';
require SZ_YI_INC . 'plugin/plugin_processor.php';

class CreditshopProcessor extends PluginProcessor
{
	public function __construct()
	{
		parent::__construct('creditshop');
	}

	public function respond($obj = null)
	{
		global $_W;
		$_var_1 = $obj->message;
		$from = $obj->message['from'];
		$content = $obj->message['content'];
		$msgtype = strtolower($_var_1['msgtype']);
		$event = strtolower($_var_1['event']);
		if ($msgtype == 'text' || $event == 'click') {
			$saler = pdo_fetch('select * from ' . tablename('sz_yi_saler') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $from));
			if (empty($saler)) {
				return $this->responseEmpty();
			}
			if (!$obj->inContext) {
				$obj->beginContext();
				return $obj->respText('请输入兑换码:');
			} else if ($obj->inContext && is_numeric($content)) {
				$creditshop = pdo_fetch('select * from ' . tablename('sz_yi_creditshop_log') . ' where eno=:eno and uniacid=:uniacid  limit 1', array(':eno' => $content, ':uniacid' => $_W['uniacid']));
				if (empty($creditshop)) {
					return $obj->respText('未找到要兑换码,请重新输入!');
				}
				$_var_8 = $creditshop['id'];
				if (empty($creditshop)) {
					return $obj->respText('未找到要兑换码,请重新输入!');
				}
				if (empty($creditshop['status'])) {
					return $obj->respText('无效兑换记录!');
				}
				if ($creditshop['status'] >= 3) {
					return $obj->respText('此记录已兑换过了!');
				}
				$member = m('member')->getMember($creditshop['openid']);
				$goods = $this->model->getGoods($creditshop['goodsid'], $member);
				if (empty($goods['id'])) {
					return $obj->respText('商品记录不存在!');
				}
				if (empty($goods['isverify'])) {
					$obj->endContext();
					return $obj->respText('此商品不支持线下兑换!');
				}
				if (!empty($goods['type'])) {
					if ($creditshop['status'] <= 1) {
						return $obj->respText('未中奖，不能兑换!');
					}
				}
				if ($goods['money'] > 0 && empty($creditshop['paystatus'])) {
					return $obj->respText('未支付，无法进行兑换!');
				}
				if ($goods['dispatch'] > 0 && empty($creditshop['dispatchstatus'])) {
					return $obj->respText('未支付运费，无法进行兑换!');
				}
				$storeids = explode(',', $goods['storeids']);
				if (!empty($storeids)) {
					if (!empty($saler['storeid'])) {
						if (!in_array($saler['storeid'], $storeids)) {
							return $obj->respText('您无此门店的兑换权限!');
						}
					}
				}
				$time = time();
				pdo_update('sz_yi_creditshop_log', array('status' => 3, 'usetime' => $time, 'verifyopenid' => $from), array('id' => $creditshop['id']));
				$this->model->sendMessage($_var_8);
				$obj->endContext();
				return $obj->respText('兑换成功!');
			}
		}
	}

	private function responseEmpty()
	{
		ob_clean();
		ob_start();
		echo '';
		ob_flush();
		ob_end_flush();
		exit(0);
	}
}