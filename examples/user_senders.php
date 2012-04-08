<?php

/**
 * This examples shows how Mandrill library is used to obtain information about a user.
 */

require('../Mandrill.php');
require('config.php');

Mandrill::call(array('type'=>'users', 'call'=>'senders'));