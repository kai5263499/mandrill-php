<?php
/**
  * This class allows the user to easily consume MailChimp's Mandrill services
  * @package Mandrill
  *
  * @author  Wes Widner <wes@werxltd.com>
  *
  * @version 1.0
  *
  * @method string getVersion() Retrieve the Mandrill PHP library's current version
  * @method string getApiKey() Retrieve the API key that is currently set
  * @method mixed call() call(mixed $data) Call Mandril service using an associative array containing the parameters Mandrill for the given type of service and specific call
  * @link http://mandrillapp.com/api/docs/index.html Official documentation for Mandrill API call types and calls
  * @link ../../examples/user_info.php Example: Calling User/Info per http://mandrillapp.com/api/docs/users.html#method=info
  *
  */
abstract class Mandrill {
    /**
     * Stores the operating enviroment state. Null means the state has not been evaluated yet.
     * @since 1.0
     * @static
     * @ignore
     */
    private static $is_cli         = null;

    /**
     * The current Mandrill PHP lib version
     * @since 1.0
     * @static
     * @ignore
     */
    private static $version        = '1.0';

    /**
     * The Mandrill service URL
     * @since 1.0
     * @static
     * @ignore
     */    
    private static $api_url        = 'https://mandrillapp.com/api/1.0/%s/%s.json';
    
    /**
     * The user's API key
     * @since 1.0
     * @static
     * @ignore
     */
    private static $api_key        = null;
    
    /**
     * Holds known Mandrill API call array. Used to validate user requests
     * @since 1.0
     * @static
     * @ignore
     */
    private static $api_calls      = null;
    
    /**
     * Whether or not to send additional information to error_log
     * @since 1.0
     * @static
     * @ignore
     */
    private static $verbose        = false;
    
    /**
     * Shorthand for the key property which is required for all Mandrill requests
     * @since 1.0
     * @static
     * @ignore
     */
    private static $required_key   = array('key');
    
    /**
     * Stores last validation error message
     * @since 1.0
     * @static
     * @ignore
     */
    private static $last_error     = null;
    
    /**
     * Returns true if the class is being run from the command line, caches result
     * @since 1.0
     * @static
     * @ignore
     * @return bool True if the script is being run from a CLI or false if its being run from a webserver
     */
    private static function _is_cli() {
        if(is_null(self::$is_cli)) self::$is_cli = php_sapi_name() == 'cli';
        return self::$is_cli;
    }
    
    /**
     * Generates a structured array of valid Mandrill API calls
     * @since 1.0
     * @static
     * @ignore
     * @returns mixed Associative array of valid calls and their required parameters
     */
    private static function api_calls() {
        if(is_null(self::$api_calls)) self::$api_calls = array(
            /* Users Calls */
            'users'=>array(
                'info'             => self::$required_key,
                'ping'             => self::$required_key,
                'senders'          => self::$required_key,
                'disable-sender'   => array_merge_recursive(self::$required_key, array('domain')),
                'verify-sender'    => array_merge_recursive(self::$required_key, array('email'))
            ),
            
            /* Messages Calls */
            'messages'=>array(
                'send'             => array_merge_recursive(self::$required_key, array('message')),
                'send-template'    => array_merge_recursive(self::$required_key, array('template_name','template_content','message')),
                'search'           => array_merge_recursive(self::$required_key, array('query','date_from','date_to','tags','senders','limit'))
            ),
            
            /* Tags Calls */
            'tags'=>array(
                'list'             => self::$required_key,
                'info'             => array_merge_recursive(self::$required_key, array('tag')),
                'time-series'      => array_merge_recursive(self::$required_key, array('tag')),
                'all-time-series'  => self::$required_key
            ),
            
            /* Senders Calls */
            'senders'=>array(
                'list'             => self::$required_key,
                'info'             => array_merge_recursive(self::$required_key, array('address')),
                'time-series'      => array_merge_recursive(self::$required_key, array('address'))
            ),
            
            /* Urls Calls */
            'urls'=>array(
                'list'             => self::$required_key,
                'search'           => array_merge_recursive(self::$required_key, array('q')),
                'time-series'      => array_merge_recursive(self::$required_key, array('url'))
            ),
            
            /* Templates Calls */
            'templates'=>array(
                'add'              => array_merge_recursive(self::$required_key, array('name','code')),
                'info'             => array_merge_recursive(self::$required_key, array('name')),
                'update'           => array_merge_recursive(self::$required_key, array('name','code')),
                'delete'           => array_merge_recursive(self::$required_key, array('name')),
                'list'             => self::$required_key
            ),
            
            /* Webhooks Calls */
            'webhooks'=>array(
                'list'             => self::$required_key,
                'add'              => array_merge_recursive(self::$required_key, array('url','events')),
                'info'             => array_merge_recursive(self::$required_key, array('id')),
                'update'           => array_merge_recursive(self::$required_key, array('id','url','events')),
                'delete'           => array_merge_recursive(self::$required_key, array('id'))
            )
        );
        
        return self::$api_calls;
    }
    
