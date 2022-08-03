<?php

namespace PurpleCommerce\RestrictedLogin\Observer;
use Magento\Framework\Event\ObserverInterface;


class CheckLoginPersistentObserver implements ObserverInterface
{
         /**
         * @var \Magento\Framework\App\Response\RedirectInterface
         */
        protected $redirect;

        /**
         * Customer session
         *
         * @var \Magento\Customer\Model\Session
         */
        protected $_customerSession;

        public function __construct(
            \Magento\Customer\Model\Session $customerSession,
            \Magento\Framework\App\Response\RedirectInterface $redirect

        ) {

            $this->_customerSession = $customerSession;
            $this->redirect = $redirect;

        }

        public function execute(\Magento\Framework\Event\Observer $observer)
        {
            $actionName = $observer->getEvent()->getRequest()->getFullActionName();
            $controller = $observer->getControllerAction();
            if($actionName!='iscron_cron_scheduler' && $actionName!='iscron_cron_processor' && $actionName!='iscron_cron_reattemptcron'){
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $openActions = array(
                    'create',
                    'createpost',
                    'login',
                    'loginpost',
                    'logoutsuccess',
                    'forgotpassword',
                    'forgotpasswordpost',
                    'resetpassword',
                    'resetpasswordpost',
                    'confirm',
                    'confirmation'
                );
                $state =  $objectManager->get('Magento\Framework\App\State');
                $area = $state->getAreaCode();
                $request = $objectManager->get('Magento\Framework\App\Request\Http');
                
                if(!$this->_customerSession->isLoggedIn() && $area=='frontend') {
                    /** @var \Magento\Framework\UrlInterface $urlInterface */
                    $urlInterface = $objectManager->get('Magento\Framework\UrlInterface');
                    $url = $urlInterface->getUrl('customer/account/login');
                    if(strpos($request->getPathInfo(), '/customer/account/') !== 0)
                    {
                        $observer->getControllerAction()->getResponse()->setRedirect($url);
                    }
                }else if($this->_customerSession->isLoggedIn() && $area=='frontend'){
                    $customerId=$this->_customerSession->getCustomer()->getId();
                    $customerRepository = $objectManager->get('Magento\Customer\Api\CustomerRepositoryInterface');
                    $customerData = $customerRepository->getById($customerId);
                    $type=$customerData->getCustomAttribute('interprise_businesstype')->getValue();
                    if(strtolower($type)!='business' || $type==''){
                        /** @var \Magento\Framework\UrlInterface $urlInterface */
                        $urlInterface = $objectManager->get('Magento\Framework\UrlInterface');
                        $url = $urlInterface->getUrl('nottradeuser');
                        $observer->getControllerAction()->getResponse()->setRedirect($url);
                        $this->_customerSession->logout()
                 ->setBeforeAuthUrl($this->redirect->getRefererUrl())
                 ->setLastCustomerId($customerId);
                        $messageManager = $objectManager->get('Magento\Framework\Message\ManagerInterface');
                        $messageManager->addError(__("You are not authorise to visit the trade site. Contact us."));
                    }
                    return $this;
                }else{
                    return $this;
                }
            }else{
                return $this;
            }
            

        }

}