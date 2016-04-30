<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}

global $_W, $_GPC;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$openid    = m('user')->getOpenid();
$uniacid   = $_W['uniacid'];
$designer  = p('designer');
if ($designer) {
	$pagedata = $designer->getPage();
	if ($pagedata) {
		extract($pagedata);
		$guide = $designer->getGuide($system, $pageinfo);
		$_W['shopshare'] = array('title' => $share['title'], 'imgUrl' => $share['imgUrl'], 'desc' => $share['desc'], 'link' => $this->createMobileUrl('shop'));
		if (p('commission')) {
			$set = p('commission')->getSet();
			if (!empty($set['level'])) {
				$member = m('member')->getMember($openid);
				if (!empty($member) && $member['status'] == 1 && $member['isagent'] == 1) {
					$_W['shopshare']['link'] = $this->createMobileUrl('shop', array('mid' => $member['id']));
					if (empty($set['become_reg']) && (empty($member['realname']) || empty($member['mobile']))) {
						$trigger = true;
					}
				} else if (!empty($_GPC['mid'])) {
					$_W['shopshare']['link'] = $this->createMobileUrl('shop', array('mid' => $_GPC['mid']));
				}
			}
		}
		include $this->template('shop/index_diy');
		exit;
	}
}
$set = set_medias(m('common')->getSysset('shop'), array('logo', 'img'));
$custom = m('common')->getSysset('custom');

if ($operation == 'index') {
	$advs = pdo_fetchall('select id,advname,link,thumb,thumb_pc from ' . tablename('sz_yi_adv') . ' where uniacid=:uniacid and enabled=1 order by displayorder desc', array(':uniacid' => $uniacid));
	foreach($advs as $key => $adv){
		if(!empty($advs[$key]['thumb'])){
			$adv[] = $advs[$key];
		}
		if(!empty($advs[$key]['thumb_pc'])){
			$adv_pc[] = $advs[$key];
		}
	}
	$advs = set_medias($advs, 'thumb,thumb_pc');
	$advs_pc = set_medias($adv_pc, 'thumb,thumb_pc');
	$adss = pdo_fetchall('select * from ' . tablename('sz_yi_ads') . ' where uniacid=:uniacid', array(':uniacid' => $uniacid));
	$adss = set_medias($adss, 'thumb_1,thumb_2,thumb_3,thumb_4');
    $category = pdo_fetchall('select id,name,thumb,parentid,level from ' . tablename('sz_yi_category') . ' where uniacid=:uniacid and ishome=1 and enabled=1 order by displayorder desc', array(':uniacid' => $uniacid));
	$category = set_medias($category, 'thumb');

	$custom = m("common")->getSysset('custom');

	$index_name = array(
		'isrecommand' 	=> '精品推荐',
		'isnew' 		=> '新上商品',
		'ishot' 		=> '热卖商品',
		'isdiscount' 	=> '促销商品',
		'issendfree' 	=> '包邮商品',
		'istime' 		=> '限时特价'
		);


	$condition1 = ' and isrecommand = 1 ';
	$condition2 = ' and isnew = 1';
	$index_name1 = $index_name['isrecommand'];
	$index_name2 = $index_name['isnew'];
	if($custom['iscustom'])
	{
		$condition1 = '';
		$condition2 = '';
		$index_name1 = '';
		$index_name2 = '';
		$i = 1;
		foreach ($custom['index1'] as $key => $value) {
			$index_name1 .= " ".$index_name[$key];
			if($i == 1)
			{
				$condition1 .= " and (".$key." = 1";
			}else
			{
				$condition1 .= " or ".$key." = 1";
			}

			$i++;
		}
		$condition1 .= ") ";
		$i = 1;
		foreach ($custom['index2'] as $key => $value) {
			$index_name2 .= " ".$index_name[$key];
			if($i == 1)
			{
				$condition2 .= " and (".$key." = 1";
			}else
			{
				$condition2 .= " or ".$key." = 1";
			}
			$i++;
		}
		$condition2 .= ") ";
	}


	$goods_one = pdo_fetchall('select * from ' . tablename('sz_yi_goods') . ' where uniacid = :uniacid and status = 1 and deleted = 0 '.$condition1, array(':uniacid' => $uniacid));
	$goods_one = set_medias($goods_one, 'thumb');

	$goods_two = pdo_fetchall('select * from ' . tablename('sz_yi_goods') . ' where uniacid = :uniacid and status = 1 and deleted = 0 '.$condition2, array(':uniacid' => $uniacid));

	$goods_two = set_medias($goods_two, 'thumb');
	foreach ($category as &$c) {
		$c['thumb'] = tomedia($c['thumb']);
		if ($c['level'] == 3) {
			$c['url'] = $this->createMobileUrl('shop/list', array('tcate' => $c['id']));
		} else if ($c['level'] == 2) {
			$c['url'] = $this->createMobileUrl('shop/list', array('ccate' => $c['id']));
		}
	}
	unset($c);
} else if ($operation == 'goods') {
	$type = $_GPC['type'];
	$args = array('page' => $_GPC['page'], 'pagesize' => 6, 'isrecommand' => 1, 'order' => 'displayorder desc,createtime desc', 'by' => '');
	$goods = m('goods')->getList($args);
}
if ($_W['isajax']) {
	if ($operation == 'index') {
		show_json(1, array('set' => $set, 'advs' => $advs, 'category' => $category));
	} else if ($operation == 'goods') {
		$type = $_GPC['type'];
		show_json(1, array('goods' => $goods, 'pagesize' => $args['pagesize']));
	}
}


$this->setHeader();
include $this->template('shop/index');
