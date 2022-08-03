<?php


namespace Interprise\Logger\Observer\Sales;

use Interprise\Logger\Helper\Data;

class OrderPlaceAfter implements \Magento\Framework\Event\ObserverInterface
{
    protected $customerFactory;

    public function __construct(
        \Interprise\Logger\Model\ChangelogFactory $changelog,
        \Interprise\Logger\Helper\Data $helper,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->_changelog = $changelog;
        $this->_helper = $helper;
        $this->customerFactory  = $customerFactory;
    }
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        
        $order_ids = $observer->getEvent()->getOrderIds();
        //print_r($order_ids);
        $order_id = $order_ids[0];
        
        if ($order_id!=null || $order_id!='') {
            $changelog_master = $this->_changelog->create();
            $changelog_master->setData('CreatedAt',$this->_helper->getCurrentTime());
            $changelog_master->setData('ItemType','order');
            $changelog_master->setData('ItemId',$order_id);
            $changelog_master->setData('Action','POST');
            $changelog_master->setData('PushedStatus',0);
            $changelog_master->save();
        }
        $order = $observer->getEvent()->getOrder();
        $customer_id = $order->getCustomerId();
        if($customer_id){
            $vatExemptCustomer = $order->getVatExemptCustomer();
            $vatExemptReason = $order->getVatExemptReason();
            if($vatExemptCustomer!='' && $vatExemptReason!=''){
                $update_customer_factory = $this->customerFactory->create();
                $customerData = $update_customer_factory->load($customer_id);
                $vatExemptExpiryDate = $customerData->getData('vatexemptexpirydate_c');
                $currentDate = $this->_helper->getCurrentTime();
                if($vatExemptExpiryDate != ''){
                    if(strtotime($vatExemptExpiryDate) < strtotime($currentDate)){
                        $afterThreeYears = date('Y-m-d H:i:s', strtotime('+3 years', strtotime($currentDate)));
                        $customerDataModel = $customerData->getDataModel();
                        $customerDataModel->setCustomAttribute('vatexemptexpirydate_c', $afterThreeYears);
                        $customerData->updateData($customerDataModel);
                        $customerData->save();
                    }
                } else{
                    $afterThreeYears = date('Y-m-d H:i:s', strtotime('+3 years', strtotime($currentDate)));
                    $customerDataModel = $customerData->getDataModel();
                    $customerDataModel->setCustomAttribute('vatexemptexpirydate_c', $afterThreeYears);
                    $customerData->updateData($customerDataModel);
                    $customerData->save();
                }
            }
        }
        
    }
}
