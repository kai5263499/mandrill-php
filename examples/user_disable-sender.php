<?php

/**
 * This examples shows how Mandrill library is used to obtain information about a user.
 */

require('../Mandrill.php');
require('config.php');

print_r(Mandrill::call(array('type'=>'users', 'call'=>'disable-sender', 'domain':'werxltd.com')));