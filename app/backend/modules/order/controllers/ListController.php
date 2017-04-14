<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/3/4
 * Time: 上午9:09
 */

namespace app\backend\modules\order\controllers;

use app\backend\modules\order\models\Order;
use app\backend\modules\order\models\OrderJoinOrderGoods;
use app\backend\modules\order\services\ExportService;
use app\common\components\BaseController;

use app\common\helpers\PaginationHelper;

class ListController extends BaseController
{
    /**
     * 页码
     */
    const PAGE_SIZE = 10;
    private $_order_model;
    public function index(){
        //phpinfo();
        $params = \YunShop::request()->get();
        $this->_order_model = Order::getAllOrders($params['search'],self::PAGE_SIZE);
        //dd($this->_order_model);
        return view('order.index', $this->getData())->render();
    }
    public function waitPay()
    {
        $params = \YunShop::request();
        
        $this->_order_model = Order::getWaitPayOrders($params['search'],self::PAGE_SIZE);
        return view('order.index', $this->getData())->render();
    }
    public function waitSend()
    {
        $params = \YunShop::request();
        
        $this->_order_model = Order::getWaitSendOrders($params['search'],self::PAGE_SIZE);
        return view('order.index', $this->getData())->render();
    }
    public function waitReceive()
    {
        $params = \YunShop::request();
        
        $this->_order_model = Order::getWaitReceiveOrders($params['search'],self::PAGE_SIZE);
        return view('order.index', $this->getData())->render();
    }
    public function completed()
    {
        $params = \YunShop::request();
        
        $this->_order_model = Order::getCompletedOrders($params['search'],self::PAGE_SIZE);
        return view('order.index', $this->getData())->render();
    }

    /**
     * @return mixed
     * 退换货订单
     */
    public function refund()
    {
        $params = \YunShop::request();
        $this->_order_model = Order::getRefundOrders($params['search'],self::PAGE_SIZE);
        return view('order.index', $this->getData())->render();
    }
    
    public function test()
    {
        $data = Order::getOrderCountGroupByStatus([Order::WAIT_PAY,Order::WAIT_SEND,Order::WAIT_RECEIVE,Order::COMPLETE]);
        dd($data);
    }



    private function getData(){
        /*$params = [
            'search' => [
                'ambiguous' => [
                    'field' => 'order_goods',
                    'string' => '春',
                ],
                'pay_type' => 1,
                'time_range' => [
                    'field' => 'create_time',
                    'range' => [1458425047, 1498425047]
                ]
            ]
        ];*/
        $requestSearch = \YunShop::request()->get('search');
        $requestSearch['plugin'] = 'fund';
        if ($requestSearch) {
            $requestSearch = array_filter($requestSearch, function ($item) {
                return !empty($item);
            });
        }
        $list = $this->_order_model;
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);
        //dd($list);
        $data = [
            'list' => $list,
            'total_price' => $list['total_price'],
            'lang' => $this->_lang(),
            'totals' => $this->_totals(),
            'pager' => $pager,
            'requestSearch' => $requestSearch,
            'var' => \YunShop::app()->get(),
            'url' => 'order.list'
        ];
        $data += $this->fakeData();
        return $data;
    }
    //假数据,配合模板修改
    private function fakeData()
    {
        return array(
            'supplierapply' => '',
            'stores' => '',
            'list' => '',
            'yunbiset' => '',
            'card_plugin' => '',
            'perm_role' => '',
            'sstarttime' => 0,
            'r_type' => '',
            'costmoney' => '',
            'card_set' => '',
            'liveRooms' => '',
            'paytype' => '',
            'sendtime' => 0,
            'cashier_stores' => '',
            'supplier' => '',
            'type' => '',
            'store' => '',
            'status' => '',
            'pendtime' => 0,
            'p_cashier' => '',
            'liveRoom' => '',
            'cashier_store' => '',
            'key' => '',
            'endtime' => 0,
            'fendtime' => 0,
            'shopset' => '',
            'pstarttime' => 0,
            'level' => '',
            'suppliers' => '',
            'request' => '',
            'fstarttime' => 0,
            'starttime' => 0,
            'agentid' => '',
        );
    }
    //假数据,配合模板修改
    private function _lang()
    {
        return array(
            'goods' => '商品',
            'good' => '商品',
            'orderlist' => '订单列表'
        );
    }
    //假数据,配合模板修改

    private function _totals()
    {
        return array(
            'index' => '30',
            'waitPay' => '3',
            'waitSend' => '2',
            'waitReceive' => '5',
            'complete' => '6',
            'close' => '7',
            'waitRefund' => '2',
            'refund' => '1',
            'applyWithdraw' => '4',
        );
    }

    public function export()
    {
        $params = \YunShop::request()->search;
        $orders = Order::getExportOrders($params);
        $export_class = new ExportService();
        $export_class->export($orders);
    }
}