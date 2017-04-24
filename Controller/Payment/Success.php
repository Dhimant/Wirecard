<?php

namespace Dhimant\Wirecard\Controller\Payment;

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
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
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
        $this->resourceConfig = $resourceConfig;

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

                try {
                        $order_ref = $this->_order->load($orderId);
                        $order_ref->setState("complete")->setStatus("complete");
                        $order_ref->setCanSendNewEmailFlag(true);
                        $order_ref->addStatusHistoryComment('Paid Successfully using Wirecard. Bin Number :','complete')->setIsCustomerNotified(true);
                        $order_ref->setBinNumber($binNumber);
                        $order_ref->save();
                        $this->_orderSender->send($order_ref); // This will send the email to the customer ! Hopefully ! :p

                     } catch (\Exception $e) {
                        $this->_logger->critical($e);
                        echo "Something went wrong with the payment !";
                }

                $resultRedirect = $this->resultRedirectFactory->create();
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $redirectPath = $this->_scopeConfig->getValue('payment/wirecardpayment/successurl', $storeScope);
                $resultRedirect->setPath($redirectPath);
               
                return $resultRedirect;
            }
    
    }

}