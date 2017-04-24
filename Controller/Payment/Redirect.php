<?php

namespace Dhimant\Wirecard\Controller\Payment;

class Redirect extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    protected $productRepository;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Checkout\Model\Session $session,
        \Magento\Sales\Model\OrderFactory $OrderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\Order $order,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig

    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->layoutFactory = $layoutFactory;
        $this->_checkoutSession = $session;
        $this->_orderFactory = $OrderFactory;
        $this->_storeManager = $storeManager;
        $this->_order = $order;
        $this->_scopeConfig = $scopeConfig;
        $this->resourceConfig = $resourceConfig;
        $this->checkoutSession = $checkoutSession;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $payment_page_url = 'https://checkout.wirecard.com/page/init.php' ;
        $incrementId = $this->_checkoutSession->getLastRealOrder()->getIncrementId();
        $orderId = $this->_checkoutSession->getLastOrderId();

        $last_order = $this->_checkoutSession->getLastRealOrder();     
        $price =  $last_order->getGrandTotal();
        if (isset($price)) { } else { $price = 0.00;}
        $price = round($price, 2);
        $price = number_format((float)$price, 2, '.', '');
        $price = (string)$price;

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $customerId = $this->_scopeConfig->getValue('payment/wirecardpayment/customerid', $storeScope);
        $secret = $this->_scopeConfig->getValue('payment/wirecardpayment/secret', $storeScope);
        $language = $this->_scopeConfig->getValue('payment/wirecardpayment/language', $storeScope);
        $currency = $this->_scopeConfig->getValue('payment/wirecardpayment/currency', $storeScope);              
        $base_url = $this->_storeManager->getStore()->getBaseUrl();
        $successUrl = $base_url."dhimant_success/payment/success";
        $cancelUrl  = $base_url."dhimant_cancel/payment/cancel";
        $failureUrl = $base_url."dhimant_failure/payment/failure";
        $pendingUrl = $base_url."dhimant_pending/payment/pending";
        $serviceUrl = $base_url."dhimant_pending/payment/service";
        $paymentType = 'SELECT';
        $amount = $price;
        $orderDescription  = 'Test Payment';
        $shoporderReference = $orderId;

        /* This is some serious stuff. If you change the order here. It must be reflected in requestFingerprintOrder_value */
        $requestFingerprintOrder = 'customerId,language,amount,currency,orderDescription,successUrl,failureUrl,cancelUrl,serviceUrl,paymentType,shoporderReference,requestFingerprintOrder,secret';
        $requestFingerprintOrder_values = $customerId.$language.$amount.$currency.$orderDescription.$successUrl.$failureUrl.$cancelUrl.$serviceUrl.$paymentType.$shoporderReference.$requestFingerprintOrder.$secret;

        $requestFingerprint = hash_hmac('sha512', $requestFingerprintOrder_values,$secret); 


$form =<<<END
    <img src="http://www.uskoop.com.sg/shop/media/wysiwyg/wirecard.png" alt="wirecard payment gateway" >
    <form name="easypayform" method="post" action="$payment_page_url">
    <input type="hidden" name="customerId" value="$customerId" />                                       
    <input type="hidden" name="language" value="$language" />
    <input type="hidden" name="amount" value="$amount" />
    <input type="hidden" name="currency" value="$currency" />
    <input type="hidden" name="orderDescription" value="$orderDescription" />
    <input type="hidden" name="successUrl" value="$successUrl" />
    <input type="hidden" name="failureUrl" value="$failureUrl" />
    <input type="hidden" name="cancelUrl" value="$cancelUrl" />
    <input type="hidden" name="serviceUrl" value="$serviceUrl" />
    <input type="hidden" name="paymentType" value="$paymentType" /> 
    <input type="hidden" name="shoporderReference" value="$shoporderReference" /> 
    <input type="hidden" name="requestFingerprintOrder" value="$requestFingerprintOrder" /> 
    <input type="hidden" name="requestFingerprint" value="$requestFingerprint" /> 
</form>
<script type="text/javascript">
document.easypayform.submit();
</script>s
END;
echo $form;

    }

}