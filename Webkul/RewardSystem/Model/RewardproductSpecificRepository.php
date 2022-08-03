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
use Webkul\RewardSystem\Api\RewardproductSpecificRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Webkul\RewardSystem\Model\ResourceModel\RewardproductSpecific as RewardProductResource;
use Webkul\RewardSystem\Model\ResourceModel\RewardproductSpecific\CollectionFactory as RewardproductSpecificCollection;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class RewardproductSpecificRepository is used for the reward product for specific time
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RewardproductSpecificRepository implements RewardproductSpecificRepositoryInterface
{
    /**
     * @var ResourceBlock
     */
    protected $resource;

    /**
     * @var BlockFactory
     */
    protected $rewardproductSpecificFactory;

    /**
     * @var BlockCollectionFactory
     */
    protected $rewardproductSpecificCollectionFactory;

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
     * @param ResourceTimeSlotConfig $resource
     * @param TimeSlotConfigFactory $timeSlotConfigFactory
     * @param Data\PreorderCompleteInterfaceFactory $dataTimeslotConfigFactory
     * @param TimeSlotConfigCollectionFactory $timeSlotCollectionFactory
     * @param Data\PreorderCompleteSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        RewardProductResource $resource,
        RewardproductSpecificFactory $rewardproductSpecificFactory,
        RewardproductSpecificCollection $rewardproductSpecificCollectionFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->rewardproductSpecificFactory = $rewardproductSpecificFactory;
        $this->rewardproductSpecificCollectionFactory = $rewardproductSpecificCollectionFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Save Preorder Complete data
     *
     * @param \Webkul\MarketplacePreorder\Api\Data\TimeslotConfigInterface $timeSlotConfig
     * @return PreorderComplete
     * @throws CouldNotSaveException
     */
    public function save(Data\RewardproductSpecificInterface $rewardProduct)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $rewardProduct->setStoreId($storeId);
        try {
            $this->resource->save($rewardProduct);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $rewardProduct;
    }

    /**
     * Load Preorder Complete data by given Block Identity
     *
     * @param string $id
     * @return PreorderComplete
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id)
    {
        $rewardProduct = $this->rewardproductSpecificFactory->create();
        $this->resource->load($rewardProduct, $id);
        if (!$rewardProduct->getEntityId()) {
            throw new NoSuchEntityException(__('Reward Product with id "%1" does not exist.', $id));
        }
        return $rewardProduct;
    }

    /**
     * Delete PreorderComplete
     *
     * @param \Webkul\MarketplacePreorder\Api\Data\PreorderCompleteInterface $timeSlot
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Data\RewardproductSpecificInterface $rewardProduct)
    {
        try {
            $this->resource->delete($rewardProduct);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete PreorderComplete by given Block Identity
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
