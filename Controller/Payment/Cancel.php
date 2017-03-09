<?php

namespace Czar\Wirecard\Controller\Payment;
use Magento\Framework\Controller\ResultFactory;


class Cancel extends \Magento\Framework\App\Action\Action
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
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->layoutFactory = $layoutFactory;
        $this->_checkoutSession = $session;
        $this->_orderFactory = $OrderFactory;
        $this->_logger = $logger;
        $this->_order = $order;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

            $response = $_REQUEST;
            
            if($response['paymentState']=='CANCEL' && !empty($response['shoporderReference']))
            {
                $orderId = $response['shoporderReference'];
                $order_ref = $this->_order->load($orderId);
                //$order_ref->setState("canceled")->setStatus("canceled");
                $order_ref->addStatusHistoryComment('Order canceled by Wirecard');
                //$order_ref->save();
                $order_ref->cancel()->save();

              

                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $redirectPath = $this->_scopeConfig->getValue('payment/wirecardpayment/failureurl', $storeScope);
                return $resultRedirect->setPath($redirectPath);


                //$resultRedirect = $this->resultRedirectFactory->create();
               
                
                //$resultRedirect->setPath($redirectPath);

                //return $resultRedirect;
            }



    }
}