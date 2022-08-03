<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_RewardSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\RewardSystem\Block\Sales\Order;

use Magento\Sales\Model\Order;

class Reward extends \Magento\Framework\View\Element\Template
{

    protected $_objectManager;

    /**
     * @var Order
     */
    protected $_order;

    public function getSource()
    {
        return $this->_source;
    }

    public function displayFullSummary()
    {
        return true;
    }
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();
        $title = 'Rewarded Amount';
        $store = $this->getStore();
        if ($this->_order->getRewardAmount()!=0) {
            $rewardamount = new \Magento\Framework\DataObject(
                [
                    'code' => 'rewardamount',
                    'strong' => false,
                    'value' => $this->_order->getRewardAmount(),
                    'label' => __($title),
                ]
            );
            $parent->addTotal($rewardamount, 'rewardamount');
        }
        return $this;
    }

    /**
     * Get order store object
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->_order->getStore();
    }
    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * @return array
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * @return array
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }
}
