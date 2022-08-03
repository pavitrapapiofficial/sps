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

namespace Webkul\RewardSystem\Api\Data;

interface RewardattributeInterface
{
    const ENTITY_ID      = 'entity_id';
    const POINTS         = 'points';
    const ATTRIBUTE_CODE = 'attribute_code';
    const OPTION_ID      = 'option_id';
    const OPTION_LABEL   = 'option_label';
    const CREATED_AT     = 'created_at';
    const STATUS         = 'status';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getEntityId();

    /**
     * Get Points
     *
     * @return int|null
     */
    public function getPoints();

    /**
     * Get AttributeCode
     *
     * @return string|null
     */
    public function getAttributeCode();

    /**
     * Get OptionId
     *
     * @return int|null
     */
    public function getOptionId();

    /**
     * Get OptionLabel
     *
     * @return string|null
     */
    public function getOptionLabel();

    /**
     * Get CreatedAt
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Get Status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Set ID
     *
     * @return int|null
     */
    public function setEntityId($id);

    /**
     * Set Points
     *
     * @return int|null
     */
    public function setPoints($points);

    /**
     * Set AttributeCode
     *
     * @return string|null
     */
    public function setAttributeCode($attributeCode);

    /**
     * Set OptionId
     *
     * @return int|null
     */
    public function setOptionId($optionId);

    /**
     * Set OptionLabel
     *
     * @return string|null
     */
    public function setOptionLabel($optionLabel);

    /**
     * Set CreatedAt
     *
     * @return string|null
     */
    public function setCreatedAt($createdAt);

    /**
     * Set Status
     *
     * @return int|null
     */
    public function setStatus($status);
}
