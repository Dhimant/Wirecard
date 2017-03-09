<?php
namespace Czar\Wirecard\Model;
use Czar\Wirecard\Api\BinInterface;
 
class Bin implements BinInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
         $this->_scopeConfig = $scopeConfig;
    }


    public function number($number) {
       
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $bins = $this->_scopeConfig->getValue('payment/wirecardpayment/bins', $storeScope);
        $bins = explode(',', $bins);


        if(in_array($number, $bins))
        {


            $result['response']['result']['bin_number'] = $number;
            $result['response']['result']['validation'] = 'valid';
            return $result;
        }
        else
        {

            $result['response']['result']['bin_number'] = $number;
            $result['response']['result']['validation'] = 'invalid';
            return $result;
        }

        //return "Hello, " . $name;
    }
}