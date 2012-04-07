<?php
require('Mandrill.php');

/**
 * This is the unit test for Mandril to make sure everything is in order.
 */
class MandrillTest extends PHPUnit_Framework_TestCase {
    private static $my_api_key = 'mykey';
    
    public function testNoLastError() {
        $this->assertNull(Mandrill::getLastError());
    }
    
    public function testCallTypeCount() {
        $this->assertCount(6, Mandrill::getApiCalls());
    }
    
    public function testUserCallsCount() {
        $this->assertCount(5, Mandrill::getApiCalls()['users']);
    }
    
    public function testMessagesCallsCount() {
        $this->assertCount(3, Mandrill::getApiCalls()['messages']);
    }
    
    public function testTagsCallsCount() {
        $this->assertCount(4, Mandrill::getApiCalls()['tags']);
    }
    
    public function testSendersCallsCount() {
        $this->assertCount(3, Mandrill::getApiCalls()['senders']);
    }
    
    public function testUrlsCallsCount() {
        $this->assertCount(3, Mandrill::getApiCalls()['urls']);
    }
    
    public function testTemplatesCallsCount() {
        $this->assertCount(5, Mandrill::getApiCalls()['templates']);
    }
    
    public function testNullApiKey() {
        $this->assertEmpty(Mandrill::getApiKey());
    }
    
    public function testSetApiKeyConstant() {
        define('MANDRILL_API_KEY',self::$my_api_key.'DEFINE');
        Mandrill::setApiKey();
        $this->assertEquals(self::$my_api_key.'DEFINE',Mandrill::getApiKey());
    }

    public function testSetApiKeyMethod() {
        Mandrill::setApiKey(self::$my_api_key);
        $this->assertEquals(self::$my_api_key,Mandrill::getApiKey());
    }
    
    public function testApiKeyStillSet() {
        $this->assertEquals(self::$my_api_key,Mandrill::getApiKey());
    }
    
    public function testInvalidCallType() {
        try {
            Mandrill::call(array('type'=>'bad_users', 'call'=>'ping'));
        } catch (Exception $expected) {
            $this->assertTrue($expected instanceof Exception);
            return;
        }
 
        $this->fail('An expected exception has not been raised.');
    }
    
    public function testInvalidUserPing() {
        try {
            Mandrill::call(array('type'=>'users', 'call'=>'ping', 'extra'=>true));
        } catch (Exception $expected) {
            $this->assertTrue($expected instanceof Exception);
            return;
        }
 
        $this->fail('An expected exception has not been raised.');
    }
    
    public function testUserPing() {
        $this->assertEquals('"PONG!"', Mandrill::call(array('type'=>'users', 'call'=>'ping')));
    }
    
    public function testUserList() {
        $o = Mandrill::call(array('type'=>'users', 'call'=>'info'));
        $this->assertTrue(is_object($o));
        $this->assertTrue(property_exists($o,'username'));
        $this->assertTrue(property_exists($o,'created_at'));
        $this->assertTrue(property_exists($o,'reputation'));
        $this->assertTrue(property_exists($o,'hourly_quota'));
        $this->assertTrue(property_exists($o,'stats'));
        $this->assertTrue(property_exists($o,'stats'));
    }
    
    public function testUserSenders() {
        $o = Mandrill::call(array('type'=>'users', 'call'=>'senders'));
        $this->assertTrue(is_array($o));
    }
}
