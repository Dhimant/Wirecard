<?php

namespace Czar\Wirecard\Controller\Payment;

class Success extends \Magento\Framework\App\Action\Action
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
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $OrderSender
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->layoutFactory = $layoutFactory;
        $this->_checkoutSession = $session;
        $this->_orderFactory = $OrderFactory;
        $this->_logger = $logger;
        $this->_order = $order;
        $this->_scopeConfig = $scopeConfig;
        $this->_orderSender = $OrderSender;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

            $response = $_REQUEST;
            
            if($response['paymentState']=='SUCCESS' && !empty($response['shoporderReference']))
            {
                $orderId = $response['shoporderReference'];
                $order_ref = $this->_order->load($orderId);
                $order_ref->setState("processing")->setStatus("processing");
                $order_ref->addStatusHistoryComment('Paid Successfully using Wirecard.');
                $order_ref->save();


                $this->_orderSender->send($order_ref); // This will send the email to the customer ! Hopefully ! :p

                $resultRedirect = $this->resultRedirectFactory->create();
                //$resultRedirect->setPath('checkout/onepage/success');
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $redirectPath = $this->_scopeConfig->getValue('payment/wirecardpayment/successurl', $storeScope);
                $resultRedirect->setPath($redirectPath);
               
                return $resultRedirect;
            }
    
    }


}
/* 
Array
(
    [amount] => 100.00
    [currency] => SGD
    [paymentType] => CCARD
    [financialInstitution] => Visa
    [language] => en
    [orderNumber] => 12201301
    [paymentState] => SUCCESS
    [shoporderReference] => 17
    [authenticated] => No
    [anonymousPan] => 0004
    [expiry] => 10/2036
    [cardholder] => Dhimant
    [maskedPan] => 940000******0004
    [gatewayReferenceNumber] => DGW_12201301_RN
    [gatewayContractNumber] => DemoContractNumber123
    [avsResponseCode] => X
    [avsResponseMessage] => Demo AVS ResultMessage
    [avsProviderResultCode] => X
    [avsProviderResultMessage] => Demo AVS ProviderResultMessage
    [responseFingerprintOrder] => amount,currency,paymentType,financialInstitution,language,orderNumber,paymentState,shoporderReference,authenticated,anonymousPan,expiry,cardholder,maskedPan,gatewayReferenceNumber,gatewayContractNumber,avsResponseCode,avsResponseMessage,avsProviderResultCode,avsProviderResultMessage,secret,responseFingerprintOrder
    [responseFingerprint] => bf0c7e81c13fa7e6df1285da495534b69ea06831267ee8db1348af220243ed6b28ef31fd2a43218726b1ab3bc8af680e66523974926a8cc0ef179d13014c12b9
)
*/