<?php
namespace PurpleCommerce\Custom\Plugin;

class FinalPricePlugin
{
    public function beforeSetTemplate(\Magento\Catalog\Pricing\Render\FinalPriceBox $subject, $template)
    {
        ini_set("display_errors","1");
        //        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //        $enable=$objectManager->create('PurpleCommerce\Custom\Helper\Data')->chkIsModuleEnable();
        $enable = true;
        if ($enable) {
            //echo $template;
            //die;
            if ($template == 'Magento_ConfigurableProduct::product/price/final_price.phtml') {
                sleep(3);
                return ['PurpleCommerce_Custom::product/price/final_price.phtml'];
            } 
            else
            {
                return [$template];
            }
        } else {
            return[$template];
        }
    }
}