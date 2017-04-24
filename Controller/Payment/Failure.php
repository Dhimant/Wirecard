<?php

namespace Dhimant\Wirecard\Controller\Payment;

class Failure extends \Magento\Framework\App\Action\Action
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
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->layoutFactory = $layoutFactory;
        $this->_checkoutSession = $session;
        $this->_orderFactory = $OrderFactory;
        $this->_logger = $logger;
        $this->_order = $order;
        $this->_scopeConfig = $scopeConfig;
        $this->resourceConfig = $resourceConfig;
        $this->directory_list = $directory_list;
        $this->resultPageFactory = $resultPageFactory;


        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

            $response = $_REQUEST;
            
            if($response['paymentState']=='FAILURE' && !empty($response['shoporderReference']))
            {
                $orderId = $response['shoporderReference'];
                $order_ref = $this->_order->load($orderId);
                $order_ref->setState("canceled")->setStatus("canceled");
                $order_ref->addStatusHistoryComment('Transaction declined by Wirecard')->setIsCustomerNotified(false);
                $order_ref->save();

                $resultRedirect = $this->resultRedirectFactory->create();
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $redirectPath = $this->_scopeConfig->getValue('payment/wirecardpayment/failureurl', $storeScope);
              
                if($redirectPath=="checkout/onepage/failure")
                {
                $resultPage = $this->resultPageFactory->create();
                $resultPage->addHandle('checkout_onepage_failure_dhimant');
                return $resultPage;               
                }
                else {
                   $resultRedirect->setPath($redirectPath);
                   return $resultRedirect;
                }
            }



    }
}