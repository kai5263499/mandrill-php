<?php

/**
 * This examples shows how Mandrill library is used to obtain information about a user.
 */

require('../Mandrill.php');

Mandrill::call(array('key'=>'mykey', 'type'=>'users', 'call'=>'info'));