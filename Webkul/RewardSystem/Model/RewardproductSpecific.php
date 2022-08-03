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

use Webkul\RewardSystem\Api\Data\RewardproductSpecificInterface;
use Magento\Framework\DataObject\IdentityInterface;
use \Magento\Framework\Model\AbstractModel;

class RewardproductSpecific extends AbstractModel implements RewardproductSpecificInterface, IdentityInterface
{
    const CACHE_TAG = 'rewardsystem_rewardproductspecific';
    /**
     * @var string
     */
    protected $_cacheTag = 'rewardsystem_rewardproductspecific';
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'rewardsystem_rewardproductspecific';
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Webkul\RewardSystem\Model\ResourceModel\RewardproductSpecific::class);
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

    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    public function getPoints()
    {
        return $this->getData(self::POINTS);
    }

    public function setPoints($point)
    {
        return $this->setData(self::POINTS, $point);
    }

    public function getStartTime()
    {
        return $this->getData(self::START_TIME);
    }

    public function setStartTime($startTime)
    {
        return $this->setData(self::START_TIME, $startTime);
    }

    public function getEndTime()
    {
        return $this->getData(self::END_TIME);
    }

    public function setEndTime($endTime)
    {
        return $this->setData(self::END_TIME, $endTime);
    }

    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }
}
