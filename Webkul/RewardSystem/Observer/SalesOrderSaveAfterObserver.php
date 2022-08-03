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
namespace Webkul\RewardSystem\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderSaveAfterObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $rewardAmount = $observer->getQuote()->getRewardAmount();
        $baseRewardAmount = $observer->getQuote()->getBaseRewardAmount();
        $order = $observer->getOrder();
        $order->setRewardAmount($rewardAmount);
        $order->setBaseRewardAmount($baseRewardAmount);
    }
}
