# Wirecard-Payment-Gateway-Magento-2
Wirecard Payment Gateway for magento 2

**How to setup** 

Put these files/Directories within app/Czar/Wirecard in your magento installation. 

After that run the following commands from your magento installation 


php bin/magento setup:upgrade
php bin/magento module:enable Czar/Wirecard

You can find settings for the module inside admin area. 

Store --> Configuration --> Sales -> Payment Methods -> Wirecard 

Following Settings are available. 

Enabled : Yes, No

Title : Title you want to appear on checkout page. 

Payment for Applicable Countries : select country for which you want to enable the payment gateway. 

Wirecard Language : language code 

Wirecard Currency : Currency to use for checkout. 

Failure url : URL to which costomer will be redirect after an unsuccessful payment. 

Instructions : Instruction for the customer on checkout Page. 

Success URL : Redirects to this url after successful payment. 

Wirecard Customer ID : Your wirecard customer ID. 

Sort Order : 



