<?php
namespace PurpleCommerce\DisplayPricerange\Plugin\Block\Product;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class ListProduct
{
    /**
     * @var \Magento\Catalog\Block\Product\ListProduct
     */
    protected $listProductBlock;

    /**
     * @var Configurable
     */
    protected $configurableProduct;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * ListProduct constructor.
     *
     * @param Configurable $configurableProduct
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     */
    public function __construct(
        Configurable $configurableProduct,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->configurableProduct = $configurableProduct;
        $this->pricingHelper = $pricingHelper;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Catalog\Block\Product\ListProduct $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function aroundGetProductPrice(
        \Magento\Catalog\Block\Product\ListProduct $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product
    ) {

        if (Configurable::TYPE_CODE !== $product->getTypeId()) {
            return $proceed($product);
        }

        $this->listProductBlock = $subject;
        $priceText = $this->getPriceRange($product);

        return $priceText;
    }

    /**
     * Get configurable product price range
     *
     * @param $product
     * @return string
     */
    public function getPriceRange($product)
    {
        $childProductPrice = [];
        $childProducts = $this->configurableProduct->getUsedProducts($product);
        foreach($childProducts as $child) {
            $price = number_format($child->getPrice(), 2, '.', '');
            $finalPrice = number_format($child->getFinalPrice(), 2, '.', '');
            if($price == $finalPrice) {
                $childProductPrice[] = $price;
            } else if($finalPrice < $price) {
                $childProductPrice[] = $finalPrice;
            }
        }

        $max = $this->pricingHelper->currencyByStore(max($childProductPrice));
        $min = $this->pricingHelper->currencyByStore(min($childProductPrice));
        $maxmin = $this->pricingHelper->currencyByStore(min($childProductPrice)).'-'.$this->pricingHelper->currencyByStore(max($childProductPrice));
        if($min==$max){
            return $this->getPriceRender($product, "$min", '');
        } else {
            return $this->getPriceRender($product, "<span class='now-from'>now from</span>$maxmin", '');
        }
    }

    /**
     * Price renderer
     *
     * @param $product
     * @param $price
     * @return mixed
     */
    protected function getPriceRender($product, $price, $text='')
    {
        return $this->listProductBlock->getLayout()->createBlock('Magento\Framework\View\Element\Template')
            ->setTemplate('PurpleCommerce_DisplayPricerange::product/price/range/price.phtml')
            ->setData('price_id', 'product-price-'.$product->getId())
            ->setData('display_label', $text)
            ->setData('product_id', $product->getId())
            ->setData('display_value', $price)->toHtml();
    }
}