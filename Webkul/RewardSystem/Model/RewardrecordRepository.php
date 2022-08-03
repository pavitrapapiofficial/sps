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
use Webkul\RewardSystem\Api\RewardrecordRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Webkul\RewardSystem\Model\ResourceModel\Rewardrecord as RewardRecordResource;
use Webkul\RewardSystem\Model\ResourceModel\Rewardrecord\CollectionFactory as RewardRecordCollection;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class TimeslotConfigRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RewardrecordRepository implements RewardrecordRepositoryInterface
{
    /**
     * @var ResourceBlock
     */
    protected $resource;

    /**
     * @var BlockFactory
     */
    protected $rewardRecordFactory;

    /**
     * @var BlockCollectionFactory
     */
    protected $rewardRecordCollectionFactory;

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
        RewardRecordResource $resource,
        RewardrecordFactory $rewardRecordFactory,
        RewardRecordCollection $rewardRecordCollectionFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->rewardRecordFactory = $rewardRecordFactory;
        $this->rewardRecordCollectionFactory = $rewardRecordCollectionFactory;
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
    public function save(Data\RewardrecordInterface $rewardRecord)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $rewardRecord->setStoreId($storeId);
        try {
            $this->resource->save($rewardRecord);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $rewardRecord;
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
        $rewardRecord = $this->rewardRecordFactory->create();
        $this->resource->load($rewardRecord, $id);
        if (!$rewardRecord->getEntityId()) {
            throw new NoSuchEntityException(__('Reward record with id "%1" does not exist.', $id));
        }
        return $rewardRecord;
    }

    /**
     * Delete PreorderComplete
     *
     * @param \Webkul\MarketplacePreorder\Api\Data\PreorderCompleteInterface $timeSlot
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Data\RewardrecordInterface $rewardRecord)
    {
        try {
            $this->resource->delete($rewardRecord);
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
