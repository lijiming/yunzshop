<?php
global $_W, $_GPC;
$openid = m('user')->getOpenid();
if ($_W['isajax']) {
	$level = $this->set['level'];
	$member = $this->model->getInfo($openid, array('ok'));
	$time = time();
	$day_times = intval($this->set['settledays']) * 3600 * 24;
	$commission_ok = $member['commission_ok'];
	$cansettle = $commission_ok >= floatval($this->set['withdraw']);
	$member['commission_ok'] = number_format($commission_ok, 2);
	if ($_W['ispost']) {
		$orderids = array();
<<<<<<< HEAD
		//取代理商分红订单
        $sql = "select o.id from " . tablename('sz_yi_order') . " o  left join  " . tablename('sz_yi_bonus_goods') . " cg on cg.orderid=o.id  where cg.mid ={$member['id']} and o.status>=3  and cg.status=0 and ({$time} - o.createtime > {$day_times}) and o.uniacid=:uniacid";
        $orders = pdo_fetchall($sql, array(':uniacid' => $_W['uniacid']));
        foreach ($orders as $o) {
            if (empty($o['id'])) {
                continue;
            }
            $orderids[] = array('orderid' => $o['id'], 'level' => 1);
        }
        //可提现

		$applyno = m('common')->createNO('commission_apply', 'applyno', 'CA');

		$apply = array(
			'uniacid' => $_W['uniacid'], 
			'applyno' => $applyno, 
			'orderids' => iserializer($orderids), 
			'mid' => $member['id'], 
			'commission' => $commission_ok, 
			'type' => intval($_GPC['type']), 
			'status' => 1, 
			'applytime' => $time
			);
		pdo_insert('sz_yi_bonus_apply', $apply);

=======
		if ($level >= 1) {
			$level1_orders = pdo_fetchall('select distinct o.id from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . " where o.agentid=:agentid and o.status>=3  and og.status1=0 and og.nocommission=0 and ({$time} - o.createtime > {$day_times}) and o.uniacid=:uniacid  group by o.id", array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));
			if(p('hotel')){
			$level1_orders = pdo_fetchall('select distinct o.id from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . " where o.agentid=:agentid and o.status>=3  and o.status<>4 and o.status<>5 and o.status<>6 and og.status1=0 and og.nocommission=0 and ({$time} - o.createtime > {$day_times}) and o.uniacid=:uniacid  group by o.id", array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));
			}
			foreach ($level1_orders as $o) {
				if (empty($o['id'])) {
					continue;
				}
				$orderids[] = array('orderid' => $o['id'], 'level' => 1);
			}
		}
		if ($level >= 2) {
			if ($member['level1'] > 0) {
				$level2_orders = pdo_fetchall('select distinct o.id from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . " where o.agentid in( " . implode(',', array_keys($member['level1_agentids'])) . ")  and o.status>=3  and og.status2=0 and og.nocommission=0 and ({$time} - o.createtime > {$day_times}) and o.uniacid=:uniacid  group by o.id", array(':uniacid' => $_W['uniacid']));
				if(p('hotel')){
				$level2_orders = pdo_fetchall('select distinct o.id from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . " where o.agentid in( " . implode(',', array_keys($member['level1_agentids'])) . ")  and o.status>=3 and o.status<>4 and o.status<>5 and o.status<>6  and og.status2=0 and og.nocommission=0 and ({$time} - o.createtime > {$day_times}) and o.uniacid=:uniacid  group by o.id", array(':uniacid' => $_W['uniacid']));
				}
				foreach ($level2_orders as $o) {
					if (empty($o['id'])) {
						continue;
					}
					$orderids[] = array('orderid' => $o['id'], 'level' => 2);
				}
			}
		}
		if ($level >= 3) {
			if ($member['level2'] > 0) {
				$level3_orders = pdo_fetchall('select distinct o.id from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . " where o.agentid in( " . implode(',', array_keys($member['level2_agentids'])) . ")  and o.status>=3  and  og.status3=0 and og.nocommission=0 and ({$time} - o.createtime > {$day_times})   and o.uniacid=:uniacid  group by o.id", array(':uniacid' => $_W['uniacid']));
				if(p('hotel')){
				$level3_orders = pdo_fetchall('select distinct o.id from ' . tablename('sz_yi_order') . ' o ' . ' left join  ' . tablename('sz_yi_order_goods') . ' og on og.orderid=o.id ' . " where o.agentid in( " . implode(',', array_keys($member['level2_agentids'])) . ")  and o.status>=3  and o.status<>4 and o.status<>5 and o.status<>6  and  og.status3=0 and og.nocommission=0 and ({$time} - o.createtime > {$day_times})   and o.uniacid=:uniacid  group by o.id", array(':uniacid' => $_W['uniacid']));
				}
				foreach ($level3_orders as $o) {
					if (empty($o['id'])) {
						continue;
					}
					$orderids[] = array('orderid' => $o['id'], 'level' => 3);
				}
			}
		}
		$time = time();
>>>>>>> d636c4e8c1de97d7ae516c16c5da9ec8db4d3930
		foreach ($orderids as $o) {
			pdo_update('sz_yi_bonus_goods', array('status' => 1, 'applytime' => $time), array('mid' => $member['id'], 'orderid' => $o['orderid'], 'uniacid' => $_W['uniacid']));
		}

		$returnurl = urlencode($this->createMobileUrl('member/withdrawg'));
		$infourl = $this->createMobileUrl('member/info', array('returnurl' => $returnurl));
		$this->model->sendMessage($openid, array('commission' => $commission_ok, 'type' => $apply['type'] == 1 ? '微信' : '余额'), TM_COMMISSION_APPLY);
		show_json(1, '已提交,请等待审核!');
	}
	$returnurl = urlencode($this->createPluginMobileUrl('bonus/apply'));
	$infourl = $this->createMobileUrl('member/info', array('returnurl' => $returnurl));
	show_json(1, array('commission_ok' => $member['commission_ok'], 'cansettle' => $cansettle, 'member' => $member, 'set' => $this->set, 'infourl' => $infourl, 'noinfo' => empty($member['realname'])));
}
include $this->template('apply');
