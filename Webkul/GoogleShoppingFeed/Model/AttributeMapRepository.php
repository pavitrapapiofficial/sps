<?php
/**
 * @category   Webkul
 * @package    Webkul_AmazonMagentoConnect
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Model;

use Webkul\GoogleShoppingFeed\Api\Data\AttributeMapInterface;
use Webkul\GoogleShoppingFeed\Model\ResourceModel\Accounts\Collection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class AttributeMapRepository implements \Webkul\GoogleShoppingFeed\Api\AttributeMapRepositoryInterface
{
    /**
     * resource model
     * @var \Webkul\GoogleShoppingFeed\Model\ResourceModel\Accounts
     */
    protected $resourceModel;

    public function __construct(
        AttributeMapFactory $attributeMapFactory,
        \Webkul\GoogleShoppingFeed\Model\ResourceModel\AttributeMap\CollectionFactory $collectionFactory,
        \Webkul\GoogleShoppingFeed\Model\ResourceModel\AttributeMap $resourceModel
    ) {
        $this->resourceModel = $resourceModel;
        $this->attributeMapFactory = $attributeMapFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * get collection by account id
     * @param  int $accountId
     * @return object
     */
    public function getCollectionById($accountId)
    {
        $accountDetails = $this->attributeMapFactory->create()->load($accountId);
        return $accountDetails;
    }
}
