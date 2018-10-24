<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/5/22
 * Time: 16:39
 */

return [
    'buy_goods_msg' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '购买商品通知[卖家]',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有新一笔订单状态更新！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "订单通知",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "订单编号为：[订单编号]的订单状态更新了",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "会员：[会员昵称]购买的商品：[商品名称（含规格）]，商品金额为[商品金额]元，商品数量为[商品数量]，订单状态于[时间]更新为[订单状态]状态。",
                "color" => "#000000",
            ],
        ],
        'remark' => '请尽快处理您的订单！',

    ],
    'seller_order_create' => [
        'template_id_short' => 'OPENTM204958750',
        'title' => '订单生成通知[卖家]',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有新的订单！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "[订单金额]元",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "[商品详情（含规格）]",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "[订单号]",
                "color" => "#000000",
            ],
            3 => [
                "keywords" => "keyword4",
                "value" => "[粉丝昵称]",
                "color" => "#000000",
            ],
        ],
        'remark' => '请尽快处理您的订单！',

    ],
    'seller_order_pay' => [
        'template_id_short' => 'OPENTM207525131',
        'title' => '订单支付通知[卖家]',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您商城有新的付款订单！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "[粉丝昵称]",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "商品：[商品详情（含规格）]通过[支付方式]于[支付时间]支付了[订单金额]元，订单编号：[订单号]",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "[下单时间]",
                "color" => "#000000",
            ],
        ],
        'remark' => '请尽快处理！',

    ],
    'seller_order_finish' => [
        'template_id_short' => 'OPENTM413711838',
        'title' => '订单完成通知[卖家]',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您商城有新的付款订单！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "[订单号][商品详情（含规格）]",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "[粉丝昵称]于[确认收货时间]已成功确认收货",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "收件人姓名：[收件人姓名]；收件人电话：[收件人电话]；收件人地址：[收件人地址]",
                "color" => "#000000",
            ],
        ],
        'remark' => '请确认已完成订单！',

    ],
    'other_toggle_temp' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '订单两级消息通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '你有新的下级订单！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "订单通知",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "两级会员订单",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的用户，您的[下级层级]会员[下级昵称]下单购买了商品，订单编号：[订单号]，订单金额：[订单金额]元，订单状态为：[订单状态]",
                "color" => "#000000",
            ],
        ],
        'remark' => '请继续加油！',

    ],
    'order_submit_success' => [
        'template_id_short' => 'OPENTM200746866',
        'title' => '订单提交成功通知[买家]',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您的订单已提交成功',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "[商城名称]",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "[下单时间]",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "[商品详情（含规格）]",
                "color" => "#000000",
            ],
            3 => [
                "keywords" => "keyword4",
                "value" => "[订单金额]元",
                "color" => "#000000",
            ],
        ],
        'remark' => '您的订单已经成功提交，请尽快完成支付！',

    ],
    'order_pay_success' => [
        'template_id_short' => 'OPENTM204987032',
        'title' => '订单支付成功通知[买家]',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您已支付成功订单，我们将尽快处理您的订单！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "订单编号：[订单号]；商品：[商品详情（含规格）]",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "通过[支付方式]",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "[支付时间]",
                "color" => "#000000",
            ],
            3 => [
                "keywords" => "keyword4",
                "value" => "[商城名称]",
                "color" => "#000000",
            ],
            4 => [
                "keywords" => "keyword5",
                "value" => "[订单金额]元",
                "color" => "#000000",
            ],
        ],
        'remark' => '[商城名称]欢迎您的再次到来！',

    ],
    'order_send' => [
        'template_id_short' => 'OPENTM413713493',
        'title' => '订单发货通知[买家]',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您的订单已发货，请注意查收。',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "[订单号]",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "[商品详情（含规格）]",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "[快递公司]",
                "color" => "#000000",
            ],
            3 => [
                "keywords" => "keyword4",
                "value" => "[快递单号]",
                "color" => "#000000",
            ],
            4 => [
                "keywords" => "keyword5",
                "value" => "[发货时间]",
                "color" => "#000000",
            ],
        ],
        'remark' => '感谢您的惠顾。',

    ],
    'order_finish' => [
        'template_id_short' => 'OPENTM202521011',
        'title' => '订单确认收货通知[买家]',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '尊敬的用户您好，您的订单已完成。',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "[订单号]",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "[确认收货时间]",
                "color" => "#000000",
            ],
        ],
        'remark' => '如有任何疑问，请联系我们！',

    ],
    'order_refund_apply' => [
        'template_id_short' => 'OPENTM407277862',
        'title' => '退款申请通知[买家]',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您已申请退款，等待商家确认退款信息。',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "[退款单号]",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "[退款金额]元",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "[退款原因]",
                "color" => "#000000",
            ],
        ],
        'remark' => '如有任何疑问，请联系我们！',

    ],
    'order_cancel' => [
        'template_id_short' => 'OPENTM412815063',
        'title' => '订单取消通知[买家]',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '订单取消成功！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "[订单号]",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "[商品详情（含规格）]",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "[订单取消时间]",
                "color" => "#000000",
            ],
        ],
        'remark' => '如有任何疑问，请联系我们！',

    ],
    'order_refund_success' => [
        'template_id_short' => 'OPENTM412230724',
        'title' => '退款成功通知[买家]',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您好，您有一笔退款已经成功，请及时查看。',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "[退款单号]",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "[退款金额]元",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "[退款原因]",
                "color" => "#000000",
            ],
        ],
        'remark' => '感谢您关注，如有疑问请联系在线客服或点击查看详情',

    ],
    'order_refund_reject' => [
        'template_id_short' => 'OPENTM410602931',
        'title' => '退款申请驳回通知[买家]',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您的退款申请未能通过审核！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "[驳回原因]",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "[退款金额]元",
                "color" => "#000000",
            ],
        ],
        'remark' => '请重新提交审核。',

    ],
    'customer_upgrade' => [
        'template_id_short' => 'OPENTM400341556',
        'title' => '会员升级通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '尊敬的会员：[粉丝昵称]，您已经成功升级！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "[旧等级]",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "[新等级]",
                "color" => "#000000",
            ],
        ],
        'remark' => '感谢您的支持。',

    ],
    'member_agent' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '获得推广权益通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '恭喜您获得了推广权益！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "推广权益",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "获得",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，您于[时间]获得推广资格，赶紧去呼唤小伙伴吧。",
                "color" => "#000000",
            ],
        ],
        'remark' => '感谢您的支持。',

    ],
    'member_new_lower' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '新增下线粉丝通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '恭喜您新增了小伙伴！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "新增粉丝",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "新增",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，您于[时间]新增粉丝[下级昵称]。",
                "color" => "#000000",
            ],
        ],
        'remark' => '感谢您的支持。',

    ],
    'withdraw_submit' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '余额提现提交通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '感谢您的支持！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "余额提现",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "申请提交成功",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "您于[时间]申请提现[金额]元，其中手续费[手续费]元，请等待我们审核。",
                "color" => "#000000",
            ],
        ],
        'remark' => '如有疑问，请联系客服！',

    ],
    'withdraw_success' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '余额提现成功通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '感谢您的支持！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "余额提现",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "余额提现成功通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "您于[时间]申请余额提现[金额]元，其中手续费[手续费]元，已经成功到账。",
                "color" => "#000000",
            ],
        ],
        'remark' => '如有疑问，请联系客服！',

    ],
    'withdraw_fail' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '余额提现失败通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '感谢您的支持！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "提现",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "余额提现失败",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "您于[时间]申请提现[金额]元，其中手续费[手续费]元，未能成功提现。",
                "color" => "#000000",
            ],
        ],
        'remark' => '如有疑问，请联系客服！',

    ],
    'point_change' => [
        'template_id_short' => 'OPENTM207509450',
        'title' => '积分变动通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '亲爱的[昵称]，您的积分账户有新的变动，具体内容如下：',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "[时间]",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "[积分变动金额]",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "[积分变动类型]",
                "color" => "#000000",
            ],
            3 => [
                "keywords" => "keyword4",
                "value" => "[变动后积分数值]",
                "color" => "#000000",
            ],
        ],
        'remark' => '感谢您的支持！',
    ],
    'balance_change' => [
        'template_id_short' => 'OPENTM401833445',
        'title' => '余额变动通知！',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '尊敬的用户,你的账户发生变动',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "[时间]",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "[余额变动类型]",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "[余额变动金额]元",
                "color" => "#000000",
            ],
            3 => [
                "keywords" => "keyword4",
                "value" => "[变动后余额数值]",
                "color" => "#000000",
            ],
        ],
        'remark' => '详情请点击此消息进入会员中心-余额-明细进行查询!',

    ],
    'income_withdraw' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '提现申请提交通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '感谢您的支持！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "提现",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "申请提交成功",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "您于[时间]申请提现[金额]元，其中手续费[手续费]元，请等待我们审核。",
                "color" => "#000000",
            ],
        ],
        'remark' => '如有疑问，请联系客服！',

    ],
    'income_withdraw_check' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '提现审核通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '感谢您的支持！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "提现",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "申请审核通过",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "您于[时间]申请提现[金额]元，其中手续费[手续费]元，已通过我们的审核，我们将尽快将您提现的[审核通过金额]元通过[提现方式]支付给您。",
                "color" => "#000000",
            ],
        ],
        'remark' => '如有疑问，请联系客服！',

    ],
    'income_withdraw_pay' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '提现打款通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '感谢您的支持！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "提现",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "已打款",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "您于[时间]申请提现[金额]元，其中手续费[手续费]元，我们已经通过[提现方式]支付给您[金额]元。",
                "color" => "#000000",
            ],
        ],
        'remark' => '如有疑问，请联系客服！',

    ],
    'income_withdraw_arrival' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '提现到账通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '感谢您的支持！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "提现",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "已到账",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "您于[时间]申请提现[金额]元，我们已经通过[提现方式]支付给您[金额]元，已经成功到账！",
                "color" => "#000000",
            ],
        ],
        'remark' => '如有疑问，请联系客服！',

    ],
    'become_agent' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '成为分销商通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您成功成为推客！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "推客 ",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "成功成为推客",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，您于[时间]成为推客，可以享受推客的专属优惠及服务啦！",
                "color" => "#000000",
            ],
        ],
        'remark' => '欢迎您的加入！！',

    ],
    'commission_order' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '下级下单通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有会员下单啦！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "会员下单通知 ",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "下级会员成功提交订单",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，您的会员[下级昵称]于[时间]下单购买了商品。\r\n订单编号：[订单编号]\r\n[商品详情]",
                "color" => "#000000",
            ],
        ],
        'remark' => '请前往推广—推客中查看预计奖励金额，您的客户确认收货后您可以到会员中心—推广提现中进行提现。',

    ],
    'commission_order_finish' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '下级确认收货通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有会员确认收货了！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "会员确认收货通知 ",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "下级会员订单已确认收货",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "亲爱的[昵称]，您的会员[下级昵称]于[时间]确认收货了。\r\n订单编号：[订单编号]\r\n订单金额：[订单金额]元\r\n[商品详情]\r\n奖励金额：[佣金金额]元",
                "color" => "#000000",
            ],
        ],
        'remark' => '请前往推广—推客中查看预计奖励金额，您的客户确认收货后您可以到会员中心—推广提现中进行提现。',

    ],
    'commission_upgrade' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '分销商等级升级通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '分销商等级升级通知！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "分销等级",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "成功升级",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，恭喜您于[时间]由[旧等级]升级为[新等级]，一级分销比例由[旧一级分销比例]%升为[新一级分销比例]%，二级分销比例由[旧二级分销比例]%升为[新二级分销比例]%，三级分销比例由[旧三级分销比例]%升为[新三级分销比例]%",
                "color" => "#000000",
            ],
        ],
        'remark' => '继续加油哦！',

    ],
    'statement' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '分销佣金结算通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有一笔分享奖励结算了！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "分享奖励",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "结算通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，您有一笔奖励于[结算时间]结算了，结算金额为[佣金金额]元。",
                "color" => "#000000",
            ],
        ],
        'remark' => '继续加油哦！',

    ],
    'area_agent' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '成为区域代理通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '恭喜您成为区域代理！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "区域代理",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "成为独家区域代理通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "亲爱的[昵称]，恭喜您于[时间]成为我们的[省][市][区/县][街道/乡镇]代理。",
                "color" => "#000000",
            ],
        ],
        'remark' => '继续加油哦！',

    ],
    'area_dividend' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '分红结算通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '恭喜您获得一笔区域分红！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "区域分红",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "区域分红订单结算通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "亲爱的[昵称]，您于[时间]获得一笔[等级]区域分红，分红金额为[金额]元",
                "color" => "#000000",
            ],
        ],
        'remark' => '请前往商城首页—推广—区域分红查看奖励明细。',

    ],
    'team_agent' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '成为经销商通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '亲爱的[昵称]，恭喜您升级成为团队代理。',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "团队经销商",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "成为团队经销商通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "亲爱的[昵称]，恭喜您于[时间]成为我们的[经销商等级]，",
                "color" => "#000000",
            ],
        ],
        'remark' => '继续加油哦！',

    ],
    'team_agent_level' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '经销商升级通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '恭喜您升级啦！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "代理等级",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "团队代理升级通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "亲爱的代理[昵称]，恭喜您于[时间]由[旧等级]升级为[新等级]，经销分红由[旧等级提成比例]%升级为[新等级提成比例]%。",
                "color" => "#000000",
            ],
        ],
        'remark' => '请您再接再厉，再创辉煌！',
    ],
    'return' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '经销商奖励：返现通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有一笔新的经销商奖励！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "经销商奖励",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "返现",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，您于[时间]获得返现金额[返现金额]元，商城销售额为[商城销售额]，您的返现比例为[返现比例]%，所处经销商等级为[团队等级]，等级权益为[等级权益]，同等级团队人数为[同等级团队人数] ，总权益[总权益] ",
                "color" => "#000000",
            ],
        ],
        'remark' => '请继续加油！',
    ],
    'lower_team_agent' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '新增直属经销商通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '恭喜您新增一名团队代理！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "新增直属经销商",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "新增代理通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "亲爱的[昵称]，您于[时间]新增一名[直属经销商等级][直属经销商昵称]。",
                "color" => "#000000",
            ],
        ],
        'remark' => '帮助他您将获得更多惊喜哟！',
    ],
    'dividend_order' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '经销订单通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您的团队有会员下单了！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "会员下单",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "有团队会员下单",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]：您的团队成员[下单人昵称]于[时间]下单成功！\r\n订单金额：[订单金额]元\r\n经销结算金额：[经销结算金额]元\r\n[下单人昵称]确认收货后您将获得经销分红[提成金额]元。",
                "color" => "#000000",
            ],
        ],
        'remark' => '感谢您的支持！',
    ],
    'team_dividend' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '经销结算通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有一笔经销分红结算了！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "经销分红",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "经销分红结算通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，您于[时间]有一笔奖励结算了！订单金额：[订单金额]元，分红结算金额：[经销结算金额]，分红奖励金额：[提成金额]元",
                "color" => "#000000",
            ],
        ],
        'remark' => '请继续加油！',
    ],
    'activation_grant' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '激活码发放通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '恭喜您获得激活码！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "激活码",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "发放通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，您于[时间]获得激活码[激活码]，激活码数量为[数量],使用激活码能激活等级为[等级]的团队代理。",
                "color" => "#000000",
            ],
        ],
        'remark' => '请继续加油！',
    ],
    'activation_use' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '激活码使用通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有一个激活码被使用！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "激活码",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "激活码使用通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "亲爱的[昵称]，您的激活码[激活码]已于[时间]被使用，成功激活一名团队代理[等级]。",
                "color" => "#000000",
            ],
        ],
        'remark' => '请继续加油！',
    ],
    'flat_prize' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '获得平级奖通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您获得了一笔平级奖励！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "平级奖",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "获得",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "亲爱的[昵称]，您于[时间]获得一笔平级奖！订单金额：[订单金额]元，奖励结算金额：[提成结算金额]元，平级奖金额：[平级奖金额]元",
                "color" => "#000000",
            ],
        ],
        'remark' => '请继续加油！',
    ],
    'award_gratitude' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '获得感恩奖通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您获得了一笔感恩奖励！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "感恩奖",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "获得",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "亲爱的[昵称]，您于[时间]获得一笔感恩奖！订单金额：[订单金额]元，奖励结算金额：[提成结算金额]元，感恩奖金额：[感恩奖金额]元",
                "color" => "#000000",
            ],
        ],
        'remark' => '请继续加油！',
    ],
    'shareholder_dividend' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '股东分红通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有一笔股东分红发放了！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "股东分红",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "发放通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，您于[时间]有一笔[代理等级]股东分红发放了！分红金额：[分红金额]元。",
                "color" => "#000000",
            ],
        ],
        'remark' => '请继续加油！',
    ],
    'merchant_become_staff' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '成为招商员通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '亲爱的[昵称]，恭喜您成为招商员。',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "招商员",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "成为招商员通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "亲爱的[昵称]，恭喜您于[时间]成为[商城名称]的招商员！",
                "color" => "#000000",
            ],
        ],
        'remark' => '请继续加油！',
    ],
    'merchant_become_center' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '成为招商中心通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '亲爱的[昵称]，恭喜您成为招商中心。',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "招商中心",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "成为招商中心通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "亲爱的[昵称]，恭喜您于[时间]成为我们的的[招商中心等级]，可以享受[招商中心分红比例]%分红！",
                "color" => "#000000",
            ],
        ],
        'remark' => '请继续加油！',
    ],
    'merchant_center_upgrade' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '招商中心升级通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '恭喜您升级啦！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "招商中心等级",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "招商中心等级升级通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "亲爱的[昵称]，恭喜您于[时间]由[旧等级]升级为[新等级]，招商中心分红由[旧等级分红比例]%升级为[新等级分红比例]%。",
                "color" => "#000000",
            ],
        ],
        'remark' => '请继续加油！',
    ],
    'merchant_center_order_bonus' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '招商中心分红订单通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您的团队供应商有会员下单了！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "会员下单",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "招商中心分红订单通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，您的供应商：[供应商昵称]产生一笔新的订单了！订单金额：[订单金额]元，分红比例为：[分红比例]%，下级分红比例为：[下级分红比例]%，会员确认收货后您将获得分红[分红金额]元。",
                "color" => "#000000",
            ],
        ],
        'remark' => '感谢您的支持！',
    ],
    'merchant_staff_order_bonus' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '招商员分红订单通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您的团队供应商有会员下单了！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "会员下单",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "招商员分红订单通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，您的供应商：[供应商昵称]产生一笔新的订单了！订单金额：[订单金额]元，分红比例为：[分红比例]%，会员确认收货后您将获得分红[分红金额]元。",
                "color" => "#000000",
            ],
        ],
        'remark' => '感谢您的支持！',
    ],
    'merchant_center_order_bonus_settlement' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '招商中心分红结算通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有一笔招商中心分红结算了！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "招商中心分红",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "招商中心分红结算通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，您于[时间]有一笔招商中心分红结算了！供应商[供应商昵称]，订单金额：[订单金额]元，分红结算金额：[分红结算金额]元，分红奖励金额：[分红金额]元",
                "color" => "#000000",
            ],
        ],
        'remark' => '请继续加油！',
    ],
    'merchant_staff_order_bonus_settlement' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '招商员分红结算通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有一笔招商员分红结算了！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "招商员分红",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "招商员分红结算通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，您于[时间]有一笔招商员分红结算了！供应商[供应商昵称]，订单金额：[订单金额]元，分红结算金额：[分红结算金额]元，分红奖励金额：[分红金额]元",
                "color" => "#000000",
            ],
        ],
        'remark' => '请继续加油！',
    ],
    'micro_become_micro' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '成为微店通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '成为店主通知！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "微店",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "成为微店通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，您于[时间]成为[商城名称]的[店主等级]，可以享受店主分红比例:[分红比例]%！",
                "color" => "#000000",
            ],
        ],
        'remark' => '欢迎您的加入！',
    ],
    'micro_micro_upgrade' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '微店升级通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '店主等级升级通知！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "店主等级",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "升级通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，恭喜您于[时间]升级为[店主等级]。享受新分红比例[分红比例]%！",
                "color" => "#000000",
            ],
        ],
        'remark' => '请继续加油！',
    ],
    'micro_order_bonus' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '分红订单通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有一笔新的微店订单了！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "订单通知",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "微店分红订单通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，您有一笔新的分红订单，会员确认收货后您将获得店主分红[分红金额]元。",
                "color" => "#000000",
            ],
        ],
        'remark' => '感谢您的支持！',
    ],
    'micro_lower_bonus' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '下级微店分红通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有一笔新的下级微店分红！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "分红",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "下级微店分红通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，您有一笔新的分红订单，会员在[下级昵称]微店购买，确认收货后您将获得店主分红[分红金额]元。",
                "color" => "#000000",
            ],
        ],
        'remark' => '感谢您的支持！',
    ],
    'micro_bonus_settlement' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '微店分红结算通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有一笔新的微店结算分红！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "微店分红",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "微店分红结算通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，尊敬的[昵称]，您于[时间]有一笔分红结算了！分红金额为[分红金额]元",
                "color" => "#000000",
            ],
        ],
        'remark' => '请继续加油！',
    ],
    'micro_agent_bonus_settlement' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '上级微店分红结算通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有一笔新的上级微店结算分红！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "微店分红",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "上级微店分红结算通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，尊敬的[昵称]，您于[时间]有一笔上级微店结算了！分红金额为[分红金额]元。",
                "color" => "#000000",
            ],
        ],
        'remark' => '请继续加油！',
    ],
    'micro_agent_gold' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '上级店主金币奖励通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有一笔新的上级微店金币奖励！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "微店分红",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "上级店主金币奖励通知",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，尊敬的[昵称]，您于[时间]有一笔上级微店结算了！分红金额为[分红金额]元。",
                "color" => "#000000",
            ],
        ],
        'remark' => '请继续加油！',
    ],
    'supplier_order_create' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '供应商订单下单通知(通知供应商)',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有一笔新订单下单啦！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "供应商订单",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "订单下单通知！",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "订单编号：[订单号]\r\n商品名称：[商品详情（含规格）]\r\n下单时间：[下单时间]\r\n订单金额：[订单金额]元（含运费[运费]）",
                "color" => "#000000",
            ],
        ],
        'remark' => '请您及时做好跟进！',
    ],
    'supplier_order_pay' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '供应商订单支付通知(通知供应商)',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有一笔订单已支付！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "供应商订单",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "订单支付通知！",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "订单编号：[订单号]\r\n商品名称：[商品详情（含规格）]\r\n订单金额：[订单金额]元（含运费[运费]）\r\n支付方式：[支付方式]\r\n支付时间：[支付时间]",
                "color" => "#000000",
            ],
        ],
        'remark' => '订单已支付，请及时发货哦！',
    ],
    'supplier_order_send' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '供应商订单发货通知(通知供应商)',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有一笔订单已发货！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "供应商订单",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "订单发货通知！",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "商城名称[商城名称]\r\n订单编号：[订单号]\r\n商品名称：[商品详情（含规格）]\r\n发货时间：[发货时间]\r\n快递公司：[快递公司]\r\n快递单号：[快递单号]",
                "color" => "#000000",
            ],
        ],
        'remark' => '请您及时做好跟进！',
    ],
    'supplier_order_finish' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '供应商订单完成通知(通知供应商)',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有一笔订单已确认收货！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "供应商订单",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "订单确认收货通知！",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "商城名称[商城名称]\r\n订单号：[订单号]\r\n商品名称：[商品详情（含规格）]\r\n确认收货时间：[确认收货时间]",
                "color" => "#000000",
            ],
        ],
        'remark' => '请您及时做好跟进！',
    ],
    'supplier_withdraw_apply' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '提现通知(通知供应商)',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您的余额提现申请已提交成功！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "供应商提现申请",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "申请提交成功！",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]您于[申请时间]申请的[提现金额]元，提现单号为[提现单号]",
                "color" => "#000000",
            ],
        ],
        'remark' => '这边将尽快为您审核！',
    ],
    'supplier_withdraw_pass' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '审核通过通知(通知供应商)',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您的提现申请已审核通过！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "供应商提现申请",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "审核通过！",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]您申请的[提现金额]元，提现单号为[提现单号]，已于[审核时间]审核通过！",
                "color" => "#000000",
            ],
        ],
        'remark' => '这边将尽快为您打款！',
    ],
    'supplier_withdraw_reject' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '审核驳回通知(通知供应商)',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您的提现申请已被驳回！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "供应商提现申请",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "审核驳回！",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]您申请的[提现金额]元审核不通过，已于[驳回时间]被驳回！",
                "color" => "#000000",
            ],
        ],
        'remark' => '请再次提交申请或联系客服！',
    ],
    'supplier_withdraw_play' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '提现打款通知(通知供应商)',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您的提现申请已成功打款！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "供应商提现申请",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "成功打款！",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]您申请的[提现金额]元，已于[打款时间]打款成功！",
                "color" => "#000000",
            ],
        ],
        'remark' => '请注意查收！',
    ],
    'supplier_apply_reject' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '供应商申请驳回通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您的供应商申请已驳回！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "供应商申请",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "审核驳回！",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "亲爱的[昵称]，您的供应商申请已于[时间]被驳回！",
                "color" => "#000000",
            ],
        ],
        'remark' => '您可再次申请或者联系客服哦！',
    ],
    'supplier_apply_pass' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '供应商申请通过通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '恭喜您的供应商申请审核已通过！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "供应商申请",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "审核通过！",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "亲爱的[昵称]，您的供应商申请已于[时间]审核通过！",
                "color" => "#000000",
            ],
        ],
        'remark' => '欢迎您的加入！',
    ],
    'store_pay' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '订单支付通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有一笔新的订单！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "门店-收银台订单",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "订单支付通知！",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "订单编号：[订单编号]\r\n订单金额：[订单金额]元\r\n付款时间：[付款时间]",
                "color" => "#000000",
            ],
        ],
        'remark' => '请您及时做好跟进！',
    ],
    'become_store' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '审核通过通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '恭喜您的门店申请审核已通过！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "门店申请",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "审核通过！",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "亲爱的[昵称]，您的门店申请已于[时间]审核通过！",
                "color" => "#000000",
            ],
        ],
        'remark' => '欢迎您的加入！',
    ],
    'reject_store' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '审核驳回通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您的门店申请已驳回！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "门店申请",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "审核驳回！",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "亲爱的[昵称]，您的门店申请已于[时间]被驳回！",
                "color" => "#000000",
            ],
        ],
        'remark' => '您可再次申请或者联系客服哦！',
    ],
    'change_temp_id' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '爱心值变动通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您账户发生了一笔变动，请注意查看！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "[变动值类型]",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "[业务类型]",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，您于[时间]发生一笔爱心值变动，变动类型为[变动值类型]，变动数值为[变动数量]，变动后爱心值余值为[当前剩余值]。",
                "color" => "#000000",
            ],
        ],
        'remark' => '感谢您的支持！',
    ],
    'activation_temp_id' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '爱心值激活通知',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您成功激活了爱心值！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "爱心值激活",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "激活成功",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "尊敬的[昵称]，您于[时间]激活了一笔爱心值，激活数值为[激活值]",
                "color" => "#000000",
            ],
        ],
        'remark' => '感谢您的支持！',
    ],
    'expire' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '优惠券过期提醒',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有一张优惠券即将过期，请及时使用！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "优惠券",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "即将过期",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "亲爱的[昵称]，您有一张优惠券即将过期，请及时使用哦！优惠券名称：[优惠券名称]，使用范围：[优惠券使用范围]，过期时间：[过期时间]",
                "color" => "#000000",
            ],
        ],
        'remark' => '请及时使用哦！',
    ],
    'coupon_notice' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '获得优惠券',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您获得一张优惠券，请及时查收！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "优惠券",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "获得",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "亲爱的[昵称]，您获得了一张优惠券：[优惠券名称]，优惠券使用条件：[优惠券使用条件],优惠券使用范围：[优惠券使用范围]，优惠方式：[优惠方式]，于[过期时间]过期。",
                "color" => "#000000",
            ],
        ],
        'remark' => '请及时使用哦！',
    ],
    'member_withdraw' => [
        'template_id_short' => 'OPENTM207574677',
        'title' => '会员提现提醒通知！',
        'first_color' => '#000000',
        'remark_color' => '#000000',
        'first' => '您有新一笔的待处理会员收入提现！',
        'data' => [
            0 => [
                "keywords" => "keyword1",
                "value" => "会员提现",
                "color" => "#000000",
            ],
            1 => [
                "keywords" => "keyword2",
                "value" => "提现",
                "color" => "#000000",
            ],
            2 => [
                "keywords" => "keyword3",
                "value" => "[粉丝昵称]于[申请时间]申请了一笔提现\r\n提现金额：[提现金额]\r\n提现类型：[提现类型]\r\n提现方式：[提现方式]",
                "color" => "#000000",
            ],
        ],
        'remark' => '请尽快处理这笔会员提现记录！！',
    ],

];