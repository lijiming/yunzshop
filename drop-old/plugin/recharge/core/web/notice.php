<?php
global $_W, $_GPC;

ca('recharge.notice');
$set = $this->getSet('tm');
if (checksubmit('submit')) {
    $set['tm'] = is_array($_GPC['tm']) ? $_GPC['tm'] : array();
    $this->updateSet($set);
    plog('commission.notice', '修改通知设置');
    message('设置保存成功!', referer(), 'success');
}
load()->func('tpl');
include $this->template('notice');