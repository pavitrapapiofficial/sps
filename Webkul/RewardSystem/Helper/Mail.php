<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_RewardSystem
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

namespace Webkul\RewardSystem\Helper;

class Mail extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_TRANSACTION_MAIL = 'rewardsystem/email_settings/rewardsystem_transaction';
    const XML_PATH_EXPIRY_MAIL = 'rewardsystem/email_settings/rewards_expiry';
    /**
     * @var templateId
     */
    protected $_template;
    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $_inlineTranslation;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_messageManager = $messageManager;
        $this->_storeManager = $storeManager;
    }

    public function sendMail($receiverInfo, $senderInfo, $msg, $totalPoints, $storeId = 0,$isorder=0)
    {
        $emailTempVariables = [];
        $emailTempVariables['customername'] = $receiverInfo['name'];
        $emailTempVariables['transactiondetails'] = $msg;
        if($isorder==1){
            $emailTempVariables['remainingdetails'] = __(
                "You now have %1 Feather Points in your online account.Thankyou for your order.",
                $totalPoints
            );
        }else{
            $emailTempVariables['remainingdetails'] = __(
                "You now have %1 Feather Points in your online account.You can use your Feather Points to get a discount off your next online purchase from sandpipershoes.com",
                $totalPoints
            );
        }
        
        $this->_template = $this->getTemplateId(self::XML_PATH_TRANSACTION_MAIL);
        $this->_inlineTranslation->suspend();
        $this->generateTemplate(
            $emailTempVariables,
            $senderInfo,
            $receiverInfo,
            $storeId
        );
        try {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->_messageManager->addError('unable to send email.');
        }
        $this->_inlineTranslation->resume();
    }
    /**
     * [sendExpireEmail]
     * @param  mixed  $receiverInfo
     * @param  mixed  $senderInfo
     * @param  string  $msg
     * @param  float  $totalPoints
     * @param  integer $storeId
     * @return bool
     */
    public function sendExpireEmail($receiverInfo, $senderInfo, $msg, $totalPoints, $storeId = 0)
    {
        $emailTempVariables = [];
        $emailTempVariables['customername'] = $receiverInfo['name'];
        $emailTempVariables['transactiondetails'] = $msg;
        $this->_template = $this->getTemplateId(self::XML_PATH_EXPIRY_MAIL);
        $this->_inlineTranslation->suspend();
        $this->generateTemplate(
            $emailTempVariables,
            $senderInfo,
            $receiverInfo,
            $storeId
        );
        try {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->_messageManager->addError('unable to send email.');
        }
        $this->_inlineTranslation->resume();
    }

    public function sendAdminMail($receiverInfo, $senderInfo, $adminMsg, $totalPoints, $storeId = 0,$isorder=0)
    {
        $emailTempVariables = [];
        $emailTempVariables['customername'] = 'Admin';
        $emailTempVariables['transactiondetails'] = $receiverInfo['name']." ".$adminMsg;
        if($isorder==1){
            $emailTempVariables['remainingdetails'] = __(
                "They now have %1 Feather Points left in their account.",
                $totalPoints
            );
        }else{
            $emailTempVariables['remainingdetails'] = __(
                "They now have %1 Feather Points in their account.",
                $totalPoints
            );
        }
        
        $this->_template = $this->getTemplateId(self::XML_PATH_TRANSACTION_MAIL);
        $this->_inlineTranslation->suspend();
        $this->generateTemplate(
            $emailTempVariables,
            $senderInfo,
            $senderInfo,
            $storeId
        );
        try {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->_messageManager->addError('unable to send email.');
        }
        $this->_inlineTranslation->resume();
    }
    
    protected function generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo, $storeId)
    {
        if (!$storeId) {
            $storeId = $this->_storeManager->getStore()->getId();
        }
        $template =  $this->_transportBuilder->setTemplateIdentifier($this->_template)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeId
                ]
            )
            ->setTemplateVars($emailTemplateVariables)
            ->setFrom($senderInfo)
            ->addTo($receiverInfo['email'], $receiverInfo['name']);
        return $this;
    }
    /**
     * Return template id.
     *
     * @return mixed
     */
    public function getTemplateId($xmlPath)
    {
        return $this->getConfigValue($xmlPath, $this->getStore()->getStoreId());
    }

    /**
     * Return store.
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * Return store configuration value.
     *
     * @param string $path
     * @param int    $storeId
     *
     * @return mixed
     */
    protected function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
