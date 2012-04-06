# mandrill-php

mandrill-php is a PHP library for interfacing with MailChimp's Mandrill API

##Installation

        //Include the Mandrill class
        require('Mandrill.php')

##Requirements

  *A MailChimp account, Mandrill service enabled, Mandrill API key. See the Getting Started guide for more information http://help.mandrill.com/customer/portal/topics/214457-getting-started/articles
  *PHP 5.3+
  *PHP Curl extension

##Examples

        // Include Mandrill class
        require('../Mandrill.php');
        
        // Call Mandrill API
        Mandrill::call(array('key'=>'mykey', 'type'=>'users', 'call'=>'info'));