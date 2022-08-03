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
namespace Webkul\RewardSystem\Model\Order\Creditmemo\Total;

class Rewardamount extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
          $order = $creditmemo->getOrder();
        $orderRewardamountTotal = $order->getRewardAmount();

        if ($orderRewardamountTotal && $order->getTotalRefunded() == 0) {
            $creditmemo->setGrandTotal($creditmemo->getGrandTotal()+$orderRewardamountTotal);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()+$orderRewardamountTotal);
        }
        return $this;
    }
}
