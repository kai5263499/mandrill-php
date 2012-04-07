# mandrill-php

mandrill-php is a PHP library for interfacing with MailChimp's Mandrill API

##Installation

```php
//Include the Mandrill class
require('Mandrill.php');
```

##Requirements

* A MailChimp account, Mandrill service enabled, Mandrill API key. See the Getting Started guide for more information http://help.mandrill.com/customer/portal/topics/214457-getting-started/articles
* PHP 5.3+
* PHP Curl extension

##Examples

        // Include Mandrill class
        require('../Mandrill.php');
        
        // The API key can be set in a number of ways, and once it is set it reamins
        // set for subsiquent calls. Here are a few methods of setting the API key.
        Mandrill::setKey('mykey');
        Mandrill::setApiKey('mykey');
        
        // Setting the API key via cosntant
        define('MANDRILL_API_KEY','mykey');
        
        // As a special case, calling setApiKey without an argument sets the api key 
        // in the class to null.
        Mandrill::setApiKey();
        
        // Call Mandrill API's users.info call
        // The docs for this call can be found at http://mandrillapp.com/api/docs/users.html#method=info
        // Also note that the api key can be set and reset with each call request so
        // the following two queries will work.
        Mandrill::call(array('key'=>'mykey', 'type'=>'users', 'call'=>'info'));
        Mandrill::call(array('type'=>'users', 'call'=>'info'));
        
        // The associative array sent is validated against an internal static list of
        // valid Mandrill queries and an exception is raised if an invalid query is specified.
        // For example, the following will produce an error.
        Mandrill::call(array('type'=>'bad_users', 'call'=>'ping'));
        
        // getApiCalls() returns an associative array of valid API queries broken down by
        // call type, ie. users
        Mandrill::getApiCalls();
        
        // For a list of valid calls please visit http://mandrillapp.com/api/docs/index.html
        
        // Setting verbosity to true prints some extra debuggin lines, like the exact 
        // URL and arguments sent to the Mandrill service
        Mandrill::toggleVerbose();
        Mandrill::setVerbose(true);
        
        // getLastError returns the verification error string produced by the last ::call()
        // This is mostly used to produce the verification exception message but it may be
        // useful to you.
        Mandrill::getLastError();

##Contributions

The Mandrill PHP class is 100% static written along a functional or lambda programming paradigm. If you want to learn more about this pattern of development and why this pattern was chosen above other, more traditional, PHP library approaches please read the following excellent blog post http://blog.lcf.name/2011/12/functional-programming-in-php.html

If you would like to help maintain this project and/or if you have any questions or comments about the library's design or implementation I'd love to hear from you.

##Unit Tests

A unit test for the Mandrill class is avaliable in the 'test' directory. You will need to change the Mandrill API key from 'mykey' to your Mandrill API key in order for the tests to complete. If you want to contribute any bugfixes or examples please add a unit test for your code and make sure nothing else breaks.