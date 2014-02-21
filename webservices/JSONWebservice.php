<?php

/**
 * @file
 * JSON Webservices Library 
 * 
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @licence GPL2
 */

namespace simple_webservices {
    
    /**
     * Connect to a JSON Webservice.
     */
    class JSONWebservice extends Webservice {
        
        public function __construct(
        $base_url
        ) {
            parent::__construct($base_url);
        }
        
        /**
         * Parse the response of a webservices query and parse it for json.
         * @param array $response
         */
        protected function parseWebserviceResponse(array $response) {
            if (!is_array($response))
                throw new WebserviceException('There appears to be no data returned from your webservices call');
            if (!isset($response['content']) || !$response['content'])
                throw new WebserviceException('Query executed, but response appears to be blank');
            
            $json = json_decode($response['content']);
            if (!$json)
                throw new WebserviceException("Webservices response doesn't appear to be JSON'");
            
            return $json;
        }
        
        public function delete($path, array $params = null, array $headers = null) {
            return $this->parseWebserviceResponse($this->__execute_query($path, $params, 'DELETE', $headers));
        }

        public function get($path, array $params = null, array $headers = null) {
            return $this->parseWebserviceResponse($this->__execute_query($path, $params, 'GET', $headers));
        }

        public function post($path, array $params = null, array $headers = null) {
            return $this->parseWebserviceResponse($this->__execute_query($path, $params, 'POST', $headers));
        }

        public function put($path, array $params = null, array $headers = null) {
            return $this->parseWebserviceResponse($this->__execute_query($path, $params, 'PUT', $headers));
        }

    }
}