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

namespace Webkul\RewardSystem\Model;

use Webkul\RewardSystem\Api\Data\RewardrecordInterface;
use Magento\Framework\DataObject\IdentityInterface;
use \Magento\Framework\Model\AbstractModel;

class Rewardrecord extends AbstractModel implements RewardrecordInterface, IdentityInterface
{
    const CACHE_TAG = 'rewardsystem_rewardrecord';
    /**
     * @var string
     */
    protected $_cacheTag = 'rewardsystem_rewardrecord';
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'rewardsystem_rewardrecord';
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Webkul\RewardSystem\Model\ResourceModel\Rewardrecord::class);
    }
    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getEntityId()];
    }
    /**
     * Get ID
     *
     * @return int|null
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    public function setEntityId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    public function getTotalRewardPoint()
    {
        return $this->getData(self::TOTAL_REWARD_POINT);
    }

    public function setTotalRewardPoint($point)
    {
        return $this->setData(self::TOTAL_REWARD_POINT, $point);
    }

    public function getRemainingRewardPoint()
    {
        return $this->getData(self::REMAINING_REWARD_POINT);
    }

    public function setRemainingRewardPoint($point)
    {
        return $this->setData(self::REMAINING_REWARD_POINT, $point);
    }

    public function getUsedRewardPoint()
    {
        return $this->getData(self::USED_REWARD_POINT);
    }

    public function setUsedRewardPoint($point)
    {
        return $this->setData(self::USED_REWARD_POINT, $point);
    }

    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    public function setUpdatedAt($date)
    {
        return $this->setData(self::UPDATED_AT, $date);
    }
}
