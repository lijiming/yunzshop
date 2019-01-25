<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/23
 * Time: 10:03 AM
 */

namespace app\frontend\modules\order;


use app\common\exceptions\AppException;
use Illuminate\Support\Collection;

trait PriceNodeTrait
{
    /**
     * @var Collection
     */
    public $priceCache;
    /**
     * @var Collection
     */
    private $priceNodes;

    /**
     * @return Collection
     */
    public function getPriceNodes()
    {
        if (!isset($this->priceNodes)) {
            $this->priceNodes = $this->_getPriceNodes();
        }
        return $this->priceNodes;
    }

    /**
     * 获取某个节点之后的价格
     * @param $key
     * @return mixed
     * @throws AppException
     */
    public function getPriceAfter($key)
    {
        if (!isset($this->priceCache[$key])) {
            // 找到对应的节点
            $priceNode = $this->getPriceNodes()->first(function (PriceNode $priceNode) use ($key) {
                return $priceNode->getKey() == $key;
            });
            if (!$priceNode) {
                throw new AppException("不存在的价格节点{$key}");
            }
            $this->priceCache[$key] = $priceNode->getPrice();
        }
        return $this->priceCache[$key];
    }

    /**
     * 获取某个节点之前的价格
     * @param $key
     * @return mixed
     * @throws AppException
     */
    public function getPriceBefore($key)
    {
        $nodeKey = '';

        foreach ($this->getPriceNodes() as $priceNode) {
            if ($priceNode->getKey() == $key) {
                break;
            }
            $nodeKey = $priceNode->getKey();
        }
        if (empty($nodeKey)) {
            throw new AppException("没有比{$key}更先计算的节点了");
        }
        return $this->getPriceAfter($nodeKey);
    }
}