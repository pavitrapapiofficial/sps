<?php
/**
 * Category data interface
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Webkul\RewardSystem\Api\Data;

/**
 * @api
 */
interface CategoryInterface
{
    /**
     * Primary Key
     */
    const ENTITY_ID = 'entity_id';

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);
}
