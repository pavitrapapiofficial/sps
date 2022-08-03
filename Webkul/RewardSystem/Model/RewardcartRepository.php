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

use Webkul\RewardSystem\Api\Data;
use Webkul\RewardSystem\Api\RewardcartRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Webkul\RewardSystem\Model\ResourceModel\Rewardcart as RewardCartResource;
use Webkul\RewardSystem\Model\ResourceModel\Rewardcart\CollectionFactory as RewardCartCollection;
use Webkul\RewardSystem\Model\RewardcartFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class RewardcartRepository is used for the reward cart updation
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RewardcartRepository implements RewardcartRepositoryInterface
{
    /**
     * @var ResourceBlock
     */
    protected $resource;

    /**
     * @var BlockCollectionFactory
     */
    protected $rewardCartCollectionFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param RewardCartResource $resource
     * @param RewardCartCollection $rewardCartCollectionFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        RewardCartResource $resource,
        RewardCartCollection $rewardCartCollectionFactory,
        RewardcartFactory $rewardCartFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->rewardCartFactory = $rewardCartFactory;
        $this->rewardCartCollectionFactory = $rewardCartCollectionFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Save Reward Cart data
     *
     * @param \Webkul\RewardSystem\Api\Data\RewardcartInterface $rewardCart
     * @return Rewardcart
     * @throws CouldNotSaveException
     */
    public function save(Data\RewardcartInterface $rewardCart)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $rewardCart->setStoreId($storeId);
        try {
            $this->resource->save($rewardCart);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $rewardCart;
    }

    /**
     * Load Reward Cart data by given Block Identity
     *
     * @param string $id
     * @return RewaredCart
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id)
    {
        $rewardProduct = $this->rewardCartFactory->create();
        $this->resource->load($rewardCart, $id);
        if (!$rewardCart->getEntityId()) {
            throw new NoSuchEntityException(__('Reward Cart with id "%1" does not exist.', $id));
        }
        return $rewardCart;
    }

    /**
     * Delete Reward Cart
     *
     * @param \Webkul\RewardSystem\Api\Data\RewardcartInterface $rewardCart
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Data\RewardcartInterface $rewardCart)
    {
        try {
            $this->resource->delete($rewardCart);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete Reward Cart by given Block Identity
     *
     * @param string $id
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
