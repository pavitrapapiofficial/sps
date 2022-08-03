<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Api\Data;

interface CategoryMapInterface
{
    /**
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case.
     */
    const ID = 'entity_id';
    const STORE_CATEGORY_ID = 'store_category_id';
    const GOOGLE_FEED_CATEGORY = 'google_feed_category';
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
     * Get StoreCategoryId.
     * @return string
     */
    public function getStoreCategoryId();

    /**
     * set StoreCategoryId.
     * @return $this
     */
    public function setStoreCategoryId($storeCategoryId);

    /**
     * Get GoogleFeedCategory.
     * @return string
     */
    public function getGoogleFeedCategory();

    /**
     * set GoogleFeedCategory.
     * @return $this
     */
    public function setGoogleFeedCategory($googleFeedCategory);

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
