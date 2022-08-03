<?php


namespace Interprise\Logger\Model;

use Interprise\Logger\Api\Data\ChangelogInterface;

class Changelog extends \Magento\Framework\Model\AbstractModel implements ChangelogInterface
{

    protected $_eventPrefix = 'interprise_logger_changelog';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Interprise\Logger\Model\ResourceModel\Changelog');
    }

    /**
     * Get changelog_id
     * @return string
     */
    public function getChangelogId()
    {
        return $this->getData(self::CHANGELOG_ID);
    }

    /**
     * Set changelog_id
     * @param string $changelogId
     * @return \Interprise\Logger\Api\Data\ChangelogInterface
     */
    public function setChangelogId($changelogId)
    {
        return $this->setData(self::CHANGELOG_ID, $changelogId);
    }

    /**
     * Get CreatedAt
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATEDAT);
    }

    /**
     * Set CreatedAt
     * @param string $createdAt
     * @return \Interprise\Logger\Api\Data\ChangelogInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATEDAT, $createdAt);
    }

    /**
     * Get ItemType
     * @return string
     */
    public function getItemType()
    {
        return $this->getData(self::ITEMTYPE);
    }

    /**
     * Set ItemType
     * @param string $itemType
     * @return \Interprise\Logger\Api\Data\ChangelogInterface
     */
    public function setItemType($itemType)
    {
        return $this->setData(self::ITEMTYPE, $itemType);
    }

    /**
     * Get ItemId
     * @return string
     */
    public function getItemId()
    {
        return $this->getData(self::ITEMID);
    }

    /**
     * Set ItemId
     * @param string $itemId
     * @return \Interprise\Logger\Api\Data\ChangelogInterface
     */
    public function setItemId($itemId)
    {
        return $this->setData(self::ITEMID, $itemId);
    }

    /**
     * Get Action
     * @return string
     */
    public function getAction()
    {
        return $this->getData(self::ACTION);
    }

    /**
     * Set Action
     * @param string $action
     * @return \Interprise\Logger\Api\Data\ChangelogInterface
     */
    public function setAction($action)
    {
        return $this->setData(self::ACTION, $action);
    }

    /**
     * Get PushedStatus
     * @return string
     */
    public function getPushedStatus()
    {
        return $this->getData(self::PUSHEDSTATUS);
    }

    /**
     * Set PushedStatus
     * @param string $pushedStatus
     * @return \Interprise\Logger\Api\Data\ChangelogInterface
     */
    public function setPushedStatus($pushedStatus)
    {
        return $this->setData(self::PUSHEDSTATUS, $pushedStatus);
    }

    /**
     * Get JsonValue
     * @return string
     */
    public function getJsonValue()
    {
        return $this->getData(self::JSONVALUE);
    }

    /**
     * Set JsonValue
     * @param string $jsonValue
     * @return \Interprise\Logger\Api\Data\ChangelogInterface
     */
    public function setJsonValue($jsonValue)
    {
        return $this->setData(self::JSONVALUE, $jsonValue);
    }
}
