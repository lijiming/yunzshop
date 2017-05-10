<?php
/**
 * Created by PhpStorm.
 * User: libaojia
 * Date: 2017/5/7
 * Time: 下午3:00
 */

namespace app\common\services\credit;


class ConstService
{

    const OPERATOR_SHOP     = 0;  //操作者 商城

    const OPERATOR_ORDER_   = -1; //操作者 订单

    const OPERRTOR_MEMBER   = -2; //操作者 会员

    //类型：收入
    const TYPE_INCOME = 1;

    //类型：支出
    const TYPE_EXPENDITURE = 2;

    //充值状态 ：成功
    const STATUS_SUCCESS = 1;

    //充值状态 ：失败
    const STATUS_FAILURE = -1;


        const BALANCE_RECHARGE          = 1; //充值

        const BALANCE_CONSUME           = 2; //消费

        const BALANCE_TRANSFER          = 3; //转让

        const BALANCE_DEDUCTION         = 4; //抵扣

        const BALANCE_AWARD             = 5; //奖励

        const BALANCE_WITHDRAWAL        = 6; //提现

        const BALANCE_INCOME            = 7; //提现至～～

        const BALANCE_CANCEL_DEDUCTION  = 8; //抵扣取消回滚

        const BALANCE_CANCEL_AWARD      = 9; //奖励取消回滚

        const BALANCE_CANCEL_CONSUME    = 10; //消费取消回滚


    private static $title = '余额';




    public function __construct($title)
    {
        static::$title              = $title ?: static::$title;
    }


    public function sourceComment()
    {
        return [
            self::BALANCE_RECHARGE              => static::$title . '充值',
            self::BALANCE_CONSUME               => static::$title . '消费',
            self::BALANCE_TRANSFER              => static::$title . '转让',
            self::BALANCE_DEDUCTION             => static::$title . '抵扣',
            self::BALANCE_AWARD                 => static::$title . '奖励',
            self::BALANCE_WITHDRAWAL            => static::$title . '提现',
            self::BALANCE_INCOME                => '提现至' . static::$title,
            self::BALANCE_CANCEL_DEDUCTION      => '抵扣取消回滚',
            self::BALANCE_CANCEL_AWARD          => '奖励取消回滚',
            self::BALANCE_CANCEL_CONSUME        => '消费取消回滚'
        ];
    }

    public function typeComment()
    {
        return [
            self::TYPE_INCOME                   => '收入',
            self::TYPE_EXPENDITURE              => '支出'
        ];
    }

    public function operatorComment()
    {
        return [
            self::OPERATOR_SHOP                 => '商城操作',
            self::OPERATOR_ORDER_               => '会员操作',
            self::OPERRTOR_MEMBER               => '订单操作'
        ];
    }
}
