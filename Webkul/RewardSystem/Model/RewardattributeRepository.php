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
use Webkul\RewardSystem\Api\RewardattributeRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Webkul\RewardSystem\Model\ResourceModel\Rewardattribute as RewardattributeResource;
use Webkul\RewardSystem\Model\ResourceModel\Rewardattribute\CollectionFactory as RewardattributeCollection;
use Webkul\RewardSystem\Model\RewardattributeFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class RewardattributeRepository is used for reward attribute updation
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RewardattributeRepository implements RewardattributeRepositoryInterface
{
    /**
     * @var ResourceBlock
     */
    protected $resource;

    /**
     * @var BlockCollectionFactory
     */
    protected $rewardattributeCollectionFactory;

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
     * @param RewardattributeResource $resource
     * @param RewardattributeCollection $rewardattributeCollectionFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        RewardattributeResource $resource,
        RewardattributeCollection $rewardattributeCollectionFactory,
        RewardattributeFactory $rewardattributeFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->rewardattributeFactory = $rewardattributeFactory;
        $this->rewardattributeCollectionFactory = $rewardattributeCollectionFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Save Reward Attribute data
     *
     * @param \Webkul\RewardSystem\Api\Data\RewardattributeInterface $rewardAttribute
     * @return RewardAttribute
     * @throws CouldNotSaveException
     */
    public function save(Data\RewardattributeInterface $rewardAttribute)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $rewardAttribute->setStoreId($storeId);
        try {
            $this->resource->save($rewardAttribute);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $rewardAttribute;
    }

    /**
     * Load Reward Attribute data by given Block Identity
     *
     * @param string $id
     * @return RewardAttribute
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id)
    {
        $rewardProduct = $this->rewardattributeFactory->create();
        $this->resource->load($rewardAttribute, $id);
        if (!$rewardAttribute->getEntityId()) {
            throw new NoSuchEntityException(__('Reward Attribute with id "%1" does not exist.', $id));
        }
        return $rewardAttribute;
    }

    /**
     * Delete Reward Attribute
     *
     * @param \Webkul\RewardSystem\Api\Data\RewardattributeInterface $rewardAttribute
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Data\RewardattributeInterface $rewardAttribute)
    {
        try {
            $this->resource->delete($rewardAttribute);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete Reward Attribute by given Block Identity
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
