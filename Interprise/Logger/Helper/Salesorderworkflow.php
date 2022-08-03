<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Interprise\Logger\Helper;

use \Interprise\Logger\Helper\Data;
use \Magento\Sales\Model\Order;
use Magento\Framework\App\Helper\Context;
use Magento\Setup\Exception;

/**
 * Description of Salesorderworkflow
 *
 * @author geuser1
 */
class Salesorderworkflow extends Data
{
    
    public $order;
    public $objectManager;
    public $resource;
    public $connection;
    public $salesoder_helper;
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categorycollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory  $productCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryobj,
        \Interprise\Logger\Model\PricingcustomerFactory $pricingcustomer,
        \Interprise\Logger\Model\PricelistsFactory $pricelistsFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Model\Session $session,
        \Interprise\Logger\Model\CountryclassmappingFactory $classmapping,
        \Interprise\Logger\Model\StatementaccountFactory $statementaccountFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Interprise\Logger\Model\CustompaymentFactory $custompaymentFactory,
        \Interprise\Logger\Model\CustompaymentitemFactory $custompaymentitemFactory,
        \Interprise\Logger\Model\PaymentmethodFactory $paymentmethodfact,
        \Interprise\Logger\Model\ResourceModel\Installwizard\CollectionFactory $installwizardFactory,
        \Interprise\Logger\Model\ShippingstoreinterpriseFactory $shippingstoreinterpriseFactory,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        \Magento\Sales\Model\Order $_order,
        \Interprise\Logger\Helper\Salesorder $interprise_salesorder,
        \Interprise\Logger\Model\TransactionmasterFactory $transactionmasterFactory,
        \Interprise\Logger\Model\TransactiondetailFactory $transactiondetailFactory
    ) {
        $this->order = $_order;
        $this->salesoder_helper = $interprise_salesorder;
        $this->transationmaster = $transactionmasterFactory;
        $this->transationdetail = $transactiondetailFactory;
        parent::__construct(
            $context,
            $httpContext,
            $product,
            $curl,
            $datetime,
            $categorycollection,
            $productCollectionFactory,
            $categoryobj,
            $pricingcustomer,
            $pricelistsFactory,
            $productFactory,
            $session,
            $classmapping,
            $statementaccountFactory,
            $addressFactory,
            $custompaymentFactory,
            $custompaymentitemFactory,
            $paymentmethodfact,
            $installwizardFactory,
            $shippingstoreinterpriseFactory,
            $curlFactory
        );
    }
    public function salesorderworkflowSingle($datas)
    {
        ini_set("display_errors","1");
        $update_data=[];
        echo '<br/>'.$dataId = $datas['DataId'];
        echo '<br/>'.$exist_order = $this->salesoder_helper->checkDocumentCodeExistInTransaction($dataId);
        $update_data['ActivityTime'] = $this->getCurrentTime();
        $update_data['Request'] = 'To check exist table in transaction table';
        $update_data['Response'] = $exist_order;
        if (!$exist_order) {
            $update_data['Status'] = 'fail';
            $update_data['Remarks'] = "Can not update workflow until we get SO number:$dataId in our transaction table";
            return $update_data;
        }
        $api_responsc = $this->getCurlData('salesorder/' . $dataId.'/workflow');
        
        $update_data['Request'] = $api_responsc['request'];
        $update_data['Response'] = json_encode($api_responsc['results']['data']);
        if (!$api_responsc['api_error']) {
            $update_data['Status'] = 'fail';
            $update_data['Remarks'] = $api_responsc['results'];
            return $update_data;
        }
         $encoded_response = json_encode($api_responsc);
         $newStatus = $api_responsc['results']['data'][0]['attributes']['stage'];
         
        $time = $this->getCurrentTime();
        $transactionModel = $this->transationmaster->create()->load($exist_order);
        $transactionModel->setData('status', $newStatus);
        $transactionModel->setData('updated_at', $time);
        $transactionModel->save();
        $tdetailmodel = $this->transationdetail->create()->load($exist_order);
        $tdetailmodel->setData('json_detail2', $encoded_response);
        $tdetailmodel->save();
        $statuss = $api_responsc['results']['data']['0']['attributes']['stage'];
         //$state_status_array = $this->getStateStatus($statuss);
         $statuss = strtolower($statuss);
         
         echo '<br/>$statuss'.$statuss;
        switch ($statuss) {
            case 'na':
            case 'approve credit':
                $orderState = Order::STATE_PENDING_PAYMENT;
                $orderStatus = Order::STATE_PENDING_PAYMENT;
                break;
            case 'voided':
                $orderState = Order::STATE_CANCELED;
                $orderStatus = Order::STATE_CANCELED;
                break;
            case 'ready to post':
            case 'completed':
            case 'dispatched':
                echo '<br/>$orderState '.$orderState = Order::STATE_COMPLETE;
                echo '<br/>$orderStatus '.$orderStatus = Order::STATE_COMPLETE;
                break;
            case 'print pick note':
            case 'ready to invoice':
                $orderState = Order::STATE_PROCESSING;
                $orderStatus = Order::STATE_PROCESSING;
                break;
            default:
                $orderState = Order::STATE_NEW;
                $orderStatus = Order::STATE_NEW;
                break;
        }
        $orderId = $this->salesoder_helper->soOrderExist($dataId);
        if (!$orderId) {
            $error_message = 'Transaction table updated but note this order 
            is not exist in magento so order status is not updated';
            $update_data['Status'] = 'Success';
            $update_data['Remarks'] = $error_message;
            return $update_data;
        }
        //$orderId = 97;
        $order = $this->order->load($orderId);
        echo '<br/>$orderState'.$orderState = $orderState;
        try {
            $order->setState($orderState)->setStatus($orderStatus);
            if($statuss=='completed' || $statuss=='dispatched'){
                $history = $order->addStatusHistoryComment('Order was set to Complete by our automation tool.', true);
                $history->setIsCustomerNotified(true);
            }
            
            //$order->setIsCustomerNotified(true);
            $order->save();
            //echo '<br/>After Save';
            $update_data['Status'] = 'Success';
            $update_data['Remarks'] = 'Success';
            return $update_data;
        } catch (Exception $ex) {
            $err_message =  $ex->getMessage();
            $update_data['Status'] = 'fail';
            $update_data['Remarks'] = 'In method '.__METHOD__.' '.$err_message;
            return $update_data;
        }
    }
}
