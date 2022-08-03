<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Api\Data;

interface AttributeMapInterface
{
    /**
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case.
     */
    const ID = 'entity_id';
    const ATTRIBUTE_CODE = 'attribute_code';
    const GOOGLE_FEED_FIELD = 'google_feed_field';
    const CREATED_AT = 'created_at';

    /**
     * Get ID.
     *
     * @return int|null
     */
    public function getId();

    /**
     * set ID.
     *
     * @return $this
     */
    public function setId($entityId);

    /**
     * Get AttributeCode.
     * @return string
     */
    public function getAttributeCode();

    /**
     * set AttributeCode.
     * @return $this
     */
    public function setAttributeCode($attributeCode);

    /**
     * Get GoogleFeedField.
     * @return string
     */
    public function getGoogleFeedField();

    /**
     * set GoogleFeedField.
     * @return $this
     */
    public function setGoogleFeedField($googleFeedField);

    /**
     * Get CreatedAt.
     * @return string
     */
    public function getCreatedAt();

    /**
     * set CreatedAt.
     * @return $this
     */
    public function setCreatedAt($createdAt);
}
