<?php

namespace Czar\Wirecard\Controller\Payment;

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
        \Magento\Store\Model\StoreManagerInterface $storeManager



    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->layoutFactory = $layoutFactory;
        $this->_checkoutSession = $session;
        $this->_orderFactory = $OrderFactory;
        $this->_storeManager = $storeManager;



        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        //$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();      
        //http://127.0.0.1/czar_project_one/czar_redirect/payment/redirect/      
        // http://bed5899a.ngrok.io/
        $payment_page_url = 'https://test.wirecard.com.sg/easypay2/paymentpage.do?' ;
        $mid = '20151111011';                                                       
        $currency = 'SGD';                                                         
        $security_key = 'ABC123456';    
        $transtype = 'sale';
        $incrementId = $this->_checkoutSession->getLastRealOrder()->getIncrementId();
        $orderId = $this->_checkoutSession->getLastRealOrderId();
        $last_order = $this->_checkoutSession->getLastRealOrder();     
        $price =  $last_order->getGrandTotal();
        if (isset($price)) { } else { $price = 0.00;}
        $price = round($price, 2);
        $price = number_format((float)$price, 2, '.', '');
        $price = (string)$price;
        $orderId = $incrementId;
        $security_string = $price.$orderId.$currency.$mid.$transtype.$security_key;
        $signature = hash('sha512', $security_string); //@chintan: The signature is the important one
        
        $base_url = $this->_storeManager->getStore()->getBaseUrl();
        $response_url = $base_url."czar_response/payment/response";
        $verify_url = $base_url."czar_verify/payment/verify";

        //echo $base_url;
        //exit();




$form =<<<END
    <img src="http://www.uskoop.com.sg/shop/media/wysiwyg/wirecard.png" alt="wirecard payment gateway" >
    <form name="easypayform" method="post" action="$payment_page_url">
    <input type="hidden" name="mid" value="$mid" />                                       
    <input type="hidden" name="ref" value="$orderId" />
    <input type="hidden" name="cur" value="$currency" />
    <input type="hidden" name="transtype" value="sale" />
    <input type="hidden" name="version" value="2" />
    <input type="hidden" name="signature" value="$signature" />
    <input type="hidden" name="amt" value="$price" /> 
    <input type="hidden" name="returnurl" value="$response_url"/>
    <input type="hidden" name="statusurl" value="$verify_url" />
</form>
<script type="text/javascript">
document.easypayform.submit();
</script>
END;

echo $form;

    }
}