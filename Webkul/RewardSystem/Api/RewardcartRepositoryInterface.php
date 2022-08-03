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
namespace Webkul\RewardSystem\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface RewardcartRepositoryInterface
{
    public function save(\Webkul\RewardSystem\Api\Data\RewardcartInterface $items);

    public function getById($id);

    public function delete(\Webkul\RewardSystem\Api\Data\RewardcartInterface $item);

    public function deleteById($id);
}
