<?php

/**
 * This examples shows how Mandrill library is used to obtain information about a user.
 */

require('../Mandrill.php');
require('config.php');

$request_json = '{"type":"messages","call":"search","query":"example","date_from":"2011-1-1", "date_to": "2012-12-31", "tags":["example"],"senders":["wes@werxltd.com"],"limit":42}';

$ret = Mandrill::call((array) json_decode($request_json));

print_r($ret);