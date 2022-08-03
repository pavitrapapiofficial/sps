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

namespace Webkul\RewardSystem\Block;

use Webkul\RewardSystem\Model\ResourceModel\Rewarddetail;
use Webkul\RewardSystem\Model\ResourceModel\Rewardrecord;
use Magento\Sales\Model\Order;
use Magento\Framework\Json\Helper\Data;

class RewardPoints extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Webkul\RewardSystem\Model\ResourceModel\RewardDetail
     */
    protected $_rewardDetailModel;
    /**
     * @var _rewardDetailCollection
     */
    protected $_rewardDetailCollection;
    /**
     * @var Webkul\RewardSystem\Model\ResourceModel\Rewardrecord
     */
    protected $_rewardRecordModel;
    /**
     * @var Order
     */
    protected $_order;
    /**
     * @var Webkul\RewardSystem\Helper\Data
     */
    protected $_rewardHelper;
    /**
     * @var Magento\Framework\Pricing\Helper\Data
     */
    protected $_pricingHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Rewardrecord\CollectionFactory                   $rewardrecordModel
     * @param RewardDetail\CollectionFactory                   $rewardDetailModel
     * @param Order                                            $order
     * @param \Webkul\RewardSystem\Helper\Data                 $rewardHelper
     * @param \Magento\Framework\Pricing\Helper\Data           $pricingHelper
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Rewardrecord\CollectionFactory $rewardRecord,
        Rewarddetail\CollectionFactory $rewardDetail,
        Order $order,
        \Webkul\RewardSystem\Helper\Data $rewardHelper,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        Data $jsonData,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_rewardRecordModel = $rewardRecord;
        $this->_rewardDetailModel = $rewardDetail;
        $this->_order = $order;
        $this->_rewardHelper = $rewardHelper;
        $this->_pricingHelper = $pricingHelper;
        $this->jsonData = $jsonData;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getRewardDetailCollection()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'rewardsystem.rewarddetail.pager'
            )
            ->setCollection(
                $this->getRewardDetailCollection()
            );
            $this->setChild('pager', $pager);
            $this->getRewardDetailCollection()->load();
        }

        return $this;
    }
    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    // get remaining total of a customer

    public function getRemainingRewardPoints($customerId)
    {
        $remainingPoints = 0;
        $rewardRecordCollection = $this->_rewardRecordModel->create()
            ->addFieldToFilter('customer_id', ['eq' => $customerId]);
        if (count($rewardRecordCollection)) {
            foreach ($rewardRecordCollection as $record) {
                $remainingPoints = $record->getRemainingRewardPoint();
            }
        }
        return $remainingPoints;
    }

    // get reward detail collection of a customer

    public function getRewardDetailCollection()
    {
        if (!$this->_rewardDetailCollection) {
            $customerId = $this->_rewardHelper
                ->getCustomerId();
            $rewardDetailCollection = $this->_rewardDetailModel->create()
                ->addFieldToFilter('customer_id', $customerId)
                ->setOrder('transaction_at', 'DESC');
            $this->_rewardDetailCollection = $rewardDetailCollection;
        }

        return $this->_rewardDetailCollection;
    }
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * Get Helper Class
     */
    public function getHelperClass()
    {
        return $this->_rewardHelper;
    }

    /**
     * Get JSON helper
     */
    public function getJsonHelper()
    {
        return $this->jsonData;
    }
}
