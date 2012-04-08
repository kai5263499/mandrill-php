<?php

/**
 * This examples shows how Mandrill library is used to obtain information about a user.
 */

require('../Mandrill.php');
require('config.php');

$request_json = '{"type":"messages","call":"send","message":{"html": "<h1>example html</h1>", "text": "example text", "subject": "example subject", "from_email": "wes@werxltd.com", "from_name": "example from_name", "to":[{"email": "wes@reasontostand.org", "name": "Wes Widner"}],"headers":{"...": "..."},"track_opens":true,"track_clicks":true,"auto_text":true,"url_strip_qs":true,"tags":["test","example","sample"],"google_analytics_domains":["werxltd.com"],"google_analytics_campaign":["..."],"metadata":["..."]}}';

$ret = Mandrill::call((array) json_decode($request_json));

print_r($ret);