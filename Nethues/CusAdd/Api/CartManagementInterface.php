<?php
/**
 * Nethues Software
 *
 * @category Nethues
 * @package Webkul_CusAdd
 * @author Nethues
 * @copyright Copyright (c) Nethues Software Private Limited (https://Nethues.com)
 * @license https://Nethues.com/
 */
namespace Nethues\CusAdd\Api;

interface CartManagementInterface{
    /**
	 * PUT for Post api
	 * @param string $cartId
	 * @param string $itemId
	 * @param string $price
	 * @return string
	 */
	public function updateCartItem($cartId,$itemId,$price);
}