<?php
if (!defined('IN_IA')) {
    exit('Access Denied');
}
function sortByCreateTime($a, $b)
{
    if ($a['createtime'] == $b['createtime']) {
        return 0;
    } else {
        return ($a['createtime'] < $b['createtime']) ? 1 : -1;
    }
}
class ChannelMobile extends Plugin
{
    protected $set = null;
    public function __construct()
    {
        parent::__construct('channel');
        $this->set = $this->getSet();
    }
    public function index()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }

}
