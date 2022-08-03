<?php
/**
 * Provider: Meetanshi.
 * Package: Meetanshi VatExempt
 * Support: support@meetanshi.com (https://meetanshi.com/)
 */

namespace Meetanshi\VatExempt\Model;

use Meetanshi\VatExempt\Api\Data\GridInterface;
use Magento\Framework\Model\AbstractModel;

class Grid extends AbstractModel implements GridInterface
{
    const CACHE_TAG = 'vat_exempt_reason';
    protected $cacheTag = 'vat_exempt_reason';

    protected $eventPrefix = 'vat_exempt_reason';

    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    public function getReason()
    {
        return $this->getData(self::REASON);
    }

    public function setReason($reason)
    {
        return $this->setData(self::REASON, $reason);
    }

    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    protected function _construct()
    {
        $this->_init('Meetanshi\VatExempt\Model\ResourceModel\Grid');
    }
}
