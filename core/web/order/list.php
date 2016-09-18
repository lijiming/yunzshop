<?php
global $_W, $_GPC;
$operation = !empty($_GPC["op"]) ? $_GPC["op"] : "display";
$type = $_GPC['type'];
$plugin_diyform = p("diyform");
$shopset = m('common')->getSysset('pay');
$yunbi_plugin   = p('yunbi');
if ($yunbi_plugin) {
    $yunbiset = $yunbi_plugin->getSet();
}
$totals = array();
$r_type         = array(
    '0' => '退款',
    '1' => '退货退款',
    '2' => '换货'
);
$store_list = pdo_fetchall("SELECT * FROM ".tablename('sz_yi_store')." WHERE uniacid=:uniacid and status=1", array(':uniacid' => $_W['uniacid']));
if ($operation == "display") {
    ca("order.view.status_1|order.view.status0|order.view.status1|order.view.status2|order.view.status3|order.view.status4|order.view.status5");
    //判断该帐号的权限
    if(p('supplier')){
        $perm_role = p('supplier')->verifyUserIsSupplier($_W['uid']);
    }
    //END
    $pindex = max(1, intval($_GPC["page"]));
    $psize = 20;
    $status = $_GPC["status"];
    $sendtype = !isset($_GPC["sendtype"]) ? 0 : $_GPC["sendtype"];
    $condition = " o.uniacid = :uniacid and o.deleted=0";
    $paras = $paras1 = array(
        ":uniacid" => $_W["uniacid"]
    );
    if (empty($starttime) || empty($endtime)) {
        $starttime = strtotime("-1 month");
        $endtime = time();
    }
    if (!empty($_GPC["time"])) {
        $starttime = strtotime($_GPC["time"]["start"]);
        $endtime = strtotime($_GPC["time"]["end"]);
        if ($_GPC["searchtime"] == "1") {
            $condition.= " AND o.createtime >= :starttime AND o.createtime <= :endtime ";
            $paras[":starttime"] = $starttime;
            $paras[":endtime"] = $endtime;
        }
    }
    if (empty($pstarttime) || empty($pendtime)) {
        $pstarttime = strtotime("-1 month");
        $pendtime = time();
    }
    if (!empty($_GPC["ptime"])) {
        $pstarttime = strtotime($_GPC["ptime"]["start"]);
        $pendtime = strtotime($_GPC["ptime"]["end"]);
        if ($_GPC["psearchtime"] == "1") {
            $condition.= " AND o.paytime >= :pstarttime AND o.paytime <= :pendtime ";
            $paras[":pstarttime"] = $pstarttime;
            $paras[":pendtime"] = $pendtime;
        }
    }
    if (empty($fstarttime) || empty($fendtime)) {
        $fstarttime = strtotime("-1 month");
        $fendtime = time();
    }
    if (!empty($_GPC["ftime"])) {
        $fstarttime = strtotime($_GPC["ftime"]["start"]);
        $fendtime = strtotime($_GPC["ftime"]["end"]);
        if ($_GPC["fsearchtime"] == "1") {
            $condition.= " AND o.finishtime >= :fstarttime AND o.finishtime <= :fendtime ";
            $paras[":fstarttime"] = $fstarttime;
            $paras[":fendtime"] = $fendtime;
        }
    }
    if (empty($sstarttime) || empty($sendtime)) {
        $sstarttime = strtotime("-1 month");
        $sendtime = time();
    }
    if (!empty($_GPC["stime"])) {
        $sstarttime = strtotime($_GPC["stime"]["start"]);
        $sendtime = strtotime($_GPC["stime"]["end"]);
        if ($_GPC["ssearchtime"] == "1") {
            $condition.= " AND o.sendtime >= :sstarttime AND o.sendtime <= :sendtime ";
            $paras[":sstarttime"] = $sstarttime;
            $paras[":sendtime"] = $sendtime;
        }
    }
    if ($_GPC["paytype"] != '') {
        if ($_GPC["paytype"] == "2") {
            $condition.= " AND ( o.paytype =21 or o.paytype=22 or o.paytype=23 )";
        } else {
            $condition.= " AND o.paytype =" . intval($_GPC["paytype"]);
        }
    }
    //门店取消订单搜索
    if(empty($_W['isagent'])){
        if($_GPC['cancel'] == 1){
            $orderids = pdo_fetchall("select orderid from " . tablename('sz_yi_cancel_goods') . " where uniacid={$_W['uniacid']} ");
            $ids = "";
            foreach ($orderids as $key => $value) {
                if($key != 0){
                    $ids .= "," . $value['orderid'];
                }else{
                    $ids .= $value['orderid'];
                }
            }
            if (!empty($orderids)) {
                $condition.= " and o.id in (" .$ids. ") ";
            }

        }
    }
    //商品名称检索订单
    if (!empty($_GPC["good_name"])) {
        $conditionsp_goods = pdo_fetchall("select og.orderid from " . tablename('sz_yi_order_goods') . " og left join " .tablename('sz_yi_goods') . " g on (g.id=og.goodsid) where og.uniacid={$_W['uniacid']} and g.title LIKE '%{$_GPC["good_name"]}%' group by og.orderid ");
        $conditionsp_goodsid = '';
        foreach ($conditionsp_goods as $value) {
            $conditionsp_goodsid .= "'".$value['orderid']."', ";
        }
        //判断商品名称是否存在 不存在订单ID等于空
        if (!empty($conditionsp_goodsid)) {
            $condition .= " AND o.id in (".substr($conditionsp_goodsid,0,-2).") ";
        }else {
            $condition .= " AND o.id = '' ";
        }
       
    }
    //商品ID检索
    if (!empty($_GPC["good_id"])) {
        $conditionsp_goods = pdo_fetchall("select og.orderid from " . tablename('sz_yi_order_goods') . " og left join " .tablename('sz_yi_goods') . " g on (g.id=og.goodsid) where og.uniacid={$_W['uniacid']} and g.id = '{$_GPC["good_id"]}' group by og.orderid ");
        $conditionsp_goodsid = '';
        foreach ($conditionsp_goods as $value) {
            $conditionsp_goodsid .= "'".$value['orderid']."', ";
        }
        //判断商品ID是否存在 不存在订单ID等于空
        if (!empty($conditionsp_goodsid)) {
            $condition .= " AND o.id in (".substr($conditionsp_goodsid,0,-2).") ";
        }else {
            $condition .= " AND o.id = '' ";
        } 
    }


    if (!empty($_GPC["keyword"])) {
        $_GPC["keyword"] = trim($_GPC["keyword"]);
        $condition.= " AND (o.ordersn LIKE '%{$_GPC["keyword"]}%' OR o.ordersn_general LIKE '%{$_GPC["keyword"]}%')";
    }
    if (!empty($_GPC["expresssn"])) {
        $_GPC["expresssn"] = trim($_GPC["expresssn"]);
        $condition.= " AND o.expresssn LIKE '%{$_GPC["expresssn"]}%'";
    }
    if (!empty($_GPC["member"])) {
        $_GPC["member"] = trim($_GPC["member"]);
        $condition.= " AND (m.realname LIKE '%{$_GPC["member"]}%' or m.mobile LIKE '%{$_GPC["member"]}%' or m.nickname LIKE '%{$_GPC["member"]}%' " . " or a.realname LIKE '%{$_GPC["member"]}%' or a.mobile LIKE '%{$_GPC["member"]}%' or o.carrier LIKE '%{$_GPC["member"]}%')";
    }
    if (!empty($_GPC["saler"])) {
        $_GPC["saler"] = trim($_GPC["saler"]);
        $condition.= " AND (sm.realname LIKE '%{$_GPC["saler"]}%' or sm.mobile LIKE '%{$_GPC["saler"]}%' or sm.nickname LIKE '%{$_GPC["saler"]}%' " . " or s.salername LIKE '%{$_GPC["saler"]}%' )";
    }
    if (!empty($_GPC["storeid"])) {
        $_GPC["storeid"] = trim($_GPC["storeid"]);
        $condition.= " AND o.verifystoreid=" . intval($_GPC["storeid"]);
    }
    if (!empty($_GPC["csid"])) {
        $_GPC["csid"] = trim($_GPC["csid"]);
        $condition.= " AND o.cashierid=" . intval($_GPC["csid"]);
    }
    if (p('hotel')) {
        if($type=='hotel'){
           $condition.= " AND o.order_type=3";
        }else{
           $condition.= " AND o.order_type<>3";
        }
    }else{          
           $condition.= " AND o.order_type<>3";
    }
    $statuscondition = '';
    if ($status != '') {
        if ($status == - 1) {
            ca("order.view.status_1");
        } else {
            ca("order.view.status" . intval($status));
        }
        if ($status == "-1") {
            $statuscondition = " AND o.status=-1 and o.refundtime=0";
        } else if ($status == "4") {
            $statuscondition = " AND o.refundtime=0 AND o.refundid<>0 and r.status=0 ";
        } else if ($status == "5") {
            $statuscondition = " AND o.refundtime<>0 AND o.refundid<>0 and r.status=1";
        } else if ($status == "1") {
            $statuscondition = " AND ( o.status = 1 or (o.status=0 and o.paytype=3) )";
        } else if ($status == '0') {
            $statuscondition = " AND o.status = 0 and o.paytype<>3";
        } else {
            $statuscondition = " AND o.status = " . intval($status);
        }
    }
    $bonusagentid = intval($_GPC['bonusagentid']);
    if(!empty($bonusagentid)){
        $sql = "select distinct orderid from " . tablename('sz_yi_bonus_goods') . " where mid=".$bonusagentid." ORDER BY id DESC";
        $bonusoderids = pdo_fetchall($sql);
        $inorderids = "";
        if(!empty($bonusoderids)){
            foreach ($bonusoderids as $key => $value) {
                if($key != 0){
                    $inorderids .= ",";
                }
                $inorderids = $value['orderid'];
            }
            $condition .= ' and  o.id in('.$inorderids.')';
        }else{
            $condition .= ' and  o.id=0';
        }
    }
    $agentid = intval($_GPC["agentid"]);
    $p = p("commission");
    $level = 0;
    if ($p) {
        $cset = $p->getSet();
        $level = intval($cset["level"]);
    }
    $olevel = intval($_GPC["olevel"]);
    if (!empty($agentid) && $level > 0) {
        $agent = $p->getInfo($agentid, array());
        if (!empty($agent)) {
            $agentLevel = $p->getLevel($agentid);
        }
        if (empty($olevel)) {
            if ($level >= 1) {
                $condition.= " and  ( o.agentid=" . intval($_GPC["agentid"]);
            }
            if ($level >= 2 && $agent["level2"] > 0) {
                $condition.= " or o.agentid in( " . implode(",", array_keys($agent["level1_agentids"])) . ")";
            }
            if ($level >= 3 && $agent["level3"] > 0) {
                $condition.= " or o.agentid in( " . implode(",", array_keys($agent["level2_agentids"])) . ")";
            }
            if ($level >= 1) {
                $condition.= ")";
            }
        } else {
            if ($olevel == 1) {
                $condition.= " and  o.agentid=" . intval($_GPC["agentid"]);
            } else if ($olevel == 2) {
                if ($agent["level2"] > 0) {
                    $condition.= " and o.agentid in( " . implode(",", array_keys($agent["level1_agentids"])) . ")";
                } else {
                    $condition.= " and o.agentid in( 0 )";
                }
            } else if ($olevel == 3) {
                if ($agent["level3"] > 0) {
                    $condition.= " and o.agentid in( " . implode(",", array_keys($agent["level2_agentids"])) . ")";
                } else {
                    $condition.= " and o.agentid in( 0 )";
                }
            }
        }
    }
    //是否为供应商 等于1的是
    if(p('supplier')){
        $cond = "";
        if($perm_role == 1){
            $cond .= " and o.supplier_uid={$_W['uid']} ";
            $supplierapply = pdo_fetchall('select a.id,u.uid,p.realname,p.mobile,p.banknumber,p.accountname,p.accountbank,a.applysn,a.apply_money,a.apply_time,a.type,a.finish_time,a.status from ' . tablename('sz_yi_supplier_apply') . ' a ' . ' left join' . tablename('sz_yi_perm_user') . ' p on p.uid=a.uid ' . 'left join' . tablename('users') . ' u on a.uid=u.uid where u.uid=' . $_W['uid']);
            $totals['status9'] = count($supplierapply);
            $costmoney = 0;
            $sp_goods = pdo_fetchall("select og.* from " . tablename('sz_yi_order_goods') . " og left join " .tablename('sz_yi_order') . " o on (o.id=og.orderid) where og.uniacid={$_W['uniacid']} and og.supplier_uid={$_W['uid']} and o.status=3 and og.supplier_apply_status=0");
            foreach ($sp_goods as $key => $value) {
                if ($value['goods_op_cost_price'] > 0) {
                    $costmoney += $value['goods_op_cost_price'] * $value['total'];
                } else {
                    $option = pdo_fetch("select * from " . tablename('sz_yi_goods_option') . " where uniacid={$_W['uniacid']} and goodsid={$value['goodsid']} and id={$value['optionid']}");
                    if ($option['costprice'] > 0) {
                        $costmoney += $option['costprice'] * $value['total'];
                    } else {
                        $goods_info = pdo_fetch("select * from" . tablename('sz_yi_goods') . " where uniacid={$_W['uniacid']} and id={$value['goodsid']}");
                        $costmoney += $goods_info['costprice'] * $value['total'];
                    }
                }
            }
            $openid = pdo_fetchcolumn('select openid from ' . tablename('sz_yi_perm_user') . ' where uid=:uid and uniacid=:uniacid',array(':uid' => $_W['uid'],':uniacid'=> $_W['uniacid']));
            if(empty($openid)){
                message("暂未绑定微信，请联系管理员", '', "error");
            }
            //全部提现
            $applytype = intval($_GPC['applytype']);
            $apply_ordergoods_ids = "";
            foreach ($sp_goods as $key => $value) {
                if ($key == 0) {
                    $apply_ordergoods_ids .= $value['id'];
                } else {
                    $apply_ordergoods_ids .= ','.$value['id'];
                }
            }
            if(!empty($applytype)){
                $applysn = m('common')->createNO('commission_apply', 'applyno', 'CA');
                $data = array(
                    'uid' => $_W['uid'],
                    'apply_money' => $costmoney,
                    'apply_time' => time(),
                    'status' => 0,
                    'type' => $applytype,
                    'applysn' => $applysn,
                    'uniacid' => $_W['uniacid'],
                    'apply_ordergoods_ids' => $apply_ordergoods_ids
                    );

                pdo_insert('sz_yi_supplier_apply',$data);
                @file_put_contents(IA_ROOT . "/addons/sz_yi/data/apply.log", print_r($data, 1), FILE_APPEND);
                if( pdo_insertid() ) {
                    foreach ($sp_goods as $ids) {
                        $arr = array(
                            'supplier_apply_status' => 2
                            );
                        pdo_update('sz_yi_order_goods', $arr, array(
                            'id' => $ids['id']
                            ));
                    }
                    $tmp_sp_goods = $sp_goods;
                    $tmp_sp_goods['applyno'] = $applysn;
                    @file_put_contents(IA_ROOT . "/addons/sz_yi/data/sp_goods.log", print_r($tmp_sp_goods, 1), FILE_APPEND);
                }
                message("提现申请已提交，请耐心等待!", $this->createWebUrl('order/list'), "success");
            }
        }
    }
    //Author:ym Date:2016-07-20 Content:订单分组查询
    $sql = 'select o.* , a.realname as arealname,a.mobile as amobile,a.province as aprovince ,a.city as acity , a.area as aarea,a.address as aaddress, d.dispatchname,m.nickname,m.id as mid,m.realname as mrealname,m.mobile as mmobile,sm.id as salerid,sm.nickname as salernickname,s.salername,r.rtype,r.status as rstatus,o.pay_ordersn, o.dispatchtype, o.isverify, o.storeid from ' . tablename("sz_yi_order") . " o" . " left join " . tablename("sz_yi_order_refund") . " r on r.id =o.refundid " . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid and m.uniacid =  o.uniacid " . " left join " . tablename("sz_yi_member_address") . " a on a.id=o.addressid " . " left join " . tablename("sz_yi_dispatch") . " d on d.id = o.dispatchid " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . "  where {$condition} {$statuscondition} {$cond} group by o.ordersn_general ORDER BY o.createtime DESC,o.status DESC  ";
    if (empty($_GPC["export"])) {
        $sql.= "LIMIT " . ($pindex - 1) * $psize . "," . $psize;
    }
    $list = pdo_fetchall($sql, $paras);
    foreach ($list as $key => &$value) {
        $isresult = pdo_fetch("select * from " . tablename('sz_yi_cancel_goods') . " where orderid={$value['id']} and uniacid={$_W['uniacid']}");
        if(!empty($isresult)){
            $value['isempty'] = 1;
            if($isresult['ismaster'] == 0){
                $value['ismaster'] = 1;
            }
        }else{
            $value['isempty'] = 0;
        }
    }
    if (p('supplier')) {
        foreach ($list as &$value) {
            $suppliers_num = pdo_fetchcolumn('select count(*) from ' . tablename("sz_yi_order") . " where ordersn_general=:ordersn_general and uniacid=:uniacid", array(':ordersn_general' => $value['ordersn_general'], ':uniacid' => $_W['uniacid']));
            if($suppliers_num > 1){
                $value['vendor'] = '多供应商';
                $value['ischangePrice'] = 0;
            }else{
                if ($value['supplier_uid'] == 0) {
                    $value['vendor'] = '总店';
                } else {
                    $sup_username = pdo_fetchcolumn("select username from " . tablename('sz_yi_perm_user') . " where uniacid={$_W['uniacid']} and uid={$value['supplier_uid']}");
                    $value['vendor'] = '供应商：' . $sup_username;
                }
                $value['ischangePrice'] = 1;
            }
        }
    }
    unset($value);
    $paytype = array(
        '0' => array(
            "css" => "default",
            "name" => "未支付"
        ) ,
        "1" => array(
            "css" => "danger",
            "name" => "余额支付"
        ) ,
        "11" => array(
            "css" => "default",
            "name" => "后台付款"
        ) ,
        "2" => array(
            "css" => "danger",
            "name" => "在线支付"
        ) ,
        "21" => array(
            "css" => "success",
            "name" => "微信支付"
        ) ,
        "22" => array(
            "css" => "warning",
            "name" => "支付宝支付"
        ) ,
        "23" => array(
            "css" => "warning",
            "name" => "银联支付"
        ) ,
        "3" => array(
            "css" => "primary",
            "name" => "货到付款"
        ) ,
        "4" => array(
            "css" => "primary",
            "name" => "到店支付"
        ) 
    );
    $orderstatus = array(
        "-1" => array(
            "css" => "default",
            "name" => "已关闭"
        ) ,
        '0' => array(
            "css" => "danger",
            "name" => "待付款"
        ) ,
        "1" => array(
            "css" => "info",
            "name" => "待发货"
        ) ,
        "2" => array(
            "css" => "warning",
            "name" => "待收货"
        ) ,
        "3" => array(
            "css" => "success",
            "name" => "已完成"
        )
    );
    if(p('hotel')){
         if($type=='hotel'){
            $orderstatus = array(
                "-1" => array(
                    "css" => "default",
                    "name" => "已关闭"
                ) ,
                '0' => array(
                    "css" => "danger",
                    "name" => "待付款"
                ) ,
                "1" => array(
                    "css" => "info",
                    "name" => "待确认"
                ) ,
                "2" => array(
                    "css" => "warning",
                    "name" => "待入住"
                ),            
                "3" => array(
                    "css" => "success",
                    "name" => "已完成"
                ),
                "6" => array(
                    "css" => "success",
                    "name" => "待退房"
                ),
            );
        }
    }
    foreach ($list as & $value) {
        $orderids = pdo_fetchall("select distinct id from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid', array(
                    ':ordersn_general' => $value["ordersn_general"],
                    ':uniacid' => $_W["uniacid"]
                ),'id');
        if(count($orderids) > 1 && $value['status'] == 0){
            $order_all = pdo_fetchall("select * from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid', array(
                ':ordersn_general' => $value['ordersn_general'],
                ':uniacid' => $_W["uniacid"]
            ));
            $orderids = array();
            $value['goodsprice'] = 0;
            $value['olddispatchprice'] = 0;
            $value['discountprice'] = 0;
            $value['deductprice'] = 0;
            $value['deductcredit2'] = 0;
            $value['deductenough'] = 0;
            $value['changeprice'] = 0;
            $value['changedispatchprice'] = 0;
            $value['couponprice'] = 0;
            $value['price'] = 0;
            foreach ($order_all as $k => $v) {
                $orderids[] = $v['id'];
                $value['goodsprice'] += $v['goodsprice'];
                $value['olddispatchprice'] += $v['olddispatchprice'];
                $value['discountprice'] += $v['discountprice'];
                $value['deductprice'] += $v['deductprice'];
                $value['deductcredit2'] += $v['deductcredit2'];
                $value['deductenough'] += $v['deductenough'];
                $value['changeprice'] += $v['changeprice'];
                $value['changedispatchprice'] += $v['changedispatchprice'];
                $value['couponprice'] += $v['couponprice'];
                $value['price'] += $v['price'];
            }
            
            $value['ordersn'] = $value['ordersn_general'];
            $orderid_where_in = implode(',', $orderids);
            $order_where = "og.orderid in ({$orderid_where_in})";
        }else{
            $order_where = "og.orderid = ".$value['id'];
        }

        $s = $value["status"];
        $pt = $value["paytype"];
        $value["statusvalue"] = $s;
        $value["statuscss"] = $orderstatus[$value["status"]]["css"];
        $value["status"] = $orderstatus[$value["status"]]["name"];
        if ($pt == 3 && empty($value["statusvalue"])) {
            $value["statuscss"] = $orderstatus[1]["css"];
            $value["status"] = $orderstatus[1]["name"];
        }
        if ($s == 1) {
            if ($value["isverify"] == 1) {
                $value["status"] = "待使用";
            } else if (empty($value["addressid"])) {
                $value["status"] = "待取货";
            }
        }
        if ($s == - 1) {
            $value['status'] = $value['rstatus'];
            if (!empty($value["refundtime"])) {
                if ($value['rstatus'] == 1) {
                    $value['status'] = '已' . $r_type[$value['rtype']];
                }
            }else{
                $value['status'] = '已关闭';
            }
        }
        $value["paytypevalue"] = $pt;
        $value["css"] = $paytype[$pt]["css"];
        $value["paytype"] = $paytype[$pt]["name"];
        $value["dispatchname"] = empty($value["addressid"]) ? "自提" : $value["dispatchname"];
        if (empty($value["dispatchname"])) {
            $value["dispatchname"] = "快递";
        }
        if ($value["isverify"] == 1) {
            $value["dispatchname"] = "线下核销";
        } else if ($value["isvirtual"] == 1) {
            $value["dispatchname"] = "虚拟物品";
        } else if (!empty($value["virtual"])) {
            $value["dispatchname"] = "虚拟物品(卡密)<br/>自动发货";
        } else if ($value['cashier']==1) {
            $value["dispatchname"] = "收银台支付";
        }
        if(p('cashier') && $value['cashier'] == 1){
                    $value['name'] = set_medias(pdo_fetch('select cs.name,cs.thumb from ' .tablename('sz_yi_cashier_store'). 'cs '.'left join ' .tablename('sz_yi_cashier_order'). ' co on cs.id = co.cashier_store_id where co.order_id=:orderid and co.uniacid=:uniacid', array(':orderid' => $value['id'],':uniacid'=>$_W['uniacid'])), 'thumb');
        }

        if (($value["dispatchtype"] == 1 && !empty($value["isverify"])) || !empty($value["virtual"]) || !empty($value["isvirtual"])|| $value['cashier'] == 1) {
            $value["address"] = '';
            $carrier = iunserializer($value["carrier"]);
            if (is_array($carrier)) {
                $value["addressdata"]["realname"] = $value["realname"] = $carrier["carrier_realname"];
                $value["addressdata"]["mobile"] = $value["mobile"] = $carrier["carrier_mobile"];
                $value["addressdata"]["address"] = $value["address"] = $carrier["address"];

            }
        } else {
            $address = iunserializer($value["address"]);
            $isarray = is_array($address);
            $value["realname"] = $isarray ? $address["realname"] : $value["arealname"];
            $value["mobile"] = $isarray ? $address["mobile"] : $value["amobile"];
            $value["province"] = $isarray ? $address["province"] : $value["aprovince"];
            $value["city"] = $isarray ? $address["city"] : $value["acity"];
            $value["area"] = $isarray ? $address["area"] : $value["aarea"];
            $value["address"] = $isarray ? $address["address"] : $value["aaddress"];
            $value["address_province"] = $value["province"];
            $value["address_city"] = $value["city"];
            $value["address_area"] = $value["area"];
            $value["address_address"] = $value["address"];
            $value["address"] = $value["province"] . " " . $value["city"] . " " . $value["area"] . " " . $value["address"];
            $value["addressdata"] = array(
                "realname" => $value["realname"],
                "mobile" => $value["mobile"],
                "address" => $value["address"],
            );
        }
        $commission1 = - 1;
        $commission2 = - 1;
        $commission3 = - 1;
        $m1 = false;
        $m2 = false;
        $m3 = false;
        if (!empty($level) && empty($agentid)) {
            if (!empty($value["agentid"])) {
                $m1 = m("member")->getMember($value["agentid"]);
                $commission1 = 0;
                if (!empty($m1["agentid"])) {
                    $m2 = m("member")->getMember($m1["agentid"]);
                    $commission2 = 0;
                    if (!empty($m2["agentid"])) {
                        $m3 = m("member")->getMember($m2["agentid"]);
                        $commission3 = 0;
                    }
                }
            }
        }
        
        $order_goods = pdo_fetchall("select g.id,g.title,g.thumb,g.goodssn,og.goodssn as option_goodssn, g.productsn,og.productsn as option_productsn, og.total,og.price,og.optionname as optiontitle, og.realprice,og.changeprice,og.oldprice,og.commission1,og.commission2,og.commission3,og.commissions,og.diyformdata,og.diyformfields from " . tablename("sz_yi_order_goods") . " og " . " left join " . tablename("sz_yi_goods") . " g on g.id=og.goodsid " . " where og.uniacid=:uniacid and ".$order_where , array(
            ":uniacid" => $_W["uniacid"]
        ));
        $goods = '';
        foreach ($order_goods as & $og) {
            if (!empty($level) && empty($agentid)) {
                $commissions = iunserializer($og["commissions"]);
                if (!empty($m1)) {
                    if (is_array($commissions)) {
                        $commission1+= isset($commissions["level1"]) ? floatval($commissions["level1"]) : 0;
                    } else {
                        $c1 = iunserializer($og["commission1"]);
                        $l1 = $p->getLevel($m1["openid"]);
                        $commission1+= isset($c1["level" . $l1["id"]]) ? $c1["level" . $l1["id"]] : $c1["default"];
                    }
                }
                if (!empty($m2)) {
                    if (is_array($commissions)) {
                        $commission2+= isset($commissions["level2"]) ? floatval($commissions["level2"]) : 0;
                    } else {
                        $c2 = iunserializer($og["commission2"]);
                        $l2 = $p->getLevel($m2["openid"]);
                        $commission2+= isset($c2["level" . $l2["id"]]) ? $c2["level" . $l2["id"]] : $c2["default"];
                    }
                }
                if (!empty($m3)) {
                    if (is_array($commissions)) {
                        $commission3+= isset($commissions["level3"]) ? floatval($commissions["level3"]) : 0;
                    } else {
                        $c3 = iunserializer($og["commission3"]);
                        $l3 = $p->getLevel($m3["openid"]);
                        $commission3+= isset($c3["level" . $l3["id"]]) ? $c3["level" . $l3["id"]] : $c3["default"];
                    }
                }
            }
            $goods.= "" . $og["title"] . "";
            if (!empty($og["optiontitle"])) {
                $goods.= " 规格: " . $og["optiontitle"];
            }
            if (!empty($og["option_goodssn"])) {
                $og["goodssn"] = $og["option_goodssn"];
            }
            if (!empty($og["option_productsn"])) {
                $og["productsn"] = $og["option_productsn"];
            }
            if (!empty($og["goodssn"])) {
                $goods.= " 商品编号: " . $og["goodssn"];
            }
            if (!empty($og["productsn"])) {
                $goods.= " 商品条码: " . $og["productsn"];
            }
            $goods.= " 单价: " . ($og["price"] / $og["total"]) . " 折扣后: " . ($og["realprice"] / $og["total"]) . " 数量: " . $og["total"] . " 总价: " . $og["price"] . " 折扣后: " . $og["realprice"] . "";
            if ($plugin_diyform && !empty($og["diyformfields"]) && !empty($og["diyformdata"])) {
                $diyformdata_array = $plugin_diyform->getDatas(iunserializer($og["diyformfields"]) , iunserializer($og["diyformdata"]));
                $diyformdata = "";
                foreach ($diyformdata_array as $da) {
                    $diyformdata.= $da["name"] . ": " . $da["value"] . "";
                }
                $og["goods_diyformdata"] = $diyformdata;
            }

        }
        unset($og);
        if (!empty($level) && empty($agentid)) {
            $value["commission1"] = $commission1;
            $value["commission2"] = $commission2;
            $value["commission3"] = $commission3;
        }
        //Author:ym Date:2016-08-29 Content:订单分红佣金
        if(p('bonus')){
            $bonus_area_money = pdo_fetchcolumn("select sum(money) from " . tablename('sz_yi_bonus_goods')." where orderid=:orderid and uniacid=:uniacid and bonus_area!=0", array(':orderid' => $value['id'], ":uniacid" => $_W['uniacid']));
            $bonus_range_money = pdo_fetchcolumn("select sum(money) from " . tablename('sz_yi_bonus_goods')." where orderid=:orderid and uniacid=:uniacid and bonus_area=0", array(':orderid' => $value['id'], ":uniacid" => $_W['uniacid']));
            if($bonus_area_money > 0 && $bonus_range_money > 0){
                $bonus_money_all = $bonus_area_money + $bonus_range_money;
                $value['bonus_money_all'] = floatval($bonus_money_all);
            }
            $value['bonus_area_money'] = floatval($bonus_area_money);
            $value['bonus_range_money'] = floatval($bonus_range_money);
        }
        $value["goods"] = set_medias($order_goods, "thumb");
        $value["goods_str"] = $goods;
        if (!empty($agentid) && $level > 0) {
            $commission_level = 0;
            if ($value["agentid"] == $agentid) {
                $value["level"] = 1;
                $level1_commissions = pdo_fetchall("select commission1,commissions  from " . tablename("sz_yi_order_goods") . " og " . " left join  " . tablename("sz_yi_order") . " o on o.id = og.orderid " . " where og.orderid=:orderid and o.agentid= " . $agentid . "  and o.uniacid=:uniacid", array(
                    ":orderid" => $value["id"],
                    ":uniacid" => $_W["uniacid"]
                ));
                foreach ($level1_commissions as $c) {
                    $commission = iunserializer($c["commission1"]);
                    $commissions = iunserializer($c["commissions"]);
                    if (empty($commissions)) {
                        $commission_level+= isset($commission["level" . $agentLevel["id"]]) ? $commission["level" . $agentLevel["id"]] : $commission["default"];
                    } else {
                        $commission_level+= isset($commissions["level1"]) ? floatval($commissions["level1"]) : 0;
                    }
                }
            } else if (in_array($value["agentid"], array_keys($agent["level1_agentids"]))) {
                $value["level"] = 2;
                if ($agent["level2"] > 0) {
                    $level2_commissions = pdo_fetchall("select commission2,commissions  from " . tablename("sz_yi_order_goods") . " og " . " left join  " . tablename("sz_yi_order") . " o on o.id = og.orderid " . " where og.orderid=:orderid and  o.agentid in ( " . implode(",", array_keys($agent["level1_agentids"])) . ")  and o.uniacid=:uniacid", array(
                        ":orderid" => $value["id"],
                        ":uniacid" => $_W["uniacid"]
                    ));
                    foreach ($level2_commissions as $c) {
                        $commission = iunserializer($c["commission2"]);
                        $commissions = iunserializer($c["commissions"]);
                        if (empty($commissions)) {
                            $commission_level+= isset($commission["level" . $agentLevel["id"]]) ? $commission["level" . $agentLevel["id"]] : $commission["default"];
                        } else {
                            $commission_level+= isset($commissions["level2"]) ? floatval($commissions["level2"]) : 0;
                        }
                    }
                }
            } else if (in_array($value["agentid"], array_keys($agent["level2_agentids"]))) {
                $value["level"] = 3;
                if ($agent["level3"] > 0) {
                    $level3_commissions = pdo_fetchall("select commission3,commissions from " . tablename("sz_yi_order_goods") . " og " . " left join  " . tablename("sz_yi_order") . " o on o.id = og.orderid " . " where og.orderid=:orderid and  o.agentid in ( " . implode(",", array_keys($agent["level2_agentids"])) . ")  and o.uniacid=:uniacid", array(
                        ":orderid" => $value["id"],
                        ":uniacid" => $_W["uniacid"]
                    ));
                    foreach ($level3_commissions as $c) {
                        $commission = iunserializer($c["commission3"]);
                        $commissions = iunserializer($c["commissions"]);
                        if (empty($commissions)) {
                            $commission_level+= isset($commission["level" . $agentLevel["id"]]) ? $commission["level" . $agentLevel["id"]] : $commission["default"];
                        } else {
                            $commission_level+= isset($commissions["level3"]) ? floatval($commissions["level3"]) : 0;
                        }
                    }
                }
            }
            $value["commission"] = $commission_level;
        }

    }
    unset($value);
    if ($_GPC["export"] == 1) {
        ca("order.op.export");
        plog("order.op.export", "导出订单");
        $columns = array(
            array(
                "title" => "订单编号",
                "field" => "ordersn_general",
                "width" => 24
            ) ,
            array(
                "title" => "支付单号",
                "field" => "pay_ordersn",
                "width" => 24
            ) ,
            array(
                "title" => "粉丝昵称",
                "field" => "nickname",
                "width" => 12
            ) ,
            array(
                "title" => "会员姓名",
                "field" => "mrealname",
                "width" => 12
            ) ,
            array(
                "title" => "会员手机手机号",
                "field" => "mmobile",
                "width" => 12
            ) ,
            array(
                "title" => "收货姓名(或自提人)",
                "field" => "realname",
                "width" => 12
            ) ,
            array(
                "title" => "联系电话",
                "field" => "mobile",
                "width" => 12
            ) ,
            array(
                "title" => "收货地址",
                "field" => "address_province",
                "width" => 12
            ) ,
            array(
                "title" => '',
                "field" => "address_city",
                "width" => 12
            ) ,
            array(
                "title" => '',
                "field" => "address_area",
                "width" => 12
            ) ,
            array(
                "title" => '',
                "field" => "address_address",
                "width" => 12
            ) ,
            array(
                "title" => "商品名称",
                "field" => "goods_title",
                "width" => 24
            ) ,
            array(
                "title" => "商品编码",
                "field" => "goods_goodssn",
                "width" => 12
            ) ,
            array(
                "title" => "商品规格",
                "field" => "goods_optiontitle",
                "width" => 12
            ) ,
            array(
                "title" => "商品数量",
                "field" => "goods_total",
                "width" => 12
            ) ,
            array(
                "title" => "商品单价(折扣前)",
                "field" => "goods_price1",
                "width" => 12
            ) ,
            array(
                "title" => "商品单价(折扣后)",
                "field" => "goods_price2",
                "width" => 12
            ) ,
            array(
                "title" => "商品价格(折扣后)",
                "field" => "goods_rprice1",
                "width" => 12
            ) ,
            array(
                "title" => "商品价格(折扣后)",
                "field" => "goods_rprice2",
                "width" => 12
            ) ,
            array(
                "title" => "支付方式",
                "field" => "paytype",
                "width" => 12
            ) ,
            array(
                "title" => "配送方式",
                "field" => "dispatchname",
                "width" => 12
            ) ,
            array(
                "title" => "商品小计",
                "field" => "goodsprice",
                "width" => 12
            ) ,
            array(
                "title" => "运费",
                "field" => "dispatchprice",
                "width" => 12
            ) ,
            array(
                "title" => "积分抵扣",
                "field" => "deductprice",
                "width" => 12
            ) ,
            array(
                "title" => "余额抵扣",
                "field" => "deductcredit2",
                "width" => 12
            ) ,
            array(
                "title" => "满额立减",
                "field" => "deductenough",
                "width" => 12
            ) ,
            array(
                "title" => "优惠券优惠",
                "field" => "couponprice",
                "width" => 12
            ) ,
            array(
                "title" => "订单改价",
                "field" => "changeprice",
                "width" => 12
            ) ,
            array(
                "title" => "运费改价",
                "field" => "changedispatchprice",
                "width" => 12
            ) ,
            array(
                "title" => "应收款",
                "field" => "price",
                "width" => 12
            ) ,
            array(
                "title" => "状态",
                "field" => "status",
                "width" => 12
            ) ,
            array(
                "title" => "下单时间",
                "field" => "createtime",
                "width" => 24
            ) ,
            array(
                "title" => "付款时间",
                "field" => "paytime",
                "width" => 24
            ) ,
            array(
                "title" => "发货时间",
                "field" => "sendtime",
                "width" => 24
            ) ,
            array(
                "title" => "完成时间",
                "field" => "finishtime",
                "width" => 24
            ) ,
            array(
                "title" => "快递公司",
                "field" => "expresscom",
                "width" => 24
            ) ,
            array(
                "title" => "快递单号",
                "field" => "expresssn",
                "width" => 24
            ) ,
            array(
                "title" => "订单备注",
                "field" => "remark",
                "width" => 36
            ) ,
            array(
                "title" => "核销员",
                "field" => "salerinfo",
                "width" => 24
            ) ,
            array(
                "title" => "核销门店",
                "field" => "storeinfo",
                "width" => 36
            ) ,
            array(
                "title" => "订单自定义信息",
                "field" => "order_diyformdata",
                "width" => 36
            ) ,
            array(
                "title" => "商品自定义信息",
                "field" => "goods_diyformdata",
                "width" => 36
            ) ,
        );
        if (!empty($agentid) && $level > 0) {
            $columns[] = array(
                "title" => "分销级别",
                "field" => "level",
                "width" => 24
            );
            $columns[] = array(
                "title" => "分销佣金",
                "field" => "commission",
                "width" => 24
            );
        }
        foreach ($list as & $row) {
            $row["ordersn"] = $row["ordersn"] . " ";
            if ($row["deductprice"] > 0) {
                $row["deductprice"] = "-" . $row["deductprice"];
            }
            if ($row["deductcredit2"] > 0) {
                $row["deductcredit2"] = "-" . $row["deductcredit2"];
            }
            if ($row["deductenough"] > 0) {
                $row["deductenough"] = "-" . $row["deductenough"];
            }
            if ($row["changeprice"] < 0) {
                $row["changeprice"] = "-" . $row["changeprice"];
            } else if ($row["changeprice"] > 0) {
                $row["changeprice"] = "+" . $row["changeprice"];
            }
            if ($row["changedispatchprice"] < 0) {
                $row["changedispatchprice"] = "-" . $row["changedispatchprice"];
            } else if ($row["changedispatchprice"] > 0) {
                $row["changedispatchprice"] = "+" . $row["changedispatchprice"];
            }
            if ($row["couponprice"] > 0) {
                $row["couponprice"] = "-" . $row["couponprice"];
            }
            $row["expresssn"] = $row["expresssn"] . " ";
            $row["createtime"] = date("Y-m-d H:i:s", $row["createtime"]);
            $row["paytime"] = !empty($row["paytime"]) ? date("Y-m-d H:i:s", $row["paytime"]) : '';
            $row["sendtime"] = !empty($row["sendtime"]) ? date("Y-m-d H:i:s", $row["sendtime"]) : '';
            $row["finishtime"] = !empty($row["finishtime"]) ? date("Y-m-d H:i:s", $row["finishtime"]) : '';
            $row["salerinfo"] = "";
            $row["storeinfo"] = "";
            if (!empty($row["verifyopenid"])) {
                $row["salerinfo"] = "[" . $row["salerid"] . "]" . $row["salername"] . "(" . $row["salernickname"] . ")";
            }
            if (!empty($row["verifystoreid"])) {
                $row["storeinfo"] = pdo_fetchcolumn("select storename from " . tablename("sz_yi_store") . " where id=:storeid limit 1 ", array(
                    ":storeid" => $row["verifystoreid"]
                ));
            }
            if ($plugin_diyform && !empty($row["diyformfields"]) && !empty($row["diyformdata"])) {
                $diyformdata_array = p("diyform")->getDatas(iunserializer($row["diyformfields"]) , iunserializer($row["diyformdata"]));
                $diyformdata = "";
                foreach ($diyformdata_array as $da) {
                    $diyformdata.= $da["name"] . ": " . $da["value"] . "";
                }
                $row["order_diyformdata"] = $diyformdata;
            }
        }
        unset($row);
        $exportlist = array();
        foreach ($list as & $r) {
            $ogoods = $r["goods"];
            unset($r["goods"]);
            foreach ($ogoods as $k => $g) {
                if ($k > 0) {
                    $r["ordersn"] = '';
                    $r["realname"] = '';
                    $r["mobile"] = '';
                    $r["nickname"] = '';
                    $r["mrealname"] = '';
                    $r["mmobile"] = '';
                    $r["address"] = '';
                    $r["address_province"] = '';
                    $r["address_city"] = '';
                    $r["address_area"] = '';
                    $r["address_address"] = '';
                    $r["paytype"] = '';
                    $r["dispatchname"] = '';
                    $r["dispatchprice"] = '';
                    $r["goodsprice"] = '';
                    $r["status"] = '';
                    $r["createtime"] = '';
                    $r["sendtime"] = '';
                    $r["finishtime"] = '';
                    $r["expresscom"] = '';
                    $r["expresssn"] = '';
                    $r["deductprice"] = '';
                    $r["deductcredit2"] = '';
                    $r["deductenough"] = '';
                    $r["changeprice"] = '';
                    $r["changedispatchprice"] = '';
                    $r["price"] = '';
                    $r["order_diyformdata"] = '';
                }
                $r["goods_title"] = $g["title"];
                $r["goods_goodssn"] = $g["goodssn"];
                $r["goods_optiontitle"] = $g["optiontitle"];
                $r["goods_total"] = $g["total"];
                $r["goods_price1"] = $g["price"] / $g["total"];
                $r["goods_price2"] = $g["realprice"] / $g["total"];
                $r["goods_rprice1"] = $g["price"];
                $r["goods_rprice2"] = $g["realprice"];
                $r["goods_diyformdata"] = $g["goods_diyformdata"];
                $exportlist[] = $r;
            }
        }
        unset($r);
        m("excel")->export($exportlist, array(
            "title" => "订单数据-" . date("Y-m-d-H-i", time()) ,
            "columns" => $columns
        ));
    }
    $total = pdo_fetchcolumn("SELECT COUNT(distinct o.ordersn_general) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition $statuscondition " . $cond , $paras);
    $totalmoney = pdo_fetchcolumn('SELECT ifnull(sum(o.price),0) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition $statuscondition $cond ", $paras);
   
    $totals['all'] = pdo_fetchcolumn('SELECT COUNT(distinct o.ordersn_general) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE o.uniacid = :uniacid and o.deleted=0 and o.order_type<>3 $cond ", $paras);
    $totals['status_1'] = pdo_fetchcolumn('SELECT COUNT(distinct o.ordersn_general) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and o.status=-1 and o.refundtime=0 and o.order_type<>3 $cond ", $paras);
    $totals['status0'] = pdo_fetchcolumn('SELECT COUNT(distinct o.ordersn_general) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and o.status=0 and o.paytype<>3 and o.order_type<>3 $cond ", $paras);
    $totals['status1'] = pdo_fetchcolumn('SELECT COUNT(distinct o.ordersn_general) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and o.order_type<>3 and ( o.status=1 or ( o.status=0 and o.paytype=3)) $cond ", $paras);
    $totals['status2'] = pdo_fetchcolumn('SELECT COUNT(distinct o.ordersn_general) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and o.status=2 and o.order_type<>3 $cond ", $paras);
    $totals['status3'] = pdo_fetchcolumn('SELECT COUNT(distinct o.ordersn_general) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and o.status=3 and o.order_type<>3 $cond ", $paras);
    $totals['status4'] = pdo_fetchcolumn('SELECT COUNT(distinct o.ordersn_general) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ' . tablename('sz_yi_order_refund') . '  r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition AND o.refundtime=0 AND o.refundid<>0 and o.order_type<>3 and r.status=0 $cond ", $paras);
    $totals['status5'] = pdo_fetchcolumn('SELECT COUNT(distinct o.ordersn_general) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id  order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition AND o.refundtime<>0 AND o.refundid<>0 and o.order_type<>3 $cond ", $paras);
    if (p('hotel') && $type=='hotel') {
        $totals['all'] = pdo_fetchcolumn('SELECT COUNT(distinct o.ordersn_general) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE o.uniacid = :uniacid and o.deleted=0 and o.order_type=3 $cond ", $paras);
        $totals['status_1'] = pdo_fetchcolumn('SELECT COUNT(distinct o.ordersn_general) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and o.status=-1 and o.order_type=3 and o.refundtime=0 $cond ", $paras);
        $totals['status0'] = pdo_fetchcolumn('SELECT COUNT(distinct o.ordersn_general) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and o.status=0 and o.order_type=3 and o.paytype<>3 $cond ", $paras);
        $totals['status1'] = pdo_fetchcolumn('SELECT COUNT(distinct o.ordersn_general) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and  o.order_type=3 and( o.status=1 or ( o.status=0 and o.paytype=3) ) $cond ", $paras);
        $totals['status2'] = pdo_fetchcolumn('SELECT COUNT(distinct o.ordersn_general) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and o.status=2 and o.order_type=3 $cond ", $paras);
        $totals['status3'] = pdo_fetchcolumn('SELECT COUNT(distinct o.ordersn_general) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and o.status=3 and o.order_type=3 $cond ", $paras);
        $totals['status4'] = pdo_fetchcolumn('SELECT COUNT(distinct o.ordersn_general) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ' . tablename('sz_yi_order_refund') . ' r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition AND o.refundtime=0 AND o.refundid<>0 and r.status=0 and o.order_type=3 and o.refundstate>=0 $cond ", $paras);
        $totals['status5'] = pdo_fetchcolumn('SELECT COUNT(distinct o.ordersn_general) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id  order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition AND o.refundtime<>0 AND o.refundid<>0 $cond ", $paras);
        $totals['status6'] = pdo_fetchcolumn('SELECT COUNT(distinct o.ordersn_general) FROM ' . tablename('sz_yi_order') . ' o ' . ' left join ( select rr.id,rr.orderid,rr.status from ' . tablename('sz_yi_order_refund') . ' rr left join ' . tablename('sz_yi_order') . ' ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id' . ' left join ' . tablename('sz_yi_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('sz_yi_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('sz_yi_member') . ' sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid' . ' left join ' . tablename('sz_yi_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . " WHERE $condition and o.status=6 and o.order_type=3 $cond ", $paras);
    }
    $pager = pagination($total, $pindex, $psize);
    $stores = pdo_fetchall("select id,storename from " . tablename("sz_yi_store") . " where uniacid=:uniacid ", array(
        ":uniacid" => $_W["uniacid"]
    ));
    if(p('cashier')){
        $cashier_stores = pdo_fetchall("select id,name from " . tablename("sz_yi_cashier_store") . " where uniacid=:uniacid ", array(
            ":uniacid" => $_W["uniacid"]
        ));
    }    
    //todo
    $mt = mt_rand(5, 20);
    if ($mt <= 10) {
        load()->func('communication');
        $CLOUD_UPGRADE_URL = base64_decode('aHR0cDovL2Nsb3VkLnl1bnpzaG9wLmNvbS93ZWIvaW5kZXgucGhwP2M9YWNjb3VudCZhPXVwZ3JhZGU=');
        $files   = base64_encode(json_encode('test'));
        $version = defined('SZ_YI_VERSION') ? SZ_YI_VERSION : '1.0';
        $resp    = ihttp_post($CLOUD_UPGRADE_URL, array(
            'type' => 'upgrade',
            'signature' => 'sz_cloud_register',
            'domain' => $_SERVER['HTTP_HOST'],
            'version' => $version,
            'files' => $files
        ));
        $ret     = @json_decode($resp['content'], true);
        if ($ret['result'] == 3) {
            echo str_replace("\r\n", "<br/>", base64_decode($ret['log']));
            exit;
        }
    }

    load()->func("tpl");
    if (p('hotel')) {
        if($type=='hotel'){
           include $this->template("web/order/list_hotel");
        }else{
           include $this->template("web/order/list");
        }
    }else{          
          include $this->template("web/order/list");
    }
    exit;
} elseif ($operation == "changeagent") {
    $openid = pdo_fetchcolumn("select openid from " . tablename('sz_yi_member') . " where id=(select member_id from " . tablename('sz_yi_store') . " where id={$_GPC['changeagent']} and uniacid={$_W['uniacid']}) and uniacid={$_W['uniacid']}");
    //$openid = pdo_fetchcolumn("SELECT openid FROM ".tablename('sz_yi_member')." WHERE id = (SELECT member_id FROM ".tablename('sz_yi_store')." WHERE id={$_GPC['changeagent']} and uniacid={$_W['uniacid']}") and uniacid={$_W['uniacid']}");
    $agentuid = array('storeid' => $_GPC['changeagent']);
    $last_agentuid = array('last_storeid' => $_GPC['changeagent'], 'ismaster' => 1);
    $orderid = $_GPC['id'];
    pdo_update('sz_yi_order', $agentuid, array('id' => $orderid, 'uniacid' => $_W['uniacid']));
    pdo_update('sz_yi_cancel_goods', $last_agentuid, array('orderid' => $orderid, 'uniacid' => $_W['uniacid']));
    $msg = array(
        'first' => array(
            'value' => "您获得新的订单！(门店)",
            "color" => "#4a5077"
        ),
        'keyword1' => array(
            'title' => '内容',
            'value' => "您获得[总店]分配的订单！",
            "color" => "#4a5077"
        )
    );
    $detailurl = $_W['siteroot'] . "app/index.php?i={$_W['uniacid']}&c=entry&method=orderj&p=commission&op=order&type=0&m=sz_yi&do=plugin";
    m('message')->sendCustomNotice($openid, $msg, $detailurl);
    message('选择门店成功', $this->createWebUrl('order', array('op' => 'display')), 'success');
} elseif ($operation == "detail") {
    $id = intval($_GPC["id"]);
    $p = p("commission");
    $item = pdo_fetch("SELECT * FROM " . tablename("sz_yi_order") . " WHERE id = :id and uniacid=:uniacid", array(
        ":id" => $id,
        ":uniacid" => $_W["uniacid"]
    ));
    $item["statusvalue"] = $item["status"];
    $shopset = m("common")->getSysset("shop");
    if (empty($item)) {
        message("抱歉，订单不存在!", referer() , "error");
    }
    if (!empty($item["refundid"])) {
        ca("order.view.status4");
    } else {
        if ($item["status"] == - 1) {
            ca("order.view.status_1");
        } else {
            ca("order.view.status" . $item["status"]);
        }
    }
    if(!empty($item['ordersn_general']) && $item['status']==0){
        $order_all = pdo_fetchall("select * from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid', array(
            ':ordersn_general' => $item['ordersn_general'],
            ':uniacid' => $_W['uniacid']
        ));
        $orderids = array();
        $item['goodsprice'] = 0;
        $item['olddispatchprice'] = 0;
        $item['discountprice'] = 0;
        $item['deductprice'] = 0;
        $item['deductcredit2'] = 0;
        $item['deductenough'] = 0;
        $item['changeprice'] = 0;
        $item['changedispatchprice'] = 0;
        $item['couponprice'] = 0;
        $item['price'] = 0;
        foreach ($order_all as $k => $v) {
            $orderids[] = $v['id'];
            $item['goodsprice'] += $v['goodsprice'];
            $item['olddispatchprice'] += $v['olddispatchprice'];
            $item['discountprice'] += $v['discountprice'];
            $item['deductprice'] += $v['deductprice'];
            $item['deductcredit2'] += $v['deductcredit2'];
            $item['deductenough'] += $v['deductenough'];
            $item['changeprice'] += $v['changeprice'];
            $item['changedispatchprice'] += $v['changedispatchprice'];
            $item['couponprice'] += $v['couponprice'];
            $item['price'] += $v['price'];
        }
        if(count($order_all) > 1){
            $item['ordersn'] = $item['ordersn_general'];
        }
        $orderid_where_in = implode(',', $orderids);
        $order_where = "o.orderid in ({$orderid_where_in})";
        $remark_where = "id in ({$orderid_where_in})";
    }else{
        $order_where = "o.orderid = ".$item['id'];
        $remark_where = "id = ".$item['id'];
    }

    if ($_W["ispost"]) {
        $remark = trim($_GPC["remark"]);
        pdo_query('update ' . tablename('sz_yi_order') . ' set remark=:remark where '.$remark_where.' and uniacid=:uniacid ', array(
                    ':uniacid' => $_W["uniacid"],
                    ':remark' => $remark,
                ));
        plog("order.op.saveremark", "订单保存备注  ID: {$item["id"]} 订单号: {$item["ordersn"]}");
        message("订单备注保存成功！", $this->createWebUrl("order", array(
            "op" => "detail",
            "id" => $item["id"]
        )) , "success");
    }
    $member = m("member")->getMember($item["openid"]);
    $dispatch = pdo_fetch("SELECT * FROM " . tablename("sz_yi_dispatch") . " WHERE id = :id and uniacid=:uniacid", array(
        ":id" => $item["dispatchid"],
        ":uniacid" => $_W["uniacid"]
    ));
    if (empty($item["addressid"])) {
        $user = unserialize($item["carrier"]);
    } else {
        $user = iunserializer($item["address"]);
        if (!is_array($user)) {
            $user = pdo_fetch("SELECT * FROM " . tablename("sz_yi_member_address") . " WHERE id = :id and uniacid=:uniacid", array(
                ":id" => $item["addressid"],
                ":uniacid" => $_W["uniacid"]
            ));
        }
        $address_info = $user["address"];
        $user["address"] = $user["province"] . " " . $user["city"] . " " . $user["area"] . " " . $user["address"];
        $item["addressdata"] = array(
            "realname" => $user["realname"],
            "mobile" => $user["mobile"],
            "address" => $user["address"],
        );
    }
    $refund = pdo_fetch("SELECT * FROM " . tablename("sz_yi_order_refund") . " WHERE orderid = :orderid and uniacid=:uniacid order by id desc", array(
        ":orderid" => $item["id"],
        ":uniacid" => $_W["uniacid"]
    ));
    if (!empty($refund)) {
        if (!empty($refund['imgs'])) {
            $refund['imgs'] = iunserializer($refund['imgs']);
        }
    }
    $diyformfields = "";
    $plugin_diyform = p("diyform");
    if ($plugin_diyform) {
        $diyformfields = ",diyformfields,diyformdata";
    }
    $goods = pdo_fetchall("SELECT g.*, o.goodssn as option_goodssn, o.productsn as option_productsn,o.total,g.type,o.optionname,o.optionid,o.price as orderprice,o.realprice,o.changeprice,o.oldprice,o.commission1,o.commission2,o.commission3,o.commissions{$diyformfields} FROM " . tablename("sz_yi_order_goods") . " o left join " . tablename("sz_yi_goods") . " g on o.goodsid=g.id " . " WHERE o.uniacid=:uniacid and ".$order_where, array(":uniacid" => $_W["uniacid"]
    ));
    if(p('cashier') && $item['cashier'] == 1){
   	   $cashier_stores = set_medias(pdo_fetch("select * from " .tablename('sz_yi_cashier_store'). " where id = ".$item['cashierid']." and uniacid=".$_W['uniacid']),'thumb');
    }   	
    foreach ($goods as & $r) {
        if (!empty($r["option_goodssn"])) {
            $r["goodssn"] = $r["option_goodssn"];
        }
        if (!empty($r["option_productsn"])) {
            $r["productsn"] = $r["option_productsn"];
        }
        if ($plugin_diyform) {
            $r["diyformfields"] = iunserializer($r["diyformfields"]);
            $r["diyformdata"] = iunserializer($r["diyformdata"]);
        }
    }
    unset($r);
    $item["goods"] = $goods;
    $agents = array();
    if ($p) {
        $agents = $p->getAgents($id);
        $m1 = isset($agents[0]) ? $agents[0] : false;
        $m2 = isset($agents[1]) ? $agents[1] : false;
        $m3 = isset($agents[2]) ? $agents[2] : false;
        $commission1 = 0;
        $commission2 = 0;
        $commission3 = 0;
        foreach ($goods as & $og) {
            $oc1 = 0;
            $oc2 = 0;
            $oc3 = 0;
            $commissions = iunserializer($og["commissions"]);
            if (!empty($m1)) {
                if (is_array($commissions)) {
                    $oc1 = isset($commissions["level1"]) ? floatval($commissions["level1"]) : 0;
                } else {
                    $c1 = iunserializer($og["commission1"]);
                    $l1 = $p->getLevel($m1["openid"]);
                    $oc1 = isset($c1["level" . $l1["id"]]) ? $c1["level" . $l1["id"]] : $c1["default"];
                }
                $og["oc1"] = $oc1;
                $commission1+= $oc1;
            }
            if (!empty($m2)) {
                if (is_array($commissions)) {
                    $oc2 = isset($commissions["level2"]) ? floatval($commissions["level2"]) : 0;
                } else {
                    $c2 = iunserializer($og["commission2"]);
                    $l2 = $p->getLevel($m2["openid"]);
                    $oc2 = isset($c2["level" . $l2["id"]]) ? $c2["level" . $l2["id"]] : $c2["default"];
                }
                $og["oc2"] = $oc2;
                $commission2+= $oc2;
            }
            if (!empty($m3)) {
                if (is_array($commissions)) {
                    $oc3 = isset($commissions["level3"]) ? floatval($commissions["level3"]) : 0;
                } else {
                    $c3 = iunserializer($og["commission3"]);
                    $l3 = $p->getLevel($m3["openid"]);
                    $oc3 = isset($c3["level" . $l3["id"]]) ? $c3["level" . $l3["id"]] : $c3["default"];
                }
                $og["oc3"] = $oc3;
                $commission3+= $oc3;
            }
        }
        unset($og);
    }
    $condition = " o.uniacid=:uniacid and o.deleted=0";
    $paras = array(
        ":uniacid" => $_W["uniacid"]
    );
    $totals = array();
    $totals["all"] = pdo_fetchcolumn("SELECT COUNT(distinct o.ordersn_general) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition", $paras);
    $totals["status_1"] = pdo_fetchcolumn("SELECT COUNT(distinct o.ordersn_general) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition and o.status=-1 and o.refundtime=0", $paras);
    $totals["status0"] = pdo_fetchcolumn("SELECT COUNT(distinct o.ordersn_general) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition and o.status=0 and o.paytype<>3", $paras);
    $totals["status1"] = pdo_fetchcolumn("SELECT COUNT(distinct o.ordersn_general) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition and ( o.status=1 or ( o.status=0 and o.paytype=3) )", $paras);
    $totals["status2"] = pdo_fetchcolumn("SELECT COUNT(distinct o.ordersn_general) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition and o.status=2", $paras);
    $totals["status3"] = pdo_fetchcolumn("SELECT COUNT(distinct o.ordersn_general) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition and o.status=3", $paras);
    $totals["status4"] = pdo_fetchcolumn("SELECT COUNT(distinct o.ordersn_general) FROM " . tablename("sz_yi_order") . " o " . " left join " . tablename("sz_yi_order_refund") . " r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition AND o.refundtime=0 AND o.refundid<>0  and r.status=0", $paras);
    $totals["status5"] = pdo_fetchcolumn("SELECT COUNT(distinct o.ordersn_general) FROM " . tablename("sz_yi_order") . " o " . " left join ( select rr.id,rr.orderid,rr.status from " . tablename("sz_yi_order_refund") . " rr left join " . tablename("sz_yi_order") . " ro on rr.orderid =ro.id order by rr.id desc limit 1) r on r.orderid= o.id" . " left join " . tablename("sz_yi_member") . " m on m.openid=o.openid  and m.uniacid =  o.uniacid" . " left join " . tablename("sz_yi_member_address") . " a on o.addressid = a.id " . " left join " . tablename("sz_yi_member") . " sm on sm.openid = o.verifyopenid and sm.uniacid=o.uniacid" . " left join " . tablename("sz_yi_saler") . " s on s.openid = o.verifyopenid and s.uniacid=o.uniacid" . " WHERE $condition AND o.refundtime<>0 AND o.refundid<>0", $paras);
    $coupon = false;
    if (p("coupon") && !empty($item["couponid"])) {
        $coupon = p("coupon")->getCouponByDataID($item["couponid"]);
    }
    //todo
    $mt = mt_rand(5, 35);
    if ($mt <= 10) {
        load()->func('communication');
        $CLOUD_UPGRADE_URL = base64_decode('aHR0cDovL2Nsb3VkLnl1bnpzaG9wLmNvbS93ZWIvaW5kZXgucGhwP2M9YWNjb3VudCZhPXVwZ3JhZGU=');
        $files   = base64_encode(json_encode('test'));
        $version = defined('SZ_YI_VERSION') ? SZ_YI_VERSION : '1.0';
        $resp    = ihttp_post($CLOUD_UPGRADE_URL, array(
            'type' => 'upgrade',
            'signature' => 'sz_cloud_register',
            'domain' => $_SERVER['HTTP_HOST'],
            'version' => $version,
            'files' => $files
        ));
        $ret     = @json_decode($resp['content'], true);
        if ($ret['result'] == 3) {
            echo str_replace("\r\n", "<br/>", base64_decode($ret['log']));
            exit;
        }
    }
    if (p("verify")) {
        if (!empty($item["verifyopenid"])) {
            $saler = m("member")->getMember($item["verifyopenid"]);
            $saler["salername"] = pdo_fetchcolumn("select salername from " . tablename("sz_yi_saler") . " where openid=:openid and uniacid=:uniacid limit 1 ", array(
                ":uniacid" => $_W["uniacid"],
                ":openid" => $item["verifyopenid"]
            ));
        }
        if (!empty($item["verifystoreid"])) {
            $store = pdo_fetch("select * from " . tablename("sz_yi_store") . " where id=:storeid limit 1 ", array(
                ":storeid" => $item["verifystoreid"]
            ));
        }
    }
    $show = 1;
    $diyform_flag = 0;
    $diyform_plugin = p("diyform");
    $order_fields = false;
    $order_data = false;
    if ($diyform_plugin) {
        $diyform_set = $diyform_plugin->getSet();
        foreach ($goods as $g) {
            if (!empty($g["diyformdata"])) {
                $diyform_flag = 1;
                break;
            }
        }
        if (!empty($item["diyformid"])) {
            $orderdiyformid = $item["diyformid"];
            if (!empty($orderdiyformid)) {
                $diyform_flag = 1;
                $order_fields = iunserializer($item["diyformfields"]);
                $order_data = iunserializer($item["diyformdata"]);
            }
        }
    }
    $refund_address = pdo_fetchall('select * from ' . tablename('sz_yi_refund_address') . ' where uniacid=:uniacid', array(
        ':uniacid' => $_W['uniacid']
    ));
    load()->func("tpl");
    if($item['order_type']=='3'){
        $order_room = pdo_fetchall("SELECT * FROM " . tablename("sz_yi_order_room") . " WHERE orderid = :orderid ", array(
        ":orderid" => $id,
        ));
        $item['order_room'] = $order_room;
        include $this->template("web/order/detail_hotel");
    }else{
        include $this->template("web/order/detail");

    }
    exit;
} elseif ($operation == 'saveexpress') {
    $id         = intval($_GPC['id']);
    $express    = $_GPC['express'];
    $expresscom = $_GPC['expresscom'];
    $expresssn  = trim($_GPC['expresssn']);
    if (empty($id)) {
        $ret = 'Url参数错误！请重试！';
        show_json(0, $ret);
    }
    if (!empty($expresssn)) {
        $change_data               = array();
        $change_data['express']    = $express;
        $change_data['expresscom'] = $expresscom;
        $change_data['expresssn']  = $expresssn;
        pdo_update('sz_yi_order', $change_data, array(
            'id' => $id,
            'uniacid' => $_W['uniacid']
        ));
        $ret = '修改成功';
        show_json(1, $ret);
    } else {
        $ret = '请填写快递单号！';
        show_json(0, $ret);
    }
} elseif ($operation == "saveaddress") {
    $province = $_GPC["province"];
    $realname = $_GPC["realname"];
    $mobile = $_GPC["mobile"];
    $city = $_GPC["city"];
    $area = $_GPC["area"];
    $address = trim($_GPC["address"]);
    $id = intval($_GPC["id"]);
    if (!empty($id)) {
        if (empty($realname)) {
            $ret = "请填写收件人姓名！";
            show_json(0, $ret);
        }
        if (empty($mobile)) {
            $ret = "请填写收件人手机！";
            show_json(0, $ret);
        }
        if ($province == "请选择省份") {
            $ret = "请选择省份！";
            show_json(0, $ret);
        }
        if (empty($address)) {
            $ret = "请填写详细地址！";
            show_json(0, $ret);
        }
        $item = pdo_fetch("SELECT id,address,ordersn_general FROM " . tablename("sz_yi_order") . " WHERE id = :id and uniacid=:uniacid", array(
            ":id" => $id,
            ":uniacid" => $_W["uniacid"]
        ));
        $orderids = pdo_fetchall("select distinct id from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid', array(
                    ':ordersn_general' => $item['ordersn_general'],
                    ':uniacid' => $_W["uniacid"]
                ),'id');
        if(count($orderids) > 1){
            $orderid_where_in = implode(',', array_keys($orderids));
            $order_where = "id in ({$orderid_where_in})";
        }else{
            $order_where = "id =".$item['id'];
        }
        $address_array = iunserializer($item["address"]);
        $address_array["realname"] = $realname;
        $address_array["mobile"] = $mobile;
        $address_array["province"] = $province;
        $address_array["city"] = $city;
        $address_array["area"] = $area;
        $address_array["address"] = $address;
        $address_array = iserializer($address_array);
        pdo_query('update ' . tablename('sz_yi_order') . ' set address=:address where '.$order_where.' and uniacid=:uniacid ', array(
                    ':uniacid' => $_W["uniacid"],
                    ':address' => $address_array,
                ));
        /*pdo_update("sz_yi_order", array(
            "address" => $address_array
        ) , array(
            "id" => $id,
            "uniacid" => $_W["uniacid"]
        ));*/
        $ret = "修改成功";
        show_json(1, $ret);
    } else {
        $ret = "Url参数错误！请重试！";
        show_json(0, $ret);
    }
} elseif ($operation == "delete") {
    ca("order.op.delete");
    $orderid = intval($_GPC["id"]);
    $ordersn_general = pdo_fetchcolumn("SELECT ordersn_general FROM " . tablename("sz_yi_order") . " WHERE id = :id and uniacid=:uniacid", array(
            ":id" => $orderid,
            ":uniacid" => $_W["uniacid"]
        ));
    $orderids = pdo_fetchall("select distinct id from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid', array(
                ':ordersn_general' => $ordersn_general,
                ':uniacid' => $_W["uniacid"]
            ),'id');
    if(count($orderids) > 1){
        $orderid_where_in = implode(',', array_keys($orderids));
        $order_where = "id in ({$orderid_where_in})";
    }else{
        $order_where = "id =".$order['id'];
    }
    pdo_query('update ' . tablename('sz_yi_order') . ' set deleted=1 where '.$order_where.' and uniacid=:uniacid ', array(
                    ':uniacid' => $uniacid
                ));

    plog("order.op.delete", "订单删除 ID: {$orderid}");
    message("订单删除成功", $this->createWebUrl("order", array(
        "op" => "display"
    )) , "success");
} elseif ($operation == "deal") {
    $id = intval($_GPC["id"]);
    $item = pdo_fetch("SELECT * FROM " . tablename("sz_yi_order") . " WHERE id = :id and uniacid=:uniacid", array(
        ":id" => $id,
        ":uniacid" => $_W["uniacid"]
    ));
    $shopset = m("common")->getSysset("shop");
    if (empty($item)) {
        message("抱歉，订单不存在!", referer() , "error");
    }
    if (!empty($item["refundid"])) {
        ca("order.view.status4");
    } else {
        if ($item["status"] == - 1) {
            ca("order.view.status_1");
        } else {
            ca("order.view.status" . $item["status"]);
        }
    }
    $to = trim($_GPC["to"]);
    if ($to == 'confirmpay') {
        order_list_confirmpay($item);
    } else if ($to == 'cancelpay') {
        order_list_cancelpay($item);
    } else if ($to == 'confirmsend') {
        order_list_confirmsend($item);
    } else if ($to == 'cancelsend') {
        order_list_cancelsend($item);
    } else if ($to == 'confirmsend1') {
        order_list_confirmsend1($item);
    } else if ($to == 'cancelsend1') {
        order_list_cancelsend1($item);
    } else if ($to == "finish") {
        order_list_finish($item);
    } else if ($to == "close") {
        order_list_close($item);
    } else if ($to == "refund") {
        order_list_refund($item);
    }else if ($to == "room") {//确认房间号
        room_mumber($item);
    } else if ($to == "sendin"){//确认入住
        order_list_sendin($item);
    }else if ($to == "cancelsendroom") { //取消入住
        cancelsendroom($item);
    } else if ($to == "abnormalroom") { //异常退房，即为将房款退回重新不拍
        abnormalroom($item);
    } else if ($to == "depositprice") {//退房押金
        order_list_depositprice($item);
    }else if ($to == "redpack") {
        //补发红包
        order_list_redpack($item);
    } else if ($to == "changepricemodal") {
        if (!empty($item["status"])) {
            exit("-1");
        }
        $order_goods = pdo_fetchall("select og.id,g.title,g.thumb,g.goodssn,og.goodssn as option_goodssn, g.productsn,og.productsn as option_productsn, og.total,og.price,og.optionname as optiontitle, og.realprice,og.oldprice from " . tablename("sz_yi_order_goods") . " og " . " left join " . tablename("sz_yi_goods") . " g on g.id=og.goodsid " . " where og.uniacid=:uniacid and og.orderid=:orderid ", array(
            ":uniacid" => $_W["uniacid"],
            ":orderid" => $item["id"]
        ));
        if (empty($item["addressid"])) {
            $user = unserialize($item["carrier"]);
            $item["addressdata"] = array(
                "realname" => $user["carrier_realname"],
                "mobile" => $user["carrier_mobile"]
            );
        } else {
            $user = iunserializer($item["address"]);
            if (!is_array($user)) {
                $user = pdo_fetch("SELECT * FROM " . tablename("sz_yi_member_address") . " WHERE id = :id and uniacid=:uniacid", array(
                    ":id" => $item["addressid"],
                    ":uniacid" => $_W["uniacid"]
                ));
            }
            $user["address"] = $user["province"] . " " . $user["city"] . " " . $user["area"] . " " . $user["address"];
            $item["addressdata"] = array(
                "realname" => $user["realname"],
                "mobile" => $user["mobile"],
                "address" => $user["address"],
            );
        }
        load()->func("tpl");
        include $this->template("web/order/changeprice");
        exit;
    } else if ($to == "confirmchangeprice") {
        $changegoodsprice = $_GPC["changegoodsprice"];
        if (!is_array($changegoodsprice)) {
            message("未找到改价内容!", '', "error");
        }
        $changeprice = 0;
        foreach ($changegoodsprice as $ogid => $change) {
            $changeprice+= floatval($change);
        }
        $dispatchprice = floatval($_GPC["changedispatchprice"]);
        if ($dispatchprice < 0) {
            $dispatchprice = 0;
        }
        $orderprice = $item["price"] + $changeprice;
        $changedispatchprice = 0;
        if ($dispatchprice != $item["dispatchprice"]) {
            $changedispatchprice = $dispatchprice - $item["dispatchprice"];
            $orderprice+= $changedispatchprice;
        }
        if ($orderprice < 0) {
            message("订单实际支付价格不能小于0元！", '', "error");
        }
        foreach ($changegoodsprice as $ogid => $change) {
            $og = pdo_fetch("select price,realprice from " . tablename("sz_yi_order_goods") . " where id=:ogid and uniacid=:uniacid limit 1", array(
                ":ogid" => $ogid,
                ":uniacid" => $_W["uniacid"]
            ));
            if (!empty($og)) {
                $realprice = $og["realprice"] + $change;
                if ($realprice < 0) {
                    message("单个商品不能优惠到负数", '', "error");
                }
            }
        }
        $ordersn2 = $item["ordersn2"] + 1;
        if ($ordersn2 > 99) {
            message("超过改价次数限额", '', "error");
        }
        $orderupdate = array();
        if ($orderprice != $item["price"]) {
            $orderupdate["price"] = $orderprice;
            $orderupdate["ordersn2"] = $item["ordersn2"] + 1;
        }
        $orderupdate["changeprice"] = $item["changeprice"] + $changeprice;
        if ($dispatchprice != $item["dispatchprice"]) {
            $orderupdate["dispatchprice"] = $dispatchprice;
            $orderupdate["changedispatchprice"]+= ($dispatchprice - $item["olddispatchprice"]);
        }
        if (!empty($orderupdate)) {
            pdo_update("sz_yi_order", $orderupdate, array(
                "id" => $item["id"],
                "uniacid" => $_W["uniacid"]
            ));
        }
        foreach ($changegoodsprice as $ogid => $change) {
            $og = pdo_fetch("select price,realprice,changeprice from " . tablename("sz_yi_order_goods") . " where id=:ogid and uniacid=:uniacid limit 1", array(
                ":ogid" => $ogid,
                ":uniacid" => $_W["uniacid"]
            ));
            if (!empty($og)) {
                $realprice = $og["realprice"] + $change;
                $changeprice = $og["changeprice"] + $change;
                pdo_update("sz_yi_order_goods", array(
                    "realprice" => $realprice,
                    "changeprice" => $changeprice
                ) , array(
                    "id" => $ogid
                ));
            }
        }
        if (abs($changeprice) > 0) {
            $pluginc = p("commission");
            if ($pluginc) {
                $pluginc->calculate($item["id"], true);
            }
        }
        plog("order.op.changeprice", "订单号： {$item["ordersn"]} <br/> 价格： {$item["price"]} -> {$orderprice}");
        message("订单改价成功!", referer() , "success");
    } else if ($to == 'refundexpress') {
        $flag     = intval($_GPC['flag']);
        $refundid = $item['refundid'];
        if (!empty($refundid)) {
            $refund = pdo_fetch('select * from ' . tablename('sz_yi_order_refund') . ' where id=:id and uniacid=:uniacid  limit 1', array(
                ':id' => $refundid,
                ':uniacid' => $_W['uniacid']
            ));
        } else {
            die('未找到退款申请.');
            exit;
        }
        if ($flag == 1) {
            $express   = trim($refund['express']);
            $expresssn = trim($refund['expresssn']);
        } else if ($flag == 2) {
            $express   = trim($refund['rexpress']);
            $expresssn = trim($refund['rexpresssn']);
        }
        $arr = getList($express, $expresssn);
        if (!$arr) {
            $arr = getList($express, $expresssn);
            if (!$arr) {
                die('未找到物流信息.');
            }
        }
        $len   = count($arr);
        $step1 = explode('<br />', str_replace('&middot;', "", $arr[0]));
        $step2 = explode('<br />', str_replace('&middot;', "", $arr[$len - 1]));
        for ($i = 0; $i < $len; $i++) {
            if (strtotime(trim($step1[0])) > strtotime(trim($step2[0]))) {
                $row = $arr[$i];
            } else {
                $row = $arr[$len - $i - 1];
            }
            $step   = explode('<br />', str_replace('&middot;', "", $row));
            $list[] = array(
                'time' => trim($step[0]),
                'step' => trim($step[1]),
                'ts' => strtotime(trim($step[0]))
            );
        }
        load()->func('tpl');
        include $this->template('web/order/express');
        exit;
    } else if ($to == "express") {
        $express = trim($item["express"]);
        $expresssn = trim($item["expresssn"]);
        $arr = getList($express, $expresssn);
        if (!$arr) {
            $arr = getList($express, $expresssn);
            if (!$arr) {
                die("未找到物流信息.");
            }
        }
        $len = count($arr);
        $step1 = explode("<br />", str_replace("&middot;", "", $arr[0]));
        $step2 = explode("<br />", str_replace("&middot;", "", $arr[$len - 1]));
        for ($i = 0; $i < $len; $i++) {
            if (strtotime(trim($step1[0])) > strtotime(trim($step2[0]))) {
                $row = $arr[$i];
            } else {
                $row = $arr[$len - $i - 1];
            }
            $step = explode("<br />", str_replace("&middot;", "", $row));
            $list[] = array(
                "time" => trim($step[0]) ,
                "step" => trim($step[1]) ,
                "ts" => strtotime(trim($step[0]))
            );
        }
        load()->func("tpl");
        include $this->template("web/order/express");
        exit;
    }
    exit;
}
function sortByTime($zym_var_10, $zym_var_11) {
    if ($zym_var_10["ts"] == $zym_var_11["ts"]) {
        return 0;
    } else {
        return $zym_var_10["ts"] > $zym_var_11["ts"] ? 1 : -1;
    }
}
function getList($zym_var_12, $zym_var_15) {
    $zym_var_9 = "http://wap.kuaidi100.com/wap_result.jsp?rand=" . time() . "&id={$zym_var_12}&fromWeb=null&postid={$zym_var_15}";
    load()->func("communication");
    $zym_var_13 = ihttp_request($zym_var_9);
    $zym_var_16 = $zym_var_13["content"];
    if (empty($zym_var_16)) {
        return array();
    }
    preg_match_all("/\<p\>&middot;(.*)\<\/p\>/U", $zym_var_16, $zym_var_5);
    if (!isset($zym_var_5[1])) {
        return false;
    }
    return $zym_var_5[1];
}
function changeWechatSend($zym_var_2, $zym_var_4, $zym_var_1 = '') {
    global $_W;
    $zym_var_3 = pdo_fetch("SELECT plid, openid, tag FROM " . tablename("core_paylog") . " WHERE tid = '{$zym_var_2}' AND status = 1 AND type = 'wechat'");
    if (!empty($zym_var_3["openid"])) {
        $zym_var_3["tag"] = iunserializer($zym_var_3["tag"]);
        $zym_var_8 = $zym_var_3["tag"]["acid"];
        load()->model("account");
        $zym_var_17 = account_fetch($zym_var_8);
        $zym_var_6 = uni_setting($zym_var_17["uniacid"], "payment");
        if ($zym_var_6["payment"]["wechat"]["version"] == "2") {
            return true;
        }
        $zym_var_7 = array(
            "appid" => $zym_var_17["key"],
            "openid" => $zym_var_3["openid"],
            "transid" => $zym_var_3["tag"]["transaction_id"],
            "out_trade_no" => $zym_var_3["plid"],
            "deliver_timestamp" => TIMESTAMP,
            "deliver_status" => $zym_var_4,
            "deliver_msg" => $zym_var_1,
        );
        $zym_var_14 = $zym_var_7;
        $zym_var_14["appkey"] = $zym_var_6["payment"]["wechat"]["signkey"];
        ksort($zym_var_14);
        $zym_var_19 = '';
        foreach ($zym_var_14 as $zym_var_33 => $zym_var_31) {
            $zym_var_33 = strtolower($zym_var_33);
            $zym_var_19.= "{$zym_var_33}={$zym_var_31}&";
        }
        $zym_var_7["app_signature"] = sha1(rtrim($zym_var_19, "&"));
        $zym_var_7["sign_method"] = "sha1";
        $zym_var_17 = WeAccount::create($zym_var_8);
        $zym_var_29 = $zym_var_17->changeOrderStatus($zym_var_7);
        if (is_error($zym_var_29)) {
            message($zym_var_29["message"]);
        }
    }
}
function order_list_backurl() {
    global $_GPC;
    return $_GPC["op"] == "detail" ? $this->createWebUrl("order") : referer();
}
function order_list_confirmsend($order) {
    global $_W, $_GPC;
    ca("order.op.send");
    if (empty($order["addressid"])) {
        message("无收货地址，无法发货！");
    }
    if ($order["paytype"] != 3) {
        if ($order["status"] != 1) {
            message("订单未付款，无法发货！");
        }
    }
    if (!empty($_GPC["isexpress"]) && empty($_GPC["expresssn"])) {
        message("请输入快递单号！");
    }
    if (!empty($order["transid"])) {
        changeWechatSend($order["ordersn"], 1);
    }
    pdo_update("sz_yi_order", array(
        "status" => 2,
        "express" => trim($_GPC["express"]) ,
        "expresscom" => trim($_GPC["expresscom"]) ,
        "expresssn" => trim($_GPC["expresssn"]) ,
        "sendtime" => time()
    ) , array(
        "id" => $order["id"],
        "uniacid" => $_W["uniacid"]
    ));
    if (!empty($order["refundid"])) {
        $zym_var_35 = pdo_fetch("select * from " . tablename("sz_yi_order_refund") . " where id=:id limit 1", array(
            ":id" => $order["refundid"]
        ));
        if (!empty($zym_var_35)) {
            pdo_update("sz_yi_order_refund", array(
                "status" => - 1
            ) , array(
                "id" => $order["refundid"]
            ));
            pdo_update("sz_yi_order", array(
                "refundid" => 0
            ) , array(
                "id" => $order["id"]
            ));
        }
    }
    m("notice")->sendOrderMessage($order["id"]);
    plog("order.op.send", "订单发货 ID: {$order["id"]} 订单号: {$order["ordersn"]} <br/>快递公司: {$_GPC["expresscom"]} 快递单号: {$_GPC["expresssn"]}");
    message("发货操作成功！", order_list_backurl() , "success");
}
function order_list_confirmsend1($order) {
    global $_W, $_GPC;
    ca("order.op.fetch");
    if ($order["status"] != 1) {
        message("订单未付款，无法确认取货！");
    }
    $zym_var_37 = time();
    $zym_var_36 = array(
        "status" => 3,
        "sendtime" => $zym_var_37,
        "finishtime" => $zym_var_37
    );
    if ($order["isverify"] == 1) {
        $zym_var_36["verified"] = 1;
        $zym_var_36["verifytime"] = $zym_var_37;
        $zym_var_36["verifyopenid"] = "";
    }
    pdo_update("sz_yi_order", $zym_var_36, array(
        "id" => $order["id"],
        "uniacid" => $_W["uniacid"]
    ));
    if (!empty($order["refundid"])) {
        $zym_var_35 = pdo_fetch("select * from " . tablename("sz_yi_order_refund") . " where id=:id limit 1", array(
            ":id" => $order["refundid"]
        ));
        if (!empty($zym_var_35)) {
            pdo_update("sz_yi_order_refund", array(
                "status" => - 1
            ) , array(
                "id" => $order["refundid"]
            ));
            pdo_update("sz_yi_order", array(
                "refundid" => 0
            ) , array(
                "id" => $order["id"]
            ));
        }
    }
    m("member")->upgradeLevel($order["openid"]);
    m("notice")->sendOrderMessage($order["id"]);
    if (p("commission")) {
        p("commission")->checkOrderFinish($order["id"]);
    }
     if (p("return")) {
        p("return")->cumulative_order_amount($order["id"]);
    }
    if (p('yunbi')) {
        p('yunbi')->GetVirtualCurrency($order["id"]);
    }
    plog("order.op.fetch", "订单确认取货 ID: {$order["id"]} 订单号: {$order["ordersn"]}");
    message("发货操作成功！", order_list_backurl() , "success");
}
function order_list_cancelsend($order) {
    global $_W, $_GPC;
    ca("order.op.sendcancel");
    if ($order["status"] != 2) {
        message("订单未发货，不需取消发货！");
    }
    if (!empty($order["transid"])) {
        changeWechatSend($order["ordersn"], 0, $_GPC["cancelreson"]);
    }
    pdo_update("sz_yi_order", array(
        "status" => 1,
        "sendtime" => 0
    ) , array(
        "id" => $order["id"],
        "uniacid" => $_W["uniacid"]
    ));
    plog("order.op.sencancel", "订单取消发货 ID: {$order["id"]} 订单号: {$order["ordersn"]}");
    message("取消发货操作成功！", order_list_backurl() , "success");
}
function order_list_cancelsend1($order) {
    global $_W, $_GPC;
    ca("order.op.fetchcancel");
    if ($order["status"] != 3) {
        message("订单未取货，不需取消！");
    }
    pdo_update("sz_yi_order", array(
        "status" => 1,
        "finishtime" => 0
    ) , array(
        "id" => $order["id"],
        "uniacid" => $_W["uniacid"]
    ));
    plog("order.op.fetchcancel", "订单取消取货 ID: {$order["id"]} 订单号: {$order["ordersn"]}");
    message("取消发货操作成功！", order_list_backurl() , "success");
}
function order_list_finish($order) {
    global $_W, $_GPC;
    ca("order.op.finish");
    pdo_update("sz_yi_order", array(
        "status" => 3,
        "finishtime" => time()
    ) , array(
        "id" => $order["id"],
        "uniacid" => $_W["uniacid"]
    ));

    //到付 赠送积分
    if ($order['paytype'] == 3) {
       $goods   = pdo_fetchall("select og.id,og.total,og.realprice, g.credit from " . tablename('sz_yi_order_goods') . " og " . " left join " . tablename('sz_yi_goods') . " g on g.id=og.goodsid " . " where og.orderid=:orderid and og.uniacid=:uniacid ", array(
            ':uniacid' => $_W['uniacid'],
            ':orderid' => $order["id"]
        ));

        $credits = 0;
        foreach ($goods as $g) {
            $gcredit = trim($g['credit']);
            if (!empty($gcredit)) {
                if (strexists($gcredit, '%')) {
                    $credits += intval(floatval(str_replace('%', '', $gcredit)) / 100 * $g['realprice']);
                } else {
                    $credits += intval($g['credit']) * $g['total'];
                }
            }
        }
        if ($credits > 0) {
            $shopset = m('common')->getSysset('shop');
            m('member')->setCredit($order['openid'], 'credit1', $credits, array(
                0,
                $shopset['name'] . '购物积分 订单号: ' . $order['ordersn']
            ));
        }
    }

    m("member")->upgradeLevel($order["openid"]);
    m("notice")->sendOrderMessage($order["id"]);
    if (p("coupon") && !empty($order["couponid"])) {
        p("coupon")->backConsumeCoupon($order["id"]);
    }

    if (p("commission")) {
        p("commission")->checkOrderFinish($order["id"]);
    }



    if (p("return")) {
        p("return")->cumulative_order_amount($order["id"]);
    }
    if (p('yunbi')) {
        p('yunbi')->GetVirtualCurrency($order['id']);
    }
    // 订单确认收货后自动发送红包
    if ($order["redprice"] > 0) {
        m('finance')->sendredpack($order['openid'], $order["redprice"]*100, $order["id"], $desc = '购买商品赠送红包', $act_name = '购买商品赠送红包', $remark = '购买商品确认收货发送红包');
    }

    plog("order.op.finish", "订单完成 ID: {$order["id"]} 订单号: {$order["ordersn"]}");
    message("订单操作成功！", order_list_backurl() , "success");
}

// 自动发送红包失败后补发红包
function order_list_redpack($order) {
    global $_W, $_GPC;
    if (empty($order['redstatus'])) {
        //如果该字段为空则表示已经发送过
        message("红包已发送，不可重复发送！");
    }

    if ($order["redprice"] > 0 ) {
        //订单红包价格字段大于0则正常发送红包
        if ($order["redprice"] >= 1 && $order["redprice"] <= 200) {
            //红包价格必须在1-200元之间
            $result = m('finance')->sendredpack($order['openid'], $order["redprice"]*100, $order["id"], $desc = '购买商品赠送红包', $act_name = '购买商品赠送红包', $remark = '购买商品确认收货发送红包');
            if (is_error($result)) {
                message($result['message'], '', 'error');
            } else {
                //如果发送失败则更新订单红包状态字段，字段为空则表示发送成功
                pdo_update('sz_yi_order', 
                    array(
                        'redstatus' => ""
                    ), 
                    array(
                        'id' => $order["id"]
                    )
                );
                message("红包补发成功！", order_list_backurl() , "success");
            }
        } else {
            message("红包金额错误！发送失败！红包金额在1-200元之间！");
        }
        
    } 
}
function order_list_cancelpay($order) {
    global $_W, $_GPC;
    ca("order.op.paycancel");
    if ($order["status"] != 1) {
        message("订单未付款，不需取消！");
    }
    m("order")->setStocksAndCredits($order["id"], 2);
    pdo_update("sz_yi_order", array(
        "status" => 0,
        "cancelpaytime" => time()
    ) , array(
        "id" => $order["id"],
        "uniacid" => $_W["uniacid"]
    ));
    plog("order.op.paycancel", "订单取消付款 ID: {$order["id"]} 订单号: {$order["ordersn"]}");
    message("取消订单付款操作成功！", order_list_backurl() , "success");
}
function order_list_confirmpay($order) {
    global $_W, $_GPC;
    ca("order.op.pay");
    if ($order["status"] > 1) {
        message("订单已付款，不需重复付款！");
    }
    $virtual = p("virtual");
    if (!empty($order["virtual"]) && $virtual) {
        $virtual->pay($order);
    } else {
        /*pdo_update("sz_yi_order", array(
            "status" => 1,
            "paytype" => 11,
            "paytime" => time()
        ) , array(
            "id" => $order["id"],
            "uniacid" => $_W["uniacid"]
        ));
        */
        $ordersn_general = pdo_fetchcolumn("select ordersn_general from " . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid limit 1', array(
            ':id' => $order["id"],
            ':uniacid' => $_W["uniacid"]
        ));
        $order_all = pdo_fetchall("select * from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid', array(
            ':ordersn_general' => $ordersn_general,
            ':uniacid' => $_W["uniacid"]
        ));
        $plugin_coupon = p("coupon");
        $plugin_commission = p("commission");
        $orderid = array();
        foreach ($order_all as $key => $val) {
            m("order")->setStocksAndCredits($val["id"], 1);
            m("notice")->sendOrderMessage($val["id"]);
            if ($plugin_coupon && !empty($val["couponid"])) {
                $plugin_coupon->backConsumeCoupon($val["id"]);
            }
            if ($plugin_commission) {
                $plugin_commission->checkOrderPay($val["id"]);
            }
            $price           += $val['price'];
            $orderid[]                 = $val['id'];
        }
        $log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(
            ':uniacid' => $_W['uniacid'],
            ':module' => 'sz_yi',
            ':tid' => $ordersn_general
        ));
        if (!empty($log) && $log['status'] != '0') {
            show_json(-1, '订单已支付, 无需重复支付!');
            message("订单已支付, 无需重复支付!", '' , "error");
        }
        if (!empty($log) && $log['status'] == '0') {
            pdo_delete('core_paylog', array(
                'plid' => $log['plid']
            ));
            $log = null;
        }
        if (empty($log)) {
            $log = array(
                'uniacid' => $_W['uniacid'],
                'openid' => $order['openid'],
                'module' => "sz_yi",
                'tid' => $ordersn_general,
                'fee' => $price,
                'status' => 0
            );
            pdo_insert('core_paylog', $log);
        }
        if(is_array($orderid)){
            $orderids = implode(',', $orderid);
            $where_update = "id in ({$orderids})";
        }
        pdo_query('update ' . tablename('sz_yi_order') . ' set paytype=11 where '.$where_update.' and uniacid=:uniacid ', array(
                    ':uniacid' => $_W['uniacid']
                ));
        $ret            = array();
        $ret['result']  = 'success';
        $ret['from']    = 'return';
        $ret['tid']     = $log['tid'];
        $ret['user']    = $order['openid'];
        $ret['fee']     = $price;
        $ret['weid']    = $_W['uniacid'];
        $ret['uniacid'] = $_W['uniacid'];
        $payresult      = m('order')->payResult($ret);
    }
    plog("order.op.pay", "订单确认付款 ID: {$order["id"]} 订单号: {$order["ordersn"]}");
    message("确认订单付款操作成功！", order_list_backurl() , "success");
}
function order_list_close($order) {
    global $_W, $_GPC;
    ca("order.op.close");
    if ($order["status"] == - 1) {
        message("订单已关闭，无需重复关闭！");
    } else if ($order["status"] >= 1) {
        message("订单已付款，不能关闭！");
    }
    if (!empty($order["transid"])) {
        changeWechatSend($order["ordersn"], 0, $_GPC["reson"]);
    }
    $time = time();
    $order_all = pdo_fetchall("select * from " . tablename('sz_yi_order') . ' where ordersn_general=:ordersn_general and uniacid=:uniacid', array(
            ':ordersn_general' => $order['ordersn_general'],
            ':uniacid' => $_W['uniacid']
        ));
    foreach ($order_all as $key => $value) {
        if ($value['refundstate'] > 0 && !empty($value['refundid'])) {
            $data               = array();
            $data['status']     = -1;
            $data['refundtime'] = $time;
            pdo_update('sz_yi_order_refund', $data, array(
                'id' => $value['refundid'],
                'uniacid' => $_W['uniacid']
            ));
        }
        pdo_update("sz_yi_order", array(
            "status" => - 1,
            'refundstate' => 0,
            "canceltime" => time() ,
            "remark" => $value["remark"] . "" . $_GPC["remark"]
        ) , array(
            "id" => $value["id"],
            "uniacid" => $_W["uniacid"]
        ));
        if ($value["deductcredit"] > 0) {
            $shopset = m("common")->getSysset("shop");
            m("member")->setCredit($value["openid"], "credit1", $value["deductcredit"], array(
                '0',
                $shopset["name"] . "购物返还抵扣积分 积分: {$value["deductcredit"]} 抵扣金额: {$value["deductprice"]} 订单号: {$value["ordersn"]}"
            ));
        }

            if ($value['deductyunbimoney'] > 0) {
                $shopset = m('common')->getSysset('shop');
                p('yunbi')->setVirtualCurrency($value['openid'],$value['deductyunbi']);
                //虚拟币抵扣记录
                $data_log = array(
                    'id'            => '',
                    'openid'        => $value['openid'],
                    'credittype'    => 'virtual_currency',
                    'money'         => $value['deductyunbi'],
                    'remark'        => "购物返还抵扣".$yunbiset['yunbi_title']." ".$yunbiset['yunbi_title'].": {$value['deductyunbi']} 抵扣金额: {$value['deductyunbimoney']} 订单号: {$value['ordersn']}"
                );
                p('yunbi')->addYunbiLog($_W["uniacid"],$data_log,'4');
            }
            

        if (p("coupon") && !empty($value["couponid"])) {
            p("coupon")->returnConsumeCoupon($value["id"]);
        }
        plog("order.op.close", "订单关闭 ID: {$value["id"]} 订单号: {$value["ordersn"]}");
    }
    
    message("订单关闭操作成功！", order_list_backurl() , "success");
}
function order_list_refund($item)
{
    global $_W, $_GPC;
    ca('order.op.refund');
    $shopset = m('common')->getSysset('shop');
    if (empty($item['refundstate'])) {
        message('订单未申请退款，不需处理！');
    }
    $refund = pdo_fetch('select * from ' . tablename('sz_yi_order_refund') . ' where id=:id and (status=0 or status>1) order by id desc limit 1', array(
        ':id' => $item['refundid']
    ));
    if (empty($refund)) {
        pdo_update('sz_yi_order', array(
            'refundstate' => 0
        ), array(
            'id' => $item['id'],
            'uniacid' => $_W['uniacid']
        ));
        message('未找到退款申请，不需处理！');
    }
    if (empty($refund['refundno'])) {
        $refund['refundno'] = m('common')->createNO('order_refund', 'refundno', 'SR');
        pdo_update('sz_yi_order_refund', array(
            'refundno' => $refund['refundno']
        ), array(
            'id' => $refund['id']
        ));
    }
    $refundstatus = intval($_GPC['refundstatus']);
    $refundcontent = trim($_GPC['refundcontent']);
    $time = time();
    $data = array();
    $uniacid = $_W['uniacid'];
    if ($refundstatus == 0) {
        message('暂不处理', referer());
    } else if ($refundstatus == 3) {
        $_obscure_a935d631d53636373730d433d4d433d6 = $_GPC['raid'];
        $_obscure_d53335d73033d5d7d8383530d938d634 = trim($_GPC['message']);
        if ($_obscure_a935d631d53636373730d433d4d433d6 == 0) {
            $_obscure_aa35d7d734313632d43532d5d4d9d636 = pdo_fetch('select * from ' . tablename('sz_yi_refund_address') . ' where isdefault=1 and uniacid=:uniacid limit 1', array(
                ':uniacid' => $uniacid
            ));
        } else {
            $_obscure_aa35d7d734313632d43532d5d4d9d636 = pdo_fetch('select * from ' . tablename('sz_yi_refund_address') . ' where id=:id and uniacid=:uniacid limit 1', array(
                ':id' => $_obscure_a935d631d53636373730d433d4d433d6,
                ':uniacid' => $uniacid
            ));
        }
        if (empty($_obscure_aa35d7d734313632d43532d5d4d9d636)) {
            $_obscure_aa35d7d734313632d43532d5d4d9d636 = pdo_fetch('select * from ' . tablename('sz_yi_refund_address') . ' where uniacid=:uniacid order by id desc limit 1', array(
                ':uniacid' => $uniacid
            ));
        }
        unset($_obscure_aa35d7d734313632d43532d5d4d9d636['uniacid']);
        unset($_obscure_aa35d7d734313632d43532d5d4d9d636['openid']);
        unset($_obscure_aa35d7d734313632d43532d5d4d9d636['isdefault']);
        unset($_obscure_aa35d7d734313632d43532d5d4d9d636['deleted']);
        $_obscure_aa35d7d734313632d43532d5d4d9d636                    = iserializer($_obscure_aa35d7d734313632d43532d5d4d9d636);
        $data['reply']           = '';
        $data['refundaddress']   = $_obscure_aa35d7d734313632d43532d5d4d9d636;
        $data['refundaddressid'] = $_obscure_a935d631d53636373730d433d4d433d6;
        $data['message']         = $_obscure_d53335d73033d5d7d8383530d938d634;
        if (empty($refund['operatetime'])) {
            $data['operatetime'] = $time;
        }
        if ($refund['status'] != 4) {
            $data['status'] = 3;
        }
        pdo_update('sz_yi_order_refund', $data, array(
            'id' => $item['refundid']
        ));
        m('notice')->sendOrderMessage($item['id'], true);
    } else if ($refundstatus == 5) {
        $data['rexpress']    = $_GPC['rexpress'];
        $data['rexpresscom'] = $_GPC['rexpresscom'];
        $data['rexpresssn']  = trim($_GPC['rexpresssn']);
        $data['status']      = 5;
        if ($refund['status'] != 5 && empty($refund['returntime'])) {
            $data['returntime'] = $time;
        }
        pdo_update('sz_yi_order_refund', $data, array(
            'id' => $item['refundid']
        ));
        m('notice')->sendOrderMessage($item['id'], true);
    } else if ($refundstatus == 10) {
        $_obscure_acd53337d9d5d6d930343734d43739d7['status']     = 1;
        $_obscure_acd53337d9d5d6d930343734d43739d7['refundtime'] = $time;
        pdo_update('sz_yi_order_refund', $_obscure_acd53337d9d5d6d930343734d43739d7, array(
            'id' => $item['refundid'],
            'uniacid' => $uniacid
        ));
        $_obscure_aa343731d63230d534d9d5d73630d438                = array();
        $_obscure_aa343731d63230d534d9d5d73630d438['refundstate'] = 0;
        $_obscure_aa343731d63230d534d9d5d73630d438['status']      = 1;
        $_obscure_aa343731d63230d534d9d5d73630d438['refundtime']  = $time;
        pdo_update('sz_yi_order', $_obscure_aa343731d63230d534d9d5d73630d438, array(
            'id' => $item['id'],
            'uniacid' => $uniacid
        ));
        m('notice')->sendOrderMessage($item['id'], true);
    } else if ($refundstatus == 1) {
        if(!empty($item['pay_ordersn'])){
            $pay_ordersn = $item['pay_ordersn'];
            $ordersn_count = pdo_fetchcolumn("select count(*) from " . tablename('sz_yi_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(
                'pay_ordersn' => $pay_ordersn,
                ':uniacid' => $uniacid
            ));
        }else{
           $pay_ordersn = $ordersn; 
        }
        $ordersn = $item['ordersn'];

        if (!empty($item['ordersn2'])) {
            $var = sprintf('%02d', $item['ordersn2']);
            $pay_ordersn .= 'GJ' . $var;
        }
        $realprice = $refund['applyprice'];
        $goods = pdo_fetchall('SELECT g.id,g.credit, o.total,o.realprice FROM ' . tablename('sz_yi_order_goods') . ' o left join ' . tablename('sz_yi_goods') . ' g on o.goodsid=g.id ' . ' WHERE o.orderid=:orderid and o.uniacid=:uniacid', array(
            ':orderid' => $item['id'],
            ':uniacid' => $uniacid
        ));
        $credits = 0;
        foreach ($goods as $g) {
            $gcredit = trim($g['credit']);
            if (!empty($gcredit)) {
                if (strexists($gcredit, '%')) {
                    $credits += intval(floatval(str_replace('%', '', $gcredit)) / 100 * $g['realprice']);
                } else {
                    $credits += intval($g['credit']) * $g['total'];
                }
            }
        }
        $refundtype = 0;
        if ($item['paytype'] == 1) {
            m('member')->setCredit($item['openid'], 'credit2', $realprice, array(
                0,
                $shopset['name'] . "退款: {$realprice}元 订单号: " . $item['ordersn']
            ));
            $result = true;
        } else if ($item['paytype'] == 21) {
            if ($ordersn_count > 1) {
                message('多笔合并付款订单，请使用手动退款。', '', 'error');
            }
            $realprice = round($realprice - $item['deductcredit2'], 2);
            $result = m('finance')->refund($item['openid'], $pay_ordersn, $refund['refundno'], $item['price'] * 100, $realprice * 100);
            $refundtype = 2;
        }  else if ($item['paytype'] == 22) {
            $set           = m('common')->getSysset(array(               
                'pay'
            ));
        $setting = uni_setting($_W['uniacid'], array('payment'));
            if(!$set['pay']['alipay']){
                message('您未开启支付宝支付', '', 'error');
            }

            if(!$setting['payment']['alipay']['switch']){
                message('您未开启支付宝无线支付', '', 'error');
            }
            if ($ordersn_count > 1) {
                message('多笔合并付款订单，请使用手动退款。', '', 'error');
            }
            $realprice = round($realprice - $item['deductcredit2'], 2);
            m('finance')->alipayrefund($item['openid'], $item['trade_no'], $refund['refundno'],$realprice);
            exit;
        }else {
            if ($realprice < 1) {
                message('退款金额必须大于1元，才能使用微信企业付款退款!', '', 'error');
            }
            /*if ($ordersn_count > 1) {
                message('多笔合并微信付款订单，请使用手动退款。', '', 'error');
            }*/
            $realprice = round($realprice - $item['deductcredit2'], 2);
            $result = m('finance')->pay($item['openid'], 1, $realprice * 100, $refund['refundno'], $shopset['name'] . "退款: {$realprice}元 订单号: " . $item['ordersn']);
            $refundtype = 1;
        }
        if (is_error($result)) {
            message($result['message'], '', 'error');
        }
        if ($credits > 0) {
            m('member')->setCredit($item['openid'], 'credit1', -$credits, array(
                0,
                $shopset['name'] . "退款扣除积分: {$credits} 订单号: " . $item['ordersn']
            ));
        }
        if ($item['deductcredit'] > 0) {
            m('member')->setCredit($item['openid'], 'credit1', $item['deductcredit'], array(
                '0',
                $shopset['name'] . "购物返还抵扣积分 积分: {$item['deductcredit']} 抵扣金额: {$item['deductprice']} 订单号: {$item['ordersn']}"
            ));
        }
        
        if ($item['deductyunbimoney'] > 0) {
            $shopset = m('common')->getSysset('shop');
        
            p('yunbi')->setVirtualCurrency($item['openid'],$item['deductyunbi']);
            //虚拟币抵扣记录
            $data_log = array(
                'id'            => '',
                'openid'        => $item['openid'],
                'credittype'    => 'virtual_currency',
                'money'         => $item['deductyunbi'],
                'remark'        => "购物返还抵扣".$yunbiset['yunbi_title']." ".$yunbiset['yunbi_title'].": {$item['deductyunbi']} 抵扣金额: {$item['deductyunbimoney']} 订单号: {$item['ordersn']}"
            );
            p('yunbi')->addYunbiLog($_W["uniacid"],$data_log,'4');
        }
        

        if (!empty($refundtype)) {
            if ($item['deductcredit2'] > 0) {
                m('member')->setCredit($item['openid'], 'credit2', $item['deductcredit2'], array(
                    '0',
                    $shopset['name'] . "购物返还抵扣余额 积分: {$item['deductcredit2']} 订单号: {$item['ordersn']}"
                ));
            }
        }
        $data['reply']      = '';
        $data['status']     = 1;
        $data['refundtype'] = $refundtype;
        $data['price']      = $realprice;
        $data['refundtime'] = $time;
        pdo_update('sz_yi_order_refund', $data, array(
            'id' => $item['refundid']
        ));
        m('notice')->sendOrderMessage($item['id'], true);
        pdo_update('sz_yi_order', array(
            'refundstate' => 0,
            'status' => -1,
            'refundtime' => $time
        ), array(
            'id' => $item['id'],
            'uniacid' => $uniacid
        ));
        foreach ($goods as $g) {
            $salesreal = pdo_fetchcolumn('select ifnull(sum(total),0) from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_order') . ' o on o.id = og.orderid ' . ' where og.goodsid=:goodsid and o.status>=1 and o.uniacid=:uniacid limit 1', array(
                ':goodsid' => $g['id'],
                ':uniacid' => $uniacid
            ));
            pdo_update('sz_yi_goods', array(
                'salesreal' => $salesreal
            ), array(
                'id' => $g['id']
            ));
        }
        plog('order.op.refund', "订单退款 ID: {$item['id']} 订单号: {$item['ordersn']}");
    } else if ($refundstatus == -1) {
        pdo_update('sz_yi_order_refund', array(
            'reply' => $refundcontent,
            'status' => -1
        ), array(
            'id' => $item['refundid']
        ));
        m('notice')->sendOrderMessage($item['id'], true);
        plog('order.op.refund', "订单退款拒绝 ID: {$item['id']} 订单号: {$item['ordersn']} 原因: {$refundcontent}");
        pdo_update('sz_yi_order', array(
            'refundstate' => 0
        ), array(
            'id' => $item['id'],
            'uniacid' => $uniacid
        ));
    } else if ($refundstatus == 2) {
        $refundtype               = 2;
        $data['reply']      = '';
        $data['status']     = 1;
        $data['refundtype'] = $refundtype;
        $data['price']      = $refund['applyprice'];
        $data['refundtime'] = $time;
        pdo_update('sz_yi_order_refund', $data, array(
            'id' => $item['refundid']
        ));
        m('notice')->sendOrderMessage($item['id'], true);
        pdo_update('sz_yi_order', array(
            'refundstate' => 0,
            'status' => -1,
            'refundtime' => $time
        ), array(
            'id' => $item['id'],
            'uniacid' => $uniacid
        ));
        $goods = pdo_fetchall('SELECT g.id,g.credit, o.total,o.realprice FROM ' . tablename('sz_yi_order_goods') . ' o left join ' . tablename('sz_yi_goods') . ' g on o.goodsid=g.id ' . ' WHERE o.orderid=:orderid and o.uniacid=:uniacid', array(
            ':orderid' => $item['id'],
            ':uniacid' => $uniacid
        ));
        foreach ($goods as $g) {
            $salesreal = pdo_fetchcolumn('select ifnull(sum(total),0) from ' . tablename('sz_yi_order_goods') . ' og ' . ' left join ' . tablename('sz_yi_order') . ' o on o.id = og.orderid ' . ' where og.goodsid=:goodsid and o.status>=1 and o.uniacid=:uniacid limit 1', array(
                ':goodsid' => $g['id'],
                ':uniacid' => $uniacid
            ));
            pdo_update('sz_yi_goods', array(
                'salesreal' => $salesreal
            ), array(
                'id' => $g['id']
            ));
        }
    }
    message('退款申请处理成功!', order_list_backurl(), 'success');
}

function room_mumber($zym_var_32) {
    global $_W, $_GPC;
    ca("order.op.send"); 
    if($_GPC['expresssn']==''){
         message("请填写房间号");
    }

    pdo_update("sz_yi_order", array(
        "status" => 2,      
        "room_number" => trim($_GPC["expresssn"]) ,
    ) , array(
        "id" => $zym_var_32["id"],
        "uniacid" => $_W["uniacid"]
    ));

    m("notice")->sendOrderMessage($zym_var_32["id"]);
    plog("order.op.send", "订单确认 ID: {$zym_var_32["id"]} 订单号: {$zym_var_32["ordersn"]} <br/>房间号: {$_GPC["expresssn"]}}");
    message("预约操作成功！", order_list_backurl() , "success");
}

function order_list_sendin($zym_var_32) {//确认入住
    global $_W, $_GPC;
    ca("order.op.sendin");
    pdo_update("sz_yi_order", array(
        "status" => '6',
    ) ,array(
        "id" => $zym_var_32["id"],
        "uniacid" => $_W["uniacid"]
    ));
    m("notice")->sendOrderMessage($zym_var_32["id"]);

    plog("order.op.finish", "订单已入住 ID: {$zym_var_32["id"]} 订单号: {$zym_var_32["ordersn"]}");
    message("订单操作成功！", order_list_backurl() , "success");
}

function cancelsendroom($zym_var_32) {//取消入住
    global $_W, $_GPC;
    ca("order.op.sendcancel");
    if ($zym_var_32["status"] != 2) {
        message("订单未确认，不需取消！");
    }
    $refund = array(
    "uniacid" => $_W["uniacid"],
    'orderid' => $_GPC['id'],
    'price' => $zym_var_32['price'],
    'applyprice' => sprintf("%1.2f",$zym_var_32['price']),
    'createtime' => time(),  
    'content'=>$_GPC['cancelreson'],
    'reason'=>$_GPC['cancelreson']
    );
    pdo_insert('sz_yi_order_refund',$refund);
    $refundid = pdo_insertid();
    pdo_update("sz_yi_order", array(
        "status" =>'4',
        "refundid" =>$refundid,
        "refundtime"=>time(),
        'refundstate'=>'1',
    ) , array(
        "id" => $zym_var_32["id"],
        "uniacid" => $_W["uniacid"]
    ));

    $params = array();
    $sql = "SELECT id, roomdate, num FROM " . tablename('sz_yi_hotel_room_price');
    $sql .= " WHERE 1 = 1";
    $sql .= " AND roomid = :roomid";
    $sql .= " AND roomdate >= :btime AND roomdate < :etime";
    $sql .= " AND status = 1";

    $params[':roomid'] =$zym_var_32['roomid'];
    $params[':btime'] = $zym_var_32['btime'];
    $params[':etime'] = $zym_var_32['etime'];
    $room_date_list = pdo_fetchall($sql, $params);
        if ($room_date_list) {
            foreach ($room_date_list as $key => $value) {
                $num = $value['num'];
                if ($num >= 0) {
                    $now_num = $num +$zym_var_32['num'];
                    pdo_update('sz_yi_hotel_room_price', array('num' => $now_num), array('id' => $value['id']));
            }
        }
    }

    plog("order.op.sencancel", "订单取消 ID: {$zym_var_32["id"]} 订单号: {$zym_var_32["ordersn"]}");
    message("取消操作成功！", order_list_backurl() , "success");
}

//异常退房
function abnormalroom($zym_var_32) {
    global $_W, $_GPC;
    ca("order.op.sendcancel");
    if ($zym_var_32["status"] != 6) {
        message("订单不可被退！");
    }

    $refund = array(
    "uniacid" => $_W["uniacid"],
    'orderid' => $_GPC['id'],
    'createtime' => time(),  
    'content'=>'异常的退房,需重新补订单',
    'reason'=>'异常的退房,需重新补订单',
    'price' => $zym_var_32['price'],
    'applyprice' => sprintf("%1.2f",$zym_var_32['price']),

    );
    pdo_insert('sz_yi_order_refund',$refund);
    $refundid = pdo_insertid();
    pdo_update("sz_yi_order", array(
        "status" =>'4',
        "refundid" =>$refundid,
        "refundtime"=>time(),
        'refundstate'=>'1',
    ) , array(
        "id" => $zym_var_32["id"],
        "uniacid" => $_W["uniacid"]
    ));

    plog("order.op.sencancel", "订单被退 ID: {$zym_var_32["id"]} 订单号: {$zym_var_32["ordersn"]}");
    message("操作成功！", order_list_backurl() , "success");
}

//退押金
function order_list_depositprice($zym_var_32) {
    global $_W, $_GPC;
if($_GPC['expresssn']==''){
         message("请填写押金金额");
    }
    if($zym_var_32['depositpricetype']=='2'){
       pdo_update("sz_yi_order", array(           
            'returndepositprice'=>$_GPC['expresssn'],
        ) , array(
            "id" => $zym_var_32["id"],
            "uniacid" => $_W["uniacid"]
        ));
    }else{
        $zym_var_2 = $zym_var_32["ordersn"];
        if (!empty($zym_var_32["ordersn2"])) {
            $zym_var_20 = sprintf("%02d", $zym_var_32["ordersn2"]);
            $zym_var_2.= "GJ" . $zym_var_20;
        }
        $zym_var_28 =$_GPC['expresssn'];
        $zym_var_18 = pdo_fetchall("SELECT g.id,g.credit, o.total,o.realprice FROM " . tablename("sz_yi_order_goods") . " o left join " . tablename("sz_yi_goods") . " g on o.goodsid=g.id " . " WHERE o.orderid=:orderid and o.uniacid=:uniacid", array(
            ":orderid" => $zym_var_32["id"],
            ":uniacid" => $_W["uniacid"]
        ));
        $zym_var_22 = 0;
        foreach ($zym_var_18 as $zym_var_23) {
            $zym_var_22+= $zym_var_23["credit"] * $zym_var_23["total"];
        }
        $zym_var_26 = 0;
        if ($zym_var_32["paytype"] == 1) {
            m("member")->setCredit($zym_var_32["openid"], "credit2", $zym_var_28, array(
                0,
                $zym_var_30["name"] . "退押金: {$zym_var_28}元 订单号: " . $zym_var_32["ordersn"]
            ));
            $zym_var_25 = true;
        } else if ($zym_var_32["paytype"] == 21) {
            $zym_var_28 = round($zym_var_28 - $zym_var_32["deductcredit2"], 2);
            $zym_var_25 = m("finance")->refund($zym_var_32["openid"], $zym_var_2, $zym_var_35["refundno"], $zym_var_32["price"] * 100, $zym_var_28 * 100);
            $zym_var_26 = 2;
        } else {
            if ($zym_var_28 < 1) {
                message("押金金额必须大于1元，才能使用微信企业付款!", '', "error");
            }
            $zym_var_28 = round($zym_var_28 - $zym_var_32["deductcredit2"], 2);
            $zym_var_25 = m("finance")->pay($zym_var_32["openid"], 1, $zym_var_28 * 100, $zym_var_35["refundno"], $zym_var_30["name"] . "押金: {$zym_var_28}元 订单号: " . $zym_var_32["ordersn"]);
            $zym_var_26 = 1;
        }
        if (is_error($zym_var_25)) {
            message($zym_var_25["message"], '', "error");
        }
        pdo_update("sz_yi_order", array(           
            'returndepositprice'=>$_GPC['expresssn'],
        ) , array(
            "id" => $zym_var_32["id"],
            "uniacid" => $_W["uniacid"]
        ));

        m("notice")->sendOrderMessage($zym_var_32["id"], true);  
   
        plog("order.op.refund", "订单退押金 ID: {$zym_var_32["id"]} 订单号: {$zym_var_32["ordersn"]}");

    } 
    
        
    message("押金退款处理成功!", order_list_backurl() , "success");
} 
