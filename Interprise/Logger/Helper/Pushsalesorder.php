<?php


namespace Interprise\Logger\Helper;

use \Interprise\Logger\Helper\Data;
use Magento\Framework\App\Helper\Context;
use Magento\Setup\Exception;

/**
 * Description of Pushsalesorder
 *
 * @author shadab
 */
class Pushsalesorder extends Data
{
    public $order;
    public $_customer;
    public $_pushcustomer;
    public $_pushcustomeraddress;
    public $_product;
    public $warehousecodefulfillment;
    public $magentoCustomerId;
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
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Interprise\Logger\Helper\Pushcustomer $pushcustomer,
        \Interprise\Logger\Helper\Pushcustomeraddress $pushcustomeraddress,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Sales\Model\OrderFactory $ordermodel,
        \Interprise\Logger\Model\ChangelogFactory $changelogFactory,
        \Interprise\Logger\Model\CronActivityScheduleFactory $cronActivityScheduleFactory,
        \Interprise\Logger\Model\PaymentmethodFactory $paymentmethodFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Bundle\Model\Option $optionbun,
        \Magento\Framework\App\ResourceConnection $resourceCon
    ) {
        $this->countryFactory  = $countryFactory;
        $this->customerFactory  = $customerFactory;
        $this->order = $order;
        $this->ordermodel = $ordermodel;
        $this->_customer = $customer;
        $this->_pushcustomer = $pushcustomer;
        $this->_pushcustomeraddress = $pushcustomeraddress;
        $this->_changelogfact = $changelogFactory;
        $this->_product = $product;
        $this->_seriializer = $serializer;
        $this->_cronactfactory = $cronActivityScheduleFactory;
        $this->_paymentmethodfact = $paymentmethodFactory;
        $this->_bundleoptfact = $optionbun;
        $this->warehousecodefulfillment = "MAIN";
        $this->resource = $resourceCon;
        $this->connection = $this->resource->getConnection();
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
    public function pushsalesorderSingle($datas)
    {
        $data_id = $datas['DataId'];
        $soNumber = $this->checkSoNumber($data_id);

        if($soNumber){
          
          $update_data['ActivityTime'] = $this->getCurrentTime();
          $update_data['Request'] = '';
          $update_data['Status'] = 'Already Processed';
          $update_data['Remarks'] = 'Already Processed '.$soNumber;
          $update_data['Response'] = 'Already Processed '.$soNumber;
          
          return $update_data;
       }
       
        $encoded_json_to_post = $datas['JsonData'];
        $result = $this->postCurlData('salesorder', $encoded_json_to_post);
        $update_data['ActivityTime'] = $this->getCurrentTime();
        $update_data['Request'] = $result['request'];
        $update_data['Response'] = json_encode($result['result']);
        $resu = json_decode($result['result'], true);
        if (is_array($resu) && array_key_exists('errors', $resu)) {
            $error_result = $resu['errors'];
            if ($error_result[0]['status'] == 500
                && strpos(
                    $error_result[0]['title'],
                    'Failed to allocate stock on Sales'
                ) !== false) {
                $update_data['Status'] = 'Success';
                $update_data['Remarks'] = 'Success';
                $so_order = "SO" . filter_var($error_result[0]['title'], FILTER_SANITIZE_NUMBER_INT);
                $this->updateMagentoSo($data_id, $so_order);
            } elseif ($error_result[0]['status'] == 400
                && strpos(
                    $error_result[0]['title'],
                    'Invalid shipping method specified'
                ) !== false
            ) {
                $default_shippingmethodcode = $this->getConfig('setup/general/defaultshippingmethod');
                $decoded_json = json_decode($encoded_json_to_post, true);
                $msg_dec = $decoded_json['notes'];
                $msg_dec.="Site shipping method:";
                $msg_dec.=$decoded_json['shipAddress']['shippingMethodCode'];
                   $decoded_json['notes'] = $msg_dec;
                   $decoded_json['shipAddress']['shippingMethodCode'] =$default_shippingmethodcode;
                   $encoded_json = json_encode($decoded_json);
                   $result_retry = $this->postCurlData('salesorder', $encoded_json);
                   $resu_retry = json_decode($result_retry['result'], true);
                if (is_array($resu_retry) && array_key_exists('errors', $resu_retry)) {
                    $error_result_retry = $resu_retry['errors'];
                    if ($error_result_retry[0]['status'] == 500
                        && strpos(
                            $error_result_retry[0]['title'],
                            'Failed to allocate stock on Sales'
                        ) !== false) {
                        $update_data['status'] = 'Success';
                        $update_data['remarks'] = 'Success';
                         $so_order = "SO" . filter_var($error_result_retry[0]['title'], FILTER_SANITIZE_NUMBER_INT);
                        $this->updateMagentoSo($data_id, $so_order);
                    } else {
                        $update_data['Status'] = 'fail';
                        $update_data['Remarks'] = json_encode($result_retry['result']);
                    }
                } else {
                    $update_data['Status'] = 'Success';
                    $update_data['Remarks'] = 'Success';
                    $so_order = "SO" . filter_var($result_retry['result'], FILTER_SANITIZE_NUMBER_INT);
                    $this->updateMagentoSo($data_id, $so_order);
                }
            } else {
                $update_data['Status'] = 'fail';
                $update_data['Remarks'] = json_encode($result['result']);
            }
        } else {
            $update_data['Status'] = 'Success';
            $update_data['Remarks'] = 'Success';
            $so_order = "SO" . filter_var($result['result'], FILTER_SANITIZE_NUMBER_INT);
           
            $so_array = explode('-',$so_order);
            echo '<br/>'.$newso_order = $so_array[0].'-'.$so_array[1];
            echo '<br/>'.$data_id;
            echo '<pre>';
            print_r($update_data);
            
            $this->updateMagentoSo($data_id, $newso_order);
        }
        return $update_data;
    }

    public function checkSoNumber($orderid){
       // $query = "select so_number from sales_order where entity_id=$orderid";
       // $result = $this->resource->getConnection()->fetchRow($query);
       //  if(!empty($result)){
       //      print_r($result);
       //      return $result['so_number'];
       //  }
        $order_object = $this->order->load($orderid);
        if($order_object->getData('so_number')!=''){
            return $order_object->getData('so_number');
        }
        return false;
    }
    
    public function updateMagentoSo($orderid, $so_number)
    {
        
        //$ordermodel = $this->ordermodel->create()->load($orderid);
        //$ordermodel->setData('so_number', $so_number);
        //$ordermodel->save();
        //$this->connection->update('sales_order', "so_number = '".$so_number."'", ['entity_id = ?' => (int)$orderid]);
        $sql = "UPDATE sales_order SET `so_number` = '$so_number' WHERE `entity_id` = '$orderid'";
        $this->connection->query($sql);
    }

    public function updateCronActivitySchedule($data, $cron_log_id)
    {
        $get_result_data = $data;
        $table_activity_schedule = 'interprise_logger_cronactivityschedule';
        if (count($get_result_data) > 0) {
            
            foreach ($get_result_data->getData() as $ks => $_changeolog_detail) {
                
                $order_object = $this->order->load($_changeolog_detail['ItemId']);
                //print_r($order_object->getData());

                $itemid = $_changeolog_detail['ItemId'];
                $cold = $this->_changelogfact->create()->getCollection();
                $cold->addFieldToFilter('ItemId', ['eq' => $itemid]);
                $cold->addFieldToFilter('ItemType', ['eq' => 'order']);
                foreach ($cold as $coun) {
                    $coun->setData('PushedStatus', -1);
                }
                $cold->save();

                echo '<br/>'.$vatExemptCustomer = $order_object->getVatExemptCustomer();
                echo '<br/>'.$vatExemptReason = $order_object->getVatExemptReason();
                $isVatExempt = false;
                if($vatExemptCustomer!='' && $vatExemptReason!=''){
                    
                    $isVatExempt = true;
                }
                
                 $get_customer_id = $order_object->getCustomerId();
                 $this->magentoCustomerId = $get_customer_id;
                 $get_billingaddressobj=$order_object->getBillingaddress();
                 $billingaddress=$this->_pushcustomeraddress->createAddressFromObj($get_billingaddressobj);
                $json_data = '';
                $previousClassCode = '';    
                if (isset($get_customer_id) && $get_customer_id!='' || $get_customer_id!=0) {
                    $customer_objec  = $this->_customer->load($get_customer_id);
                    echo '<br/>'.$interprise_code_of_customer = $customer_objec->getInterpriseCustomerCode();
                    //die;
                    if (isset($interprise_code_of_customer) && $interprise_code_of_customer!='') {

                        $customerCodeFound = $this->checkCustomerCodeOnAPI($interprise_code_of_customer);
                        
                        if($customerCodeFound) {
                            //////// If customer code found on Interprise ////////////////////
                            $cust_info['Status'] = true;
                            $cust_info['entity_id'] = $interprise_code_of_customer;
                            ///////// Update customFields of customer ////////////////////////
                            if($isVatExempt){
                                $vatExpiryDate = $customer_objec->getData('vatexemptexpirydate_c');
                                $result_updation = $this->updateCustomer($get_customer_id, $interprise_code_of_customer, $vatExpiryDate);
                                 print_r($result_updation);
                                if ($result_updation['Status']=='Success') {
                                    $cust_info['Status'] = true;
                                } else {
                                    $cust_info['Status'] = false;
                                    $cust_info['error'] = $result_updation['Remarks'];
                                }
                            }
                        } else {
                            //////// else If customer code not found on Interprise ////////////
                            echo '<br/>'.$customerEmailAddress = $customer_objec->getEmail();
                            $customerFound = $this->checkCustomerByEmailIdOnAPI($customerEmailAddress);

                            if($customerFound){
                                //////// If customer found on Interprise by email ////////////
                                $cust_info['Status'] = true;
                                ///////////////Update customer code in Magento ////////////////

                                $custom = $this->customerFactory ->create();
                                $custom = $custom->load($get_customer_id);
                                $customerData = $custom->getDataModel();
                                $customerData->setCustomAttribute('interprise_customer_code', $customer_id);
                                $custom->updateData($customerData);
                                $custom->save();
                                ////////////// End code to update customer code ///////////////
                            } else {
                                //////// Else If customer not found on Interprise by email ////////
                                $result_creation = $this->_pushcustomer->pushcustomerSingle(
                                    ['DataId'=>$get_customer_id,'address'=>$billingaddress, 'isVatExempt'=>$isVatExempt, 'billing_address_obj'=>$get_billingaddressobj]
                                );
                                 print_r($result_creation);
                                if ($result_creation['Status']=='Success') {
                                    $cust_info['Status'] = true;
                                    $cust_info['entity_id'] = $result_creation['entity_id'];
                                } else {
                                    $cust_info['Status'] = false;
                                    $cust_info['error'] = $result_creation['Remarks'];
                                }

                            }
                        }
                        ///////////////// End update customFields ///////////////////////
                    } else {
                        
                        //////// If customer code not found in Magento ////////////
                        echo '<br/>'.$customerEmailAddress = $customer_objec->getEmail();
                        $customerFound = $this->checkCustomerByEmailIdOnAPI($customerEmailAddress);

                        if($customerFound){
                             //////// If customer found on Interprise by email ////////////
                            $cust_info['Status'] = true;
                            ///////////////Update customer code in Magento ////////////////

                            $custom = $this->customerFactory ->create();
                            $custom = $custom->load($get_customer_id);
                            $customerData = $custom->getDataModel();
                            $customerData->setCustomAttribute('interprise_customer_code', $customer_id);
                            $custom->updateData($customerData);
                            $custom->save();
                            ////////////// End code to update customer code ///////////////
                        } else{
                            //////// Else If customer not found on Interprise by email ////////
                            $result_creation = $this->_pushcustomer->pushcustomerSingle(
                            ['DataId'=>$get_customer_id,'address'=>$billingaddress, 'isVatExempt'=>$isVatExempt, 'billing_address_obj'=>$get_billingaddressobj]
                            );
                             print_r($result_creation);
                            if ($result_creation['Status']=='Success') {
                                $cust_info['Status'] = true;
                                $cust_info['entity_id'] = $result_creation['entity_id'];
                            } else {
                                $cust_info['Status'] = false;
                                $cust_info['error'] = $result_creation['Remarks'];
                            }

                        }

                        
                    }
                    if ($cust_info['Status']) {
                        
                        $json_data_array = $this->createJsonOrder($order_object, $cust_info['entity_id'], 'false');
                        if ($json_data_array['Status']) {
                            $update_data['Status'] = 'pending';
                            $update_data['Remarks'] = 'pending';
                            $json_data = json_encode($json_data_array['entity_id']);
                        } else {
                            $update_data['Status'] = 'fail';
                            $update_data['Remarks'] = json_encode($json_data_array['error']);
                        }
                    } else {
                        $update_data['Status'] = 'fail';
                        $update_data['Remarks'] = json_encode($cust_info['error']);
                    }
                } elseif ($get_customer_id==0 || $get_customer_id==null) {//condition for guest checkout
                   // die;
                    /////////////// Edited By Manisha to check customer email in Interprise //////////

                    echo '<br/>'.$guestEmailAddress = $get_billingaddressobj->getData('email');
                    $customerFound = $this->checkCustomerByEmailIdOnAPI($guestEmailAddress);

                    echo '<br/>$customerFound'.$customerFound;
                   
                    if($customerFound){
                        $interpise_customer_code = $customerFound;
                        $json_data_array = $this->createJsonOrder($order_object, $interpise_customer_code, true);

                        if ($json_data_array['Status']) {
                            $update_data['Status'] = 'pending';
                            $update_data['Remarks'] = 'pending';
                            $json_data = json_encode($json_data_array['entity_id']);
                        } else {
                            $update_data['Status'] = 'fail';
                            $update_data['Remarks'] = json_encode($json_data_array['error']);
                        }
                    } else {
                        ///////////////////// End code to check customer email in Interprise //////////////
                        $create_customer_for_guest_checkout = $this->createCustomerGuest($order_object, $isVatExempt);
                         $this->prf($create_customer_for_guest_checkout);
                         
                        if (!$create_customer_for_guest_checkout['Status']) {
                             $update_data['Status'] = 'fail';
                             $update_data['Remarks'] = json_encode($create_customer_for_guest_checkout['error']);
                        } else {
                            $interpise_customer_code = $create_customer_for_guest_checkout['entity_id'];
                            $json_data_array = $this->createJsonOrder($order_object, $interpise_customer_code, true);
                            if ($json_data_array['Status']) {
                                $update_data['Status'] = 'pending';
                                $update_data['Remarks'] = 'pending';
                                $json_data = json_encode($json_data_array['entity_id']);
                            } else {
                                $update_data['Status'] = 'fail';
                                $update_data['Remarks'] = json_encode($json_data_array['error']);
                            }
                        }
                    }
                } else {
                    $update_data['Status'] = 'fail';
                    $update_data['Remarks'] = json_encode($get_customer_id['error']);
                }
                $itemid = $_changeolog_detail['ItemId'];
                $cold = $this->_changelogfact->create()->getCollection();
//                                $cold = $this->_changelogfact->create();
//                                $cold->getCollection();
                $cold->addFieldToFilter('ItemId', ['eq' => $itemid]);
                $cold->addFieldToFilter('ItemType', ['eq' => 'order']);
                foreach ($cold as $coun) {
                    $coun->setData('PushedStatus', 1);
                }
                $cold->save();

                $moduels = $this->_cronactfactory->create();
                $moduels->setData('CronLogId', $cron_log_id);
                $moduels->setData('CronMasterId', 4);
                $moduels->setData('ActionType', 'POST');
                $moduels->setData('ActivityTime', $this->getCurrentTime());
                $moduels->setData('DataId', $_changeolog_detail['ItemId']);
                $moduels->setData('Status', $update_data['Status']);
                $moduels->setData('Remarks', $update_data['Remarks']);
                $moduels->setData('JsonData', $json_data);
                $moduels->save();
            }
        }
    }

    public function checkCustomerByEmailIdOnAPI($guestCustomeremail = ''){
        if($guestCustomeremail==''){
            return false;
        } else {
            $api_responsc = $this->getCurlData('customer?emailaddress=' . $guestCustomeremail);
            if (isset($api_responsc['results'])) {
                if (isset($api_responsc['results']['data'])) {
                    $data = $api_responsc['results']['data'][0]['attributes'];
                    return $data['customerCode'];
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    public function checkCustomerCodeOnAPI($customerCode = ''){
        if($customerCode==''){
            return false;
        } else {
            $api_responsc = $this->getCurlData('customer?customercode=' . $customerCode);
            if (isset($api_responsc['results'])) {
                if (isset($api_responsc['results']['data'])) {
                    $data = $api_responsc['results']['data']['attributes'];
                    return $data['customerCode'];
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    public function updateCustomer($customer_id, $customerCode, $vatExpiryDate){
        $customFields[] = ["field" => "VATExempt_C", "value" => true];
        $customFields[] = ["field" => "VATExemptExpiryDate_C", "value" => $vatExpiryDate];

        $customerData['customerCode'] = $customerCode;
        $customerData['customFields'] = $customFields;

        echo '<br/>'.$jsonData = json_encode($customerData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
            $result = $this->putCurlData('customer', $jsonData);
        if ($result['api_error']) {
            $returnData['Status']='Success';
        } else{
            $returnData['Status']='Fail';
            $returnData['Status']='result';
        }
        return $returnData;
    }

    public function addNewShipTo($customerCode, $isVatExempt, $shipping_address, $billing_address){

        $shipping_country_name = $this->_pushcustomer->getCountryname($shipping_address['country_id']);
        $country_mapped_results_shipping = $this->getCountryMappingForInterprise($shipping_country_name);
        $country_code_shipping = $country_mapped_results_shipping['interprise_country'];



        $array_make = [
            'firstname'=>$billing_address['firstname'],
            'lastname'=>$billing_address['lastname'],
            'company'=>$billing_address['company']
        ];
        $shipToName = $this->_pushcustomer->makeShiptoName($array_make);

        if($this->magentoCustomerId!='' && $isVatExempt){

            $customer_objec  = $this->_customer->load($this->magentoCustomerId);
            $email = $customer_objec->getEmail();
            echo '<br/>$vatExemptShipToCode'.$shipToCode = $customer_objec->getData('vat_exempt_shipto_code');
            $newshipToCode = $shipToCode;
            $updateCustomer = true;
            $classCode = 'SHPCLS-000484';

        } else if($this->magentoCustomerId!='' && !$isVatExempt){

            $customer_objec  = $this->_customer->load($this->magentoCustomerId);
            $email = $customer_objec->getEmail();
            echo '<br/>$with_vat_shipto_code'.$shipToCode = $customer_objec->getData('with_vat_shipto_code');
            $newshipToCode = $shipToCode;
            $updateCustomer = true;
            $classCode = 'SHPCLS-000479';

        } else if($this->magentoCustomerId=='' && $isVatExempt){
            // Vat extemted Guest Order
            $shipToCode = "";
            $updateCustomer = false;
            $classCode = 'SHPCLS-000484';

        } else if($this->magentoCustomerId=='' && !$isVatExempt){
            // With VAT Guest Order
            $shipToCode = "";
            $updateCustomer = false;
            $classCode = 'SHPCLS-000479';
        }
           
        if($shipToCode==''){
            $newAddress['ShipToName'] = $shipToName;
            $newAddress['address'] = str_replace('\n', ', ', $shipping_address['street']);
            $newAddress['city'] = $shipping_address['city'];
            $newAddress['postalCode'] = $shipping_address['postcode'];
            $newAddress['country'] = $country_code_shipping;
            $newAddress['classCode'] = $classCode;

            $customerNewShipTo['customerCode'] = $customerCode;
            $customerNewShipTo['defaultShipTo'] = false;
            $customerNewShipTo['newShipToDetails'] = $newAddress;
            echo '<pre>';
            echo '<br/>'.$jsonData = json_encode($customerNewShipTo, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
            $result = $this->putCurlData('customer/AddShipTo', $jsonData);
            
            print_r($result);
            
            if ($result['api_error']) {
                $shipToCode = "SHIP" . filter_var($result['result'], FILTER_SANITIZE_NUMBER_INT);
                $shipToCodeArr = explode("-", $shipToCode);
                $newshipToCode = $shipToCodeArr[0].'-'.$shipToCodeArr[1];
            } else{
                $newshipToCode = '';
                $sratus['Status'] = false;
                $sratus['error'] = $e->getMessage();
                $sratus['shipToCode'] = '';
                return $sratus;
            }
            if($newshipToCode==''){
                $sratus['Status'] = false;
                $sratus['error'] = "Blank ShiptoCode";
                $sratus['shipToCode'] = '';
                return $sratus;
            } else if($newshipToCode!='' && $updateCustomer){
                try{
                
                    
                    $update_customer_factory = $this->customerFactory->create();
                    $customerData = $update_customer_factory->load($this->magentoCustomerId);
                    if($isVatExempt){
                        $customerData->setData('vat_exempt_shipto_code', $newshipToCode);
                        $customerDataModel = $customerData->getDataModel();
                        $customerDataModel->setCustomAttribute('vat_exempt_shipto_code', $newshipToCode);
                    } else{
                        $customerData->setData('with_vat_shipto_code', $newshipToCode);
                        $customerDataModel = $customerData->getDataModel();
                        $customerDataModel->setCustomAttribute('with_vat_shipto_code', $newshipToCode);
                    }
                    $customerData->updateData($customerDataModel);
                    $customerData->save();
                } catch(Exception $e){
                    
                    $sratus['Status'] = false;
                    $sratus['error'] = $e->getMessage();
                    $sratus['shipToCode'] = '';
                    return $sratus;
                }
            }

        }
        
           
        $sratus['Status'] = true;
        $sratus['error'] = '';
        $sratus['shipToCode'] = $newshipToCode;
        return $sratus;
    }

    public function createJsonOrder($order_obj, $coustmer_id, $is_guest)
    {
        
        $order = $order_obj;
        $extra_payment_charge = $order->getData('base_payment_charge');
        $history = $order->getStatusHistoryCollection()->getLastItem();
        $payment = $order->getPayment();
        
        echo '<br/>'.$payment_method = $payment->getMethod();

        $rewardAmountUsed = $order->getRewardAmount();        
        $items = $order->getAllItems();

        $order_data = [];
        $notes="";
        if($payment_method=='purchaseorder'){
            $notes = "PO: ".$payment->getData('po_number');
        } else{
            $notes=$order->getData('customer_note');
        }
        $po_number=$order->getData('notes');
        if (isset($po_number) && $po_number!='') {
            $poCode_is = $po_number;
        } else {
            $poCode_is ='Web Order';
        }
        $shipping_address = $order->getShippingAddress()->getData();
        $billing_address = $order->getBillingAddress()->getData();

        echo '<br/>matrix_id'.$matrix_id=filter_var($order->getShippingMethod(), FILTER_SANITIZE_NUMBER_INT);
        
        echo '<br/>matrix_shipping_method'.$matrix_shipping_method = trim(str_replace('Select Shipping Method -', '', $order->getShippingDescription()));

        echo '<br/>mapped_shipping_method'.$mapped_shipping_method = $this->getShippingMethod($matrix_shipping_method);
        $country_name = $this->_pushcustomer->getCountryname($billing_address['country_id']);
        $country_mapped_results_billing = $this->getCountryMappingForInterprise($country_name);
        $country_code_billing = $country_mapped_results_billing['interprise_country'];
        $classcode_mapped_billing = $country_mapped_results_billing['interprise_customer_class'];
        
        $shipping_country_name = $this->_pushcustomer->getCountryname($shipping_address['country_id']);
        $country_mapped_results_shipping = $this->getCountryMappingForInterprise($shipping_country_name);
        $country_code_shipping = $country_mapped_results_shipping['interprise_country'];
        $classcode_mapped_shipping = $country_mapped_results_shipping['interprise_customer_class'];

        ///////////////////////////////////////////////////////////////////////////////////////////
        
        if($is_guest){
            if ($coustmer_id != '') {

                $shipTowithvat = $this->getCustomerShipto($coustmer_id, 'UK 3 Exempt Sales Transactions');
                if($shipTowithvat){
                    $shipToCode = $shipTowithvat;
                } else {
                    $isVatExempt = false;
                    $resultPutShipTo = $this->addNewShipTo($coustmer_id, $isVatExempt, $shipping_address, $billing_address);

                    if($resultPutShipTo['Status'] && $resultPutShipTo['shipToCode']!=''){
                        $shipToCode = $resultPutShipTo['shipToCode'];
                    } else{
                        $order_data['items'] = array();
                        $sratus['Status'] = false;
                        $sratus['error'] = '';
                        $sratus['entity_id'] = array();
                        return $sratus;
                    }
                }
            }
        } else{
            $vatExemptCustomer = $order_obj->getVatExemptCustomer();
            $vatExemptReason = $order_obj->getVatExemptReason();
            $isVatExempt = false;
            if($vatExemptCustomer!='' && $vatExemptReason!=''){
                $isVatExempt = true;
            }

            $resultPutShipTo = $this->addNewShipTo($coustmer_id, $isVatExempt, $shipping_address, $billing_address);

            
            if($resultPutShipTo['Status'] && $resultPutShipTo['shipToCode']!=''){
                $shipToCode = $resultPutShipTo['shipToCode'];
            } else{
                $order_data['items'] = array();
                $sratus['Status'] = false;
                $sratus['error'] = '';
                $sratus['entity_id'] = array();
                return $sratus;
            }
        }

        $array_shiptomake = [
            'firstname'=>$shipping_address['firstname'],
            'lastname'=>$shipping_address['lastname'],
            'company'=>$shipping_address['company']
        ];
        $shipToName = $this->_pushcustomer->makeShiptoName($array_shiptomake);

        $array_billtomake = [
            'firstname'=>$billing_address['firstname'],
            'lastname'=>$billing_address['lastname'],
            'company'=>$billing_address['company']
        ];
        $billToName = $this->_pushcustomer->makeShiptoName($array_billtomake);

        

        $shippingAddress = [
            'shipToName' => $shipToName,
            'shipToAddress' => $shipping_address['street'],
            'shippingMethodCode' => $mapped_shipping_method,
            'shipToCity' => $shipping_address['city'],
            'shipToPostalCode' => $shipping_address['postcode'],
            'shipToCountry' => $country_code_shipping,
            'shipToCode' => $shipToCode,
        ];
        $billingAddress = [
            'billToName' => $billToName,
            'billToAddress' => $billing_address['street'],
            'billToCity' => $billing_address['city'],
            'billToCounty' => $billing_address['region'],
            'billToPostalCode' => $billing_address['postcode'],
            'billToCountry' => $country_code_billing,
        ];
        
        $payment_details = $this->getPaymentInfo($payment, $order_obj, $coustmer_id);
        if (!$payment_details['Status']) {
            return $payment_details;
        }
        $authresponses = [];
        if (count($payment_details['entity_id']['authResponse']) > 0) {
            $authresponses = $payment_details['entity_id']['authResponse'];
        }
        /************ Start Make item array for post *******************/
        $item_data = [];
        //if ($items) {
        $taxableDiscount = 0.00;
        $nonTaxableDiscount = 0.00;
        foreach ($items as $_item) {
            
            //echo '<br/>'.$_item->getProductId();
            
            $product_type = $_item->getData('product_type');
            if ($product_type == 'simple' && $_item->getData('parent_item_id') != '') {
            //if ($product_type == 'configurable') {
                //This condition checks the if this simple product is assoiated with any parent
                continue;
            }

            $itemDiscountedAmount = $_item->getDiscountAmount();
            $itemTaxAmount = $_item->getTaxAmount();
            if($itemDiscountedAmount > 0){
                if($itemTaxAmount > 0){
                    $taxableDiscount += $itemDiscountedAmount;
                } else{
                    $nonTaxableDiscount += $itemDiscountedAmount;
                }
            }

            $product_mod = $this->_product->create()->load($_item->getProductId());

            //echo '<br/>$itemName'.$itemName = $product_mod->getSku();
            $itemName = $_item->getSku();
            //$unitMeasureCode = 'EACH';
            $unitMeasureCode = $product_mod->getData('is_unitmeasurecode');
            if ($unitMeasureCode=='') {
                $unitMeasureCode='EACH';
            }
            $warehouseCode = $this->warehousecodefulfillment;

            $itemType = $product_mod->getData('interprise_item_type');
            $itemQuantity = $_item->getQtyOrdered();
            $itme_price = $_item->getBaseRowTotal();

            $itemUnitPrice = $itme_price / $itemQuantity;
            if ($product_type == 'bundle') {
                $selections = $product_mod->getTypeInstance(true)
                        ->getSelectionsCollection($product_mod->getTypeInstance(true)
                        ->getOptionsIds($product_mod), $product_mod);
                $store_id = $order->getStoreId();
                $options =$this->_bundleoptfact->create()->getResourceCollection()
                        ->setProductIdFilter($_item->product_id)
                        ->setPositionOrder();
                $options->joinValues($store_id);
                $selections = $product_mod->getTypeInstance(true)
                        ->getSelectionsCollection($product_mod->getTypeInstance(true)
                        ->getOptionsIds($product_mod), $product_mod);
                $array_opt = [];
                $group_data = [];
                foreach ($options->getItems() as $option) {
                    $option_id = $option->getId();
                    foreach ($selections as $selection) {
                        if ($option_id == $selection->getOptionId()) {
                            $array_opt[$option_id] = $selection->getProductId();
                        }
                    }
                }

                $bundle_data = $this->_seriializer->unserialize($_item->getData('product_options'));
                $bundle_options = $bundle_data['bundle_options'];
                if (count($bundle_options)) {
                    foreach ($bundle_options as $bundle_key => $bundle_value) {
                        $group_code = $bundle_value['label'];
                        $group_item_name = $bundle_value['value'][0]['title'];
                        $item_unit_price = $bundle_value['value'][0]['price'];
                        //echo "idididid---".$array_opt[$bundle_key];
                        $new_product =$this->productfactory->create()->load($array_opt[$bundle_key]);
                        $item_code = $new_product->getData('itemcode');
                        $group_item_name = $new_product->getData('sku');
                        $group_item_name = $this->removeSeparator($group_item_name);
                        $group_data[] = [
                            'groupCode' => $group_code,
                            'itemName' => $group_item_name,
                            'itemUnitPrice' => $item_unit_price,
                            'itemCode' => $item_code
                        ];
                    }
                }
                $item_data[] = [
                    'itemName' => $itemName,
                    'unitMeasureCode' => $unitMeasureCode,
                    'warehouseCode' => $warehouseCode,
                    'itemQuantity' => $itemQuantity,
                    'itemUnitPrice' => $itemUnitPrice,
                    'isKitItemPrice' => true,
                    'isUseCustomerPricing' => false,
                    'kitGroupItems' => $group_data
                ];
            } elseif ($product_type=='configurable') {
                $item_data[] = [
                    'itemName' => $itemName,
                    'unitMeasureCode' => $unitMeasureCode,
                    'warehouseCode' => $warehouseCode,
                    'itemType' => "Matrix Item",
                    'itemQuantity' => $itemQuantity,
                    'netItemPrice' => $itemUnitPrice
                ];
            } else {
                $item_data[] = [
                    'itemName' => $itemName,
                    'unitMeasureCode' => $unitMeasureCode,
                    'warehouseCode' => $warehouseCode,
                    'itemType' => $itemType,
                    'itemQuantity' => $itemQuantity,
                    'netItemPrice' => $itemUnitPrice
                ];
            }
        }

        if($taxableDiscount > 0){
            $item_data[] = [
                    'itemName' => 'COUPON_DISC1',
                    'unitMeasureCode' => 'EACH',
                    'itemType' => 'Non-Stock',
                    'itemQuantity' => -1,
                    'netItemPrice' => $taxableDiscount
                ];
        }
        if($nonTaxableDiscount > 0){
            $item_data[] = [
                    'itemName' => 'COUPON_DISC2',
                    'unitMeasureCode' => 'EACH',
                    'itemType' => 'Non-Stock',
                    'itemQuantity' => -1,
                    'netItemPrice' => $nonTaxableDiscount
                ];
        }

        if($rewardAmountUsed!=0.0){
            $item_data[] = [
                    'itemName' => 'FEATHER_DISC',
                    'unitMeasureCode' => 'EACH',
                    'itemType' => 'Non-Stock',
                    'itemQuantity' => -1,
                    'netItemPrice' => -$rewardAmountUsed
                ];
        }

        //}
         
        /************ End Make item array for post *******************/
        $shortname_store = $this->getConfig('setup/general/abbr');
        $order_data['customerCode'] = $coustmer_id;
        $order_data['notes'] = $notes;
        //$order_data['poCode'] = $poCode_is;
        $order_data['poCode'] = $shortname_store.' #'.$order->getIncrementId();
        $order_data['freightAmount'] = $order->getShippingAmount();
        //$order_data['salesRepOrderCode'] = $shortname_store.' #'.$order->getIncrementId();
        $order_data['salesRepOrderCode'] = $poCode_is;
        $order_data['shipAddress'] = $shippingAddress;
        $order_data['billToAddress'] = $billingAddress;
        if (isset($payment_details['entity_id']['paymentTerm']) && $payment_details['entity_id']['paymentTerm']!='') {
            $payment_term_1 = $payment_details['entity_id']['paymentTerm'];
        } else {
            $payment_term_1 = $payment_details['entity_id']['paymentTerm'];
        }
        $order_data['PaymentTerm'] = $payment_details['entity_id']['paymentTerm'];
        $order_data['paymentDetails'] = $payment_details['entity_id']['paymentDetails'];
        if (count($authresponses)>0) {
            $order_data['authResponse'] = $authresponses;
        }
        $order_data['items'] = $item_data;
        $sratus['Status'] = true;
        $sratus['error'] = '';
        $sratus['entity_id'] = $order_data;
        echo '<pre>';
        print_r($sratus);
        echo '</pre>';
       
        return $sratus;
    }

    public function getCustomerShipto($customerCode, $taxCode=''){
        $api_responsc = $this->getCurlData('customer/shipto?customercode=' . $customerCode);
        if ($api_responsc['api_error']) {
            $data_shipto = $api_responsc['results']['data'];
            if (count($data_shipto)>0) {
                foreach ($data_shipto as $key_shipto => $value_shipto) {
                    $attributes_shipto = $value_shipto['attributes'];
                    if(strtolower($attributes_shipto['taxCode'])==strtolower($taxCode)){
                        return $attributes_shipto(['shipToCode']);
                    }
                }
            }
        }
        return false;
    }
    
    public function removeSeparator($string)
    {
        if (strpos($string, '|K1|') !== false) {
            $string= substr($string, 0, strpos($string, '|K1|'));
        }
        return $string;
    }
    
    public function createCustomerGuest($order, $isVatExempt)
    {
        $shipping_address = $order->getShippingAddress()->getData();
        $billing_address = $order->getBillingAddress()->getData();
        $array_make = [
            'firstname'=>$billing_address['firstname'],
            'lastname'=>$billing_address['lastname'],
            'company'=>$billing_address['company']
        ];
        $default_c_name = $this->_pushcustomer->makeShiptoName($array_make);
        $json_array = [];
                $json_array['sourceCustomerID'] = 0000;
                /* Start to be uncommment */
                $json_array['email'] = $billing_address['email'];
                /* End to be uncommment */
                $json_array['namePrefix'] = 'Mr\/Ms';
                //$json_array['customerName'] = $default_address['first_name'].' '.$default_address['last_name'];
                $json_array['customerName'] = $default_c_name;
                $json_array['contact_Firstname'] = $billing_address['firstname'];
                $json_array['contact_Lastname'] = $billing_address['lastname'];
                $json_array['homePhone'] = $billing_address['telephone'];
                $json_array['workPhone'] = '';
                $json_array['mobilePhone'] = $billing_address['telephone'];
                $json_array['telephone'] = $billing_address['telephone'];
                $json_array['customerTypeCode'] = 'Sandpiper Web Customer';
               // $json_array['segmentCode'] = 'SAN';
                $address_cleaned = $billing_address['street'];
                $contury_name = $this->_pushcustomer->getCountryname($billing_address['country_id']);
                $countryclass_by_mapping = $this->_pushcustomer->getCountryMappingForInterprise($contury_name);
                $country_name_mapped = $countryclass_by_mapping['interprise_country'];
                $classcode_mapped = $countryclass_by_mapping['interprise_customer_class'];
                $shiptoclasscode_mapped = $countryclass_by_mapping['interprise_shipto_class'];
                
                $address_cleaned_shipto = str_replace('\n', ', ', $shipping_address['street']);
                $contury_name_shipto = $this->_pushcustomer->getCountryname($shipping_address['country_id']);
                $countryclass_by_mapping_shipto = $this->_pushcustomer
                    ->getCountryMappingForInterprise($contury_name_shipto);
                $country_name_mapped_shipto = $countryclass_by_mapping_shipto['interprise_country'];
                $classcode_mapped_shipto = $countryclass_by_mapping_shipto['interprise_customer_class'];
                $shiptoclasscode_mapped_shipto = $countryclass_by_mapping_shipto['interprise_shipto_class'];
                if($isVatExempt){
                    $classcode_mapped='CCLS-000484';
                    $shiptoclasscode_mapped_shipto = 'SHPCLS-000484';
                }
                $json_array['billingAddress'] = [
                    'address' => str_replace("\n", ",\r\n", $address_cleaned),
                    'city' => $billing_address['city'],
                    'county' => $billing_address['region'],
                    'postalCode' => $billing_address['postcode'],
                    'country' => $country_name_mapped,
                    'classcode'=>$classcode_mapped
                ];
                $json_array['shippingAddress'] = [
                    'address' => str_replace("\n", ",\r\n", $address_cleaned_shipto),
                    'city' => $shipping_address['city'],
                    'county' => $shipping_address['region'],
                    'postalCode' => $shipping_address['postcode'],
                    //'country' => $default_address['country_name'],
                    'country' => $country_name_mapped_shipto,
                    'classcode'=>$shiptoclasscode_mapped_shipto
                ];
                
                echo $json = json_encode(
                    $json_array,
                    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE
                );
                
                $result_post = $this->postCurlData('Customer', $json);
                $decoded  = json_decode($result_post['result'], true);
        if (is_array($decoded) && array_key_exists('errors', $decoded)) {
            $update_data['Status'] = false;
            $update_data['error'] = 'Some error occured in creation of Interprise';
            return $update_data;
        } else {
            $customer_id = "CUST" . filter_var($result_post['result'], FILTER_SANITIZE_NUMBER_INT);
            $update_data['Status'] = true;
            $update_data['entity_id'] = $customer_id;
            return $update_data;
        }
    }
    public function getShippingMethod($magento_shipping_method)
    {
        $modelsss = $this->_shippingstoreinterpriseFactory->create()->getCollection();
        $modelsss->addFieldToFilter('store_shipping_method', ['eq'=>$magento_shipping_method]);
        if ($modelsss->count()>0) {
            $data = $modelsss->getFirstItem();
            return $data['interprise_shipping_method_code'];
        } else {
            return 0;
        }
    }
    public function getPaymentInfo($payment_method, $order_obj, $cusotmer_codep = '')
    {
        $order = $order_obj;
        $billing_address = $order->getBillingAddress()->getData();
        $method = $payment_method->getMethod();
        $paylater = $payment_method->getPaylater();
//        echo '<pre>$order';
//	print_r($order->getData());
 //	die();	
	$this->ptcode = '';
        $post_method = $method;
        if (in_array($method, ['custompaymentmethod','paidinterprise','cashondelivery','interprisepaid', 'purchaseorder'])) {
            $post_method = 'interprisepaid';
            echo '<br/>ptcode '.$this->ptcode = $order->getIsPaymenttcode();
        }
        if (in_array($method, ['sagepaydirectpro','sagepayserver', 'sagepayserver'])) {
            $post_method = 'sagepay';
        }
        if (strpos($method, 'amazon') !== false) {
            $post_method = 'amazonPay';
        }
        if (strpos($method, 'paypal') !== false) {
            $post_method = 'paypal';
        }
        $customer_id = $order->getData('customer_id');
        $customers = $this->_customer->load($customer_id);
        //$customers = Mage::getModel("customer/customer")->load($customer_id);
        echo '<br/>$customer_code'.$customer_code = $customers->getInterpriseCustomerCode();
        $payment_methods = $this->getPaymentMethods($post_method, $customer_code);
        
        if ($paylater == '') {
            $paylater = 'Credit/Debit Card';
        }
       
        if ($method == 'paypal_direct') {
            $PaymentTerm = 'Credit Card';
            $paymentDetails['paymentMethod'] = 'Credit Card';
        }
        if ($method == 'sagepaydirectpro' || $method=='sagepayserver') {

            if ($payment_method->getData('cc_type') == 'MC') {
                $card_type = 'MasterCard';
            } else {
                $card_type = $payment_method->getData('cc_type');
            }

            $paymentDetails['cardNumber'] = $payment_method->getData('cc_last4');
            $paymentDetails['checkNumber'] = $payment_method->getData('echeck_routing_number');
            $paymentDetails['nameOnCard'] = $payment_method->getData('cc_owner');
            $paymentDetails['cardType'] = $card_type;
            $paymentDetails['cardExpMonth'] = $payment_method->getData('cc_exp_month');
            $paymentDetails['cardExpYear'] = $payment_method->getData('cc_exp_year');
            $paymentDetails['cardSecurityNumber'] = $payment_method->getData('cc_exp_year');
            $paymentDetails['cardAddress'] = $billing_address['street'];
            $paymentDetails['cardCity'] = $billing_address['city'];
            $countery = $this->countryFactory->loadByCode($billing_address['country_id']);
            $paymentDetails['cardCountry'] = $countery->getName();
            $paymentDetails['cardPostalCode'] = $billing_address['postcode'];
            $paymentDetails['cardEmail'] = $order->getData('customer_email');
            $paymentDetails['Authorize'] = 'Transfer';
        }
        $authResponse = [];
        if ($method == 'sagepaydirectpro' || $method=='sagepayserver' ) {
            echo '<br/>Inside This Condition';
            $sage_pay_info = $this->getSagePayInfo($order->getId());
            if ($sage_pay_info) {
                if (!in_array($sage_pay_info['status'], ['OK'])) {
                    $status['status'] = false;
                    $status['error'] = $sage_pay_info['status_detail'];
                    $status['entity_id'] = '';
                    return $status;
                }

                if ($sage_pay_info['tx_type'] == 'PAYMENT') {
                    $bv=1;
                }
                $authResponse['documentCode'] = $sage_pay_info['vendor_tx_code'];
                $avresult = "Address Result:";
                $avresult .=$sage_pay_info['address_result'];
                $avresult .="/PostCode Result:" . $sage_pay_info['postcode_result'];
                $authResponse['reference'] = '{' . str_replace(['{', '}'], ['', ''], $sage_pay_info['vps_tx_id']) . '}';
                $authResponse['responseMsg'] = $sage_pay_info['status_detail'];
                $authResponse['authCode'] = $sage_pay_info['tx_auth_no'];
                $authResponse['avsResult'] = $avresult;
                $authResponse['cvResult'] = $sage_pay_info['cv2result'];
                $authResponse['message'] = '';
                $authorizationResult = '';
                $authorizationResult .= "VPSProtocol=$sage_pay_info[vps_protocol]\r\n";
                $authorizationResult .= "Status=$sage_pay_info[status]\r\n";
                $authorizationResult .= "StatusDetail=$sage_pay_info[status_detail]\r\n";
                $authorizationResult .= "VPSTxId=$sage_pay_info[vps_tx_id]\r\n";
                $authorizationResult .= "SecurityKey=$sage_pay_info[security_key]\r\n";
                $authorizationResult .= "TxAuthNo=$sage_pay_info[tx_auth_no]\r\n";
                $authorizationResult .= "AVSCV2=$sage_pay_info[avscv2]\r\n";
                $authorizationResult .= "AddressResult=$sage_pay_info[address_result]\r\n";
                $authorizationResult .= "PostCodeResult=$sage_pay_info[postcode_result]\r\n";
                $authorizationResult .= "CV2Result=$sage_pay_info[cv2result]\r\n";
                $authorizationResult .= "3DSecureStatus=$sage_pay_info[threed_secure_status] ";
                $authResponse['authorizationResult'] = $authorizationResult;
                $authResponse['gatewayResponseCode'] = $sage_pay_info['status'];
                $authResponse['creditCardIsAuthorized'] = 1;
            }
        }
        echo '<pre>$payment_methods';
        print_r($payment_methods);
        $paymentDetails['paymentMethod']=$payment_methods['default_payment_method'];
        $PaymentTerm=$payment_methods['is_payment_term_code'];
        if ($PaymentTerm=='') {
            $PaymentTerm = $customers->getInterprisePtcode();
        }
        $payment_arr = [
            'paymentTerm' => $PaymentTerm,
            'paymentDetails' => $paymentDetails,
            'authResponse' => $authResponse
        ];
        $status['Status'] = true;
        $status['error'] = '';
        $status['entity_id'] = $payment_arr;
        return $status;
    }
    public function getPaymentMethods($payment_get_way, $customer_code)
    {
        echo '<br/>$payment_get_way'.$payment_get_way;
        $api_responsc=$this->getCurlData('customer?customerCode='.$customer_code);
        $interprice_custoner=$api_responsc['results']['data']['attributes'];
        echo '<br/>'.$paymentTermGroup=$interprice_custoner['paymentTermGroup'];
        echo '<br/>'.$paymentTermCode=$interprice_custoner['paymentTermCode'];
        if (isset($this->ptcode) && $this->ptcode!='') {
            echo '<br/>$this->ptcode '.$paymentTermCode = $this->ptcode;
        }
        $modelloadcoll = $this->_paymentmethodfact->create()->getCollection();
        $modelloadcoll->addFieldToFilter('is_payment_term_group', ['eq'=>$paymentTermGroup]);
        $modelloadcoll->addFieldToFilter('magento_method', ['eq'=>$payment_get_way]);
        $modelloadcoll->addFieldToFilter('is_isactive', ['eq'=>1]);
        if ($modelloadcoll->count()>0) {
            $data = $modelloadcoll->getData();
            
            return [
                //'is_payment_term_code'=>$paymentTermCode,
                'is_payment_term_code'=>$data[0]['is_payment_term_code'],
                'default_payment_method'=>$data[0]['default_payment_method']
            ];
        }
        return 0;
    }
}