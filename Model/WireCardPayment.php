<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dhimant\Wirecard\Model;



/**
 * Pay In Store payment method model
 */
class WireCardPayment extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'wirecardpayment';

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = false;


  

}