    /**
     * Validates the user's parameters against known valid Mandrill API calls
     * @param string $call_type The type of Mandrill call to make, ex. 'users' or 'tags'
     * @param string $call The call to make, ex. 'ping' or 'info'
     * @param mixed $data An associative array of options that correspond with the Mandrill API call being made
     * @return bool True or false for successful validation
     * @since 1.0
     * @static
     * @ignore
     */
    private static function _validate_call(&$call_type,&$call,&$data) {
        $api_calls = self::api_calls();

        if(!array_key_exists($call_type,$api_calls)) throw new Exception('Invalid call type.');
        if(!array_key_exists($call, $api_calls[$call_type])) throw new Exception("Invalid call for call type $call_type");
        
        $diff_keys = array_diff(array_keys($data),$api_calls[$call_type][$call]);
        
        if(self::$verbose) error_log('MANDRILL: Invalid keys in call: '.implode(',',$diff_keys));
        if(count($diff_keys) > 0) throw new Exception('Invalid keys in call: '.implode(',',$diff_keys));
        
        // @todo actually validate the fields
        
        return true;
    }
    
    /**
     * Set the api_key. The Mandrill API key can be set in a number of ways. 
     ** It can be set by the parameters passed in for an API call, ie. Mandrill::call(array('key'=>'mykey')); 
     ** It can be set via Mandrill::setApiKey('mykey'); 
     ** It can be set directly in this class file
     ** It can be set by the MANDRILL_API_KEY constant
     * 
     * @param mixed|string $data Associative array containing a 'key' element or a the API key as a string
     * @since 1.0
     * @static
     * @ignore
     */
    private static function _set_api_key(&$data) {
        if(array_key_exists('key',$data)) self::$api_key = $data['key'];
        if((count($data) == 0 || is_null(self::$api_key))&& defined('MANDRILL_API_KEY')) self::$api_key = MANDRILL_API_KEY;
        
        if(!isset(self::$api_key)) throw new Exception('API Key must be set.');
    }
    
    /**
     * The main method which makes the curl request to the Mandrill API
     * @param mixed $data An associative array of options that correspond with the Mandrill API call being made
     * @return mixed The response from the server.
     * @since 1.0
     * @static
     * @ignore
     */
    private static function _call_api(&$data) {
        if(!array_key_exists('type',$data)) throw new Exception('API call type must be set.');
        if(!array_key_exists('call',$data)) throw new Exception('API call must be set.');
        
        self::_set_api_key($data);
        
        $call_type = $data['type'];
        $call = $data['call'];
        
        unset($data['type']);
        unset($data['call']);
        
        if(!self::_validate_call($call_type, $call, $data)) throw new Exception(self::$last_error);

        $data['key'] = self::$api_key;
        
        $data_string = json_encode($data);
        
        $parsed_url = sprintf(self::$api_url, $call_type, $call);
        
        if(self::$verbose) error_log("MANDRILL: Sending request to: $parsed_url with data: $data_string");
        if(self::_is_cli()) echo "MANDRILL: Sending request to: $parsed_url with data: $data_string".PHP_EOL;
        
        $ch = curl_init($parsed_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',                                                                                
            'Content-Length: ' . strlen($data_string))                                                                       
        );                                                                                                               
         
        $result = curl_exec($ch);
        
        if(self::_is_cli()) echo "Mandrill API result: $result".PHP_EOL;
        
        if($call != 'ping') $result = json_decode($result);
        
        // @todo: Check result and throw exception?
        
        return $result;
    }
    
    /**
     * Rather than defining each method individually we use this method to route the
     * method call to the appropriate handler. This method should not be used directly.
     * 
     * @param string $method Method user attempted to use
     * @param mixed $args Array of arguments the user passed to the method
     * @since 1.0
     * @static
     * @ignore
     */
    public static function __callStatic($method, $args) {
        switch($method) {
            case 'getApiCalls':
                return self::api_calls();;
            break;
            case 'setVerbose':
                if(count($args) < 1) self::$verbose = false;
                else self::$verbose = (bool) $args[0];
            break;
            case 'toggleVerbose':
                self::$verbose = !self::$verbose;
            break;
            case 'version':
            case 'getVersion':
                return self::$version;
            break;
            case 'getKey':
            case 'getApiKey':
                return self::$api_key;
            break;
            case 'setKey':
            case 'setApiKey':
                if(count($args) < 1) self::_set_api_key($args);
                if(is_string($args[0])) self::$api_key = $args[0];
                elseif(is_array($args[0])) self::_set_api_key($args[0]);
                else return false;
                return true;
            break;
            case 'getLastError':
                return self::$last_error;
            break;
            case 'call':
                if(count($args) != 1 || !is_array($args[0])) throw new Exception('Must pass one associative array with proper values set.');
                $args = $args[0];
                return self::_call_api($args);
            break;
        }
    }
}
