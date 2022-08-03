<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_RewardSystem
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

namespace Webkul\RewardSystem\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderChangeStateAfterObserver implements ObserverInterface
{
    /**
     * @var \Webkul\Auction\Helper\Data
     */
    protected $_rewardSystemHelper;

    /**
     * @var \Webkul\RewardSystem\Model\RewarddetailFactory
     */
    protected $_rewardDetailFactory;

    /**
     * @param \Webkul\Auction\Helper\Data                     $rewardSystemHelper
     * @param \Webkul\RewardSystem\Model\RewarddetailFactory  $rewardDetailFactory
     */

    public function __construct(
        \Webkul\RewardSystem\Helper\Data $rewardSystemHelper,
        \Webkul\RewardSystem\Model\RewarddetailFactory $rewardDetailFactory
    ) {
        $this->_rewardSystemHelper = $rewardSystemHelper;
        $this->_rewardDetailFactory = $rewardDetailFactory;
    }

    /**
     * Invoice save after
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $helper = $this->_rewardSystemHelper;
        $order = $observer->getEvent()->getOrder();
        if ($order instanceof \Magento\Framework\Model\AbstractModel
            && $helper->getRewardApprovedOn() == 'order_state'
            && $order->getState() == "complete"
            ) {
            $incrementId = $order->getIncrementId();
            $rewardPoint = 0;
            $rewardId = 0;
            $rewardModel = $this->_rewardDetailFactory->create()->getCollection()
                         ->addFieldToFilter('order_id', $order->getId())
                         ->addFieldToFilter('customer_id', $order->getCustomerId())
                         ->addFieldToFilter('status', 0);
            foreach ($rewardModel as $rewardData) {
                $rewardPoint = $rewardData->getRewardPoint();
                $rewardId = $rewardData->getId();
            }
            if ($rewardPoint) {
                $transactionNote = __('Order id : %1 credited amount', $incrementId);
                $rewardData = [
                'customer_id' => $order->getCustomerId(),
                'points' => $rewardPoint,
                'type' => 'credit',
                'review_id' => 0,
                'order_id' => $order->getId(),
                'status' => 1,
                'note' => $transactionNote,
                'reward_id' => $rewardId
                ];
                $msg = __(
                    'You got %1 reward points on order #%2',
                    $rewardPoint,
                    $incrementId
                );
                $adminMsg = __(
                    ' have placed an order on your site, and got %1 reward points',
                    $rewardPoint
                );
                $helper->updateRewardRecordData($msg, $adminMsg, $rewardData);
                $data = ['status' => 1];
                $rewardDetailModel = $this->_rewardDetailFactory
                   ->create()
                   ->load($rewardId)
                   ->addData($data);
                $rewardDetailModel->setId($rewardId)->save();
            }
        } elseif ($order instanceof \Magento\Framework\Model\AbstractModel && $order->getState() == "canceled") {
            $incrementId = $order->getIncrementId();
            $rewardPoint = 0;
            $rewardModel = $this->_rewardDetailFactory->create()->getCollection()
                         ->addFieldToFilter('order_id', $order->getId())
                         ->addFieldToFilter('action', ['eq' =>'debit'])
                         ->addFieldToFilter('customer_id', $order->getCustomerId())
                         ->addFieldToFilter('status', 1)
                         ->addFieldToFilter('is_revert', 0)
                         ->setPageSize(1);
            foreach ($rewardModel as $rewardsData) {
                 $rewardPoint = $rewardsData->getRewardPoint();
                if ($rewardPoint) {
                    $transactionNote = __('Order id : %1 Credited amount on order item cancel', $incrementId);
                    $rewardData = [
                    'customer_id' => $order->getCustomerId(),
                    'points' => $rewardPoint,
                    'type' => 'credit',
                    'review_id' => 0,
                    'is_revert' => 1,
                    'order_id' => $order->getId(),
                    'status' => 1,
                    'note' => $transactionNote
                    ];
                    $msg = __(
                        '%1 feather points have been added back to your account against cancelled order #%2',
                        $rewardPoint,
                        $incrementId
                    );
                    $adminMsg = __(
                        ' %1 feather points have been added back to your account against cancelled order #%2',
                        $rewardPoint,
                        $incrementId
                    );
                    $rewardsData->setIsRevert(1)->save();
                    $helper->setDataFromAdmin($msg, $adminMsg, $rewardData);
                }
            }
        }
    }
}
