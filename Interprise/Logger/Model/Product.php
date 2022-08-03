<?php
     
    namespace Interprise\Logger\Model;
 
class Product
{
    public function afterGetName(\Magento\Catalog\Model\Product $subject, $result)
    {
        return "test plugin ".$result; // Adding Apple in product name
    }
}
