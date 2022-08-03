<?php
/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_RewardSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\RewardSystem\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderCreditmemoSaveAfterObserver implements ObserverInterface
{
    /**
     * @var \Webkul\RewardSystem\Helper\Data
     */
    protected $rewardSystemHelper;

    /**
     * @var \Webkul\RewardSystem\Model\RewarddetailFactory
     */
    protected $rewardDetailFactory;

    private $logger;

    /**
     * @param \Webkul\RewardSystem\Helper\Data                $rewardSystemHelper
     * @param \Webkul\RewardSystem\Model\RewarddetailFactory  $rewardDetailFactory
     */

    public function __construct(
        \Webkul\RewardSystem\Helper\Data $rewardSystemHelper,
        \Webkul\RewardSystem\Model\RewarddetailFactory $rewardDetailFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->rewardSystemHelper = $rewardSystemHelper;
        $this->rewardDetailFactory = $rewardDetailFactory;
        $this->logger = $logger;
    }

    /**
     * Invoice save after
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $helper = $this->rewardSystemHelper;
        $order = $observer->getEvent()->getCreditmemo()->getOrder();
        $incrementId = $order->getIncrementId();
        $rewardPoint = 0;

        $isRewardUsed = false;
        $rewardDetailsModel = $this->rewardDetailFactory->create()->getCollection()
                     ->addFieldToFilter('order_id', $order->getId())
                     ->addFieldToFilter('action', ['eq' =>'debit'])
                     ->addFieldToFilter('customer_id', $order->getCustomerId())
                     ->addFieldToFilter('status', 1)
                     ->addFieldToFilter('is_revert', 0);
        if ($rewardDetailsModel->getSize()) {
            $isRewardUsed = true;
        }
        $rewardModel = $this->rewardDetailFactory->create()->getCollection()
                     ->addFieldToFilter('order_id', $order->getId())
                     ->addFieldToFilter('action', ['eq' =>'credit'])
                     ->addFieldToFilter('customer_id', $order->getCustomerId())
                     ->addFieldToFilter('status', 1)
                     ->addFieldToFilter('is_revert', 0);
        foreach ($rewardModel as $rewardsData) {
            $rewardPoint = $rewardsData->getRewardPoint();
            if ($rewardPoint) {
                $transactionNote = __('Order id : %1 Debited amount on order item cancel', $incrementId);
                $rewardData = [
                  'customer_id' => $order->getCustomerId(),
                  'points' => $rewardPoint,
                  'type' => 'debit',
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
        if ($isRewardUsed) {
            $rewardPoint = 0;
            foreach ($rewardDetailsModel as $rewardsData) {
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
