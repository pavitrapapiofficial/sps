<?php

namespace PurpleCommerce\StoreLocator\Block\Adminhtml\Store\NewAction;

use Magento\Backend\Block\Widget\Context;

/**
 * Class PurpleCommerce\StoreLocator\Block\Adminhtml\Store\NewAction\GenericButton
 */
class GenericButton
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
