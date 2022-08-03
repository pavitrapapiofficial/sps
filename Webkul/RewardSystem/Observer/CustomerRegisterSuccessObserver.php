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
use Webkul\RewardSystem\Helper\Data as RewardSystemHelper;
use Webkul\RewardSystem\Api\Data\RewardrecordInterfaceFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Webkul\RewardSystem\Api\RewardrecordRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;

class CustomerRegisterSuccessObserver implements ObserverInterface
{
    /**
     * @var RewardSystemHelper
     */
    protected $_rewardSystemHelper;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;
     /**
      * @var DataObjectHelper
      */
    protected $_dataObjectHelper;
    protected $_rewardRecordInterface;
    protected $_date;
    protected $_rewardRecordRepository;

    public function __construct(
        RewardSystemHelper $rewardSystemHelper,
        DataObjectHelper $dataObjectHelper,
        RewardrecordInterfaceFactory $rewardRecordInterface,
        ManagerInterface $messageManager,
        RewardrecordRepositoryInterface $rewardRecordRepository,
        DateTime $datetime
    ) {
        $this->_rewardSystemHelper = $rewardSystemHelper;
        $this->_dataObjectHelper = $dataObjectHelper;
        $this->_rewardRecordInterface = $rewardRecordInterface;
        $this->_messageManager = $messageManager;
        $this->_rewardRecordRepository = $rewardRecordRepository;
        $this->_date = $datetime;
    }
    /**
     * cart save after observer.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $helper = $this->_rewardSystemHelper;
        $enableRewardSystem = $helper->enableRewardSystem();
        if ($helper->getAllowRegistration() && $enableRewardSystem) {
            if ($helper->getRewardOnRegistration()) {
                $customer = $observer->getCustomer();
                $customerId = $customer->getId();
                $transactionNote = __("Feather point on registration");
                $rewardValue = $helper->getRewardValue();
                $rewardPoints = $helper->getRewardOnRegistration();
                $rewardData = [
                    'customer_id' => $customerId,
                    'points' => $rewardPoints,
                    'type' => 'credit',
                    'review_id' => 0,
                    'order_id' => 0,
                    'status' => 1,
                    'note' => $transactionNote
                ];
                $msg = __(
                    'You have registered an account at sandpipershoes.com and received %1 Feather Points',
                    $rewardPoints
                );
                $adminMsg = __(
                    ' has registered an account at sandpipershoes.com has received %1 feather points',
                    $rewardPoints
                );
                $helper->setDataFromAdmin(
                    $msg,
                    $adminMsg,
                    $rewardData
                );
                $this->_messageManager->addSuccess(__(
                    'You got %1 feather points on registration',
                    $rewardPoints
                ));
            }
        }
    }
}
