# mandrill-php

mandrill-php is a PHP library for interfacing with MailChimp's Mandrill API

##Installation

        //Include the Mandrill class
        require('Mandrill.php')

##Requirements

* A MailChimp account, Mandrill service enabled, Mandrill API key. See the Getting Started guide for more information http://help.mandrill.com/customer/portal/topics/214457-getting-started/articles
* PHP 5.3+
* PHP Curl extension

##Examples

        // Include Mandrill class
        require('../Mandrill.php');
        
        // Call Mandrill API's users.info call
        // The docs for this call can be found at http://mandrillapp.com/api/docs/users.html#method=info
        Mandrill::call(array('key'=>'mykey', 'type'=>'users', 'call'=>'info'));

##Contributions

The Mandrill PHP class is 100% static written along a functional or lambda programming paradigm. If you want to learn more about this pattern of development and why this pattern was chosen above other, more traditional, PHP library approaches please read the following excellent blog post http://blog.lcf.name/2011/12/functional-programming-in-php.html

If you would like to help maintain this project and/or if you have any questions or comments about the library's design or implementation I'd love to hear from you.

##Unit Tests

A unit test for the Mandrill class is avaliable in the 'test' directory. You will need to change the Mandrill API key from 'mykey' to your Mandrill API key in order for the tests to complete. If you want to contribute any bugfixes or examples please add a unit test for your code and make sure nothing else breaks.