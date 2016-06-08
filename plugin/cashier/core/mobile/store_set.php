<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;
$page = 'set';
$openid = m('user')->getOpenid();
$member = m('member')->getInfo($openid);
if(!empty($_GPC['id'])){
	$id=$_GPC['id'];
}
$store = pdo_fetch('select * from ' . tablename('sz_yi_cashier_store') . ' where uniacid=:uniacid and id=:id', array(
    ':uniacid' => $_W['uniacid'], ':id' => $id
));
if (p('commission')) {
    $com_set = p('commission')->getSet();
}
$pcoupon = p('coupon');
if ($pcoupon) {
    $couponList = pdo_fetchall('SELECT id, couponname FROM ' . tablename('sz_yi_coupon') . 'WHERE uniacid = :uniacid', array(
        ':uniacid' => $_W['uniacid']
    ));
}

if ($_W['isajax']) {
    if ($_W['ispost'] && $_GPC['op'] == 'sub_info') {
        $data = array(
            'name'    => trim($_GPC['name']),
            'contact' => trim($_GPC['contact']),
            'mobile'  => trim($_GPC['mobile']),
            'address' => trim($_GPC['address']),
            'deduct_credit1' => trim($_GPC['deduct_credit1']),
            'deduct_credit2' => trim($_GPC['deduct_credit2']),
           // 'settle_platform' => trim($_GPC['settle_platform']),   // TODO: 平台和商家比例不可设置？
           // 'settle_store' => trim($_GPC['settle_store']),
            'commission1_rate' => trim($_GPC['commission1_rate']),
            'commission2_rate' => trim($_GPC['commission2_rate']),
            'commission3_rate' => trim($_GPC['commission3_rate']),
            'credit1' => trim($_GPC['credit1']),
            'redpack_min' => trim($_GPC['redpack_min']),
            'redpack' => trim($_GPC['redpack']),
        );
        if ($pcoupon) {
            $data['coupon_id'] = trim($_GPC['coupon_id']);
        }
        if (!empty($store['id'])) {
            pdo_update('sz_yi_cashier_store', $data, array('id' => $store['id'], 'uniacid' => $_W['uniacid']));
        } else {
            $data['create_time'] = date('Y-m-d H:i:s');
            pdo_insert('sz_yi_cashier_store', $data);
        }
        show_json(1);
    }
    show_json(1, array(
        'store' => $store
    ));
}
if ($_GPC['op'] == 'qrcode') {
    $id = intval($store['id']);
    $accountDir = IA_ROOT . '/addons/sz_yi/data/qrcode/' . $_W['uniacid'];
    if (!is_dir($accountDir)) {
        load()->func('file');
        mkdirs($accountDir);
    }
    $payLink   = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=sz_yi&do=plugin&p=cashier&method=order_comfirm&sid=' . $id .'&mid='.$member['id'];
    $qrcodeImg = 'cashier_store_' . $id . '.png';
    $fullPath  = $accountDir . '/' . $qrcodeImg;
    if (!is_file($fullPath)) {
        require IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
        QRcode::png($payLink, $fullPath, QR_ECLEVEL_H, 4);
    }

    header('Content-type: image/png'); 
    header("Content-Disposition: attachment; filename='$qrcodeImg'");
    readfile($fullPath);
    exit;
}

include $this->template('cashier/store_set');
