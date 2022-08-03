<?php
/**
 * @category   Webkul
 * @package    Webkul_AmazonMagentoConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Model;

use Webkul\GoogleShoppingFeed\Api\Data\CategoryMapInterface;
use Webkul\GoogleShoppingFeed\Model\ResourceModel\CategoryMap\Collection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class CategoryMapRepository implements \Webkul\GoogleShoppingFeed\Api\CategoryMapRepositoryInterface
{
    /**
     * resource model
     * @var \Webkul\GoogleShoppingFeed\Model\ResourceModel\Accounts
     */
    protected $resourceModel;

    public function __construct(
        AttributeMapFactory $attributeMapFactory,
        \Webkul\GoogleShoppingFeed\Model\ResourceModel\CategoryMap\CollectionFactory $collectionFactory,
        \Webkul\GoogleShoppingFeed\Model\ResourceModel\CategoryMap $resourceModel
    ) {
        $this->resourceModel = $resourceModel;
        $this->categoryMapFactory = $attributeMapFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * get collection by account id
     * @param  int $accountId
     * @return object
     */
    public function getCollectionById($accountId)
    {
        $accountDetails = $this->categoryMapFactory->create()->load($accountId);
        return $accountDetails;
    }
}
