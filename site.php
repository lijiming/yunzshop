<?php
/**
 * 芸众商城模块微站定义
 *
 * @url http://bbs.yunzshop.com/
 */
defined('IN_IA') or exit('Access Denied');

include __DIR__ . '/app/laravel.php';
//todo 什么时候重复调用的
include_once __DIR__ . '/app/yunshop.php';

class yun_shopModuleSite extends WeModuleSite
{

}
return new yun_shopModuleSite();
