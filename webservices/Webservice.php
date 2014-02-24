<?php

/**
 * @file
 * Simple Webservices Library
 * 
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @licence GPL2
 */

namespace simple_webservices {

    /**
     * Webservice class.
     */
    abstract class Webservice {

        /// Version
        private static $VERSION = 1.0;
        /// Configurable part of the user agent string.
        private $user_agent = "";
        /// Base url for calls
        private $url_base;
        /// Base timeout
        private $curl_timeout = 5;
        /// Last http response code
        private $last_http_status;

        /**
         * Create a web services object
         */
        public function __construct(
        $base_url,
        $timeout = 5
        ) {
            $this->setBaseUrl($base_url);
            $this->setTimeout($timeout);
        }

        /**
         * Set the base URL
         * @param type $url
         */
        public function setBaseUrl($url) {
            $this->url_base = trim($url, ' /');
        }

        /**
         * Return base url.
         */
        public function getBaseUrl() {
            return $this->url_base;
        }

        /**
         * Set the configurable user agent string.
         */
        public function setUserAgent($useragent) {
            $this->user_agent = $useragent;
        }

        /**
         * Return user agent
         */
        public function getUserAgent() {
            return $this->user_agent;
        }

        /**
         * Set the default curl timeout
         * @param type $timeout
         */
        public function setTimeout($timeout = 5) {
            $this->curl_timeout = $timeout;
        }

        /**
         * Get the current timeout.
         */
        public function getTimeout() {
            return $this->curl_timeout;
        }

        /**
         * Return the last HTTP status code.
         */
        public function getLastHTTPStatus() {
            return $this->last_http_status;
        }

        /**
         * Execute a raw webservices query.
         * @param string $path Path to execute off base URL
         * @param array $parameters Parameters to pass
         * @param string $verb Either GET, POST, PUT, DELETE
         * @param array $headers Extra headers to send
         * @return array
         */
        protected function __execute_query($path, array $parameters = null, $verb = 'GET', array $headers = null) {

            // Normalise verb
            $verb = strtoupper($verb);
            if (!in_array($verb, ['GET', 'POST', 'PUT', 'DELETE']))
                throw new WebserviceException("Webservices verb $verb is unrecognised.");

            // Build request
            $request = "";
            if ($parameters)
                $request = http_build_query($parameters);

            $curl_handle = curl_init();

            switch ($verb) {
                case 'GET':
                    curl_setopt($curl_handle, CURLOPT_HTTPGET, true);
                    if (strpos($path, '?') !== false)
                        $path .= '&' . $request;
                    else
                        $path .= '?' . $request;
                    break;
                case 'PUT':
                    curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'PUT'); // Override request type
                    curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $request);
                    break;
                case 'POST':
                    curl_setopt($curl_handle, CURLOPT_POST, 1);
                    curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $request);
                    break;
                case 'DELETE':
                    curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'DELETE'); // Override request type
                    curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $request);
                    break;
            }

            curl_setopt($curl_handle, CURLOPT_URL, $this->getBaseUrl() . $path);
            curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, $this->getTimeout());
            curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl_handle, CURLOPT_USERAGENT, "Simple-Webservices v" . self::$VERSION . " <https://github.com/mapkyca/simple-webservices>; " . $this->getUserAgent());
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 2);

            if (!empty($headers) && is_array($headers)) {
                curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);
            }

            $buffer = curl_exec($curl_handle);
            $this->last_http_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);

            if ($error = curl_error($curl_handle))
                throw new WebserviceException($error);

            curl_close($curl_handle);

            return ['content' => $buffer, 'response' => $this->last_http_status];
        }

        /**
         * Execute a get webservices call.
         * @param string $path The path to call from the base url.
         * @param array $params Optionally, an array of parameters to send (keys are the parameter names)
         * @param array $headers Optionally, an array of headers to send with the request (keys are the header names)
         * @return mixed
         */
        public abstract function get($path, array $params = null, array $headers = null);

        /**
         * Execute a post webservices call.
         * @param string $path The path to call from the base url.
         * @param array $params Optionally, an array of parameters to send (keys are the parameter names)
         * @param array $headers Optionally, an array of headers to send with the request (keys are the header names)
         * @return mixed
         */
        public abstract function post($path, array $params = null, array $headers = null);

        /**
         * Execute a put webservices call.
         * @param string $path The path to call from the base url.
         * @param array $params Optionally, an array of parameters to send (keys are the parameter names)
         * @param array $headers Optionally, an array of headers to send with the request (keys are the header names)
         * @return mixed
         */
        public abstract function put($path, array $params = null, array $headers = null);

        /**
         * Execute a delete webservices call.
         * @param string $path The path to call from the base url.
         * @param array $params Optionally, an array of parameters to send (keys are the parameter names)
         * @param array $headers Optionally, an array of headers to send with the request (keys are the header names)
         * @return mixed
         */
        public abstract function delete($path, array $params = null, array $headers = null);
    }

    /**
     * Webservice exception
     */
    class WebserviceException extends \Exception {
        
    }

}