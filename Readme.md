Simple PHP Web service library
==============================

This is a simple web services call library (essentially a wrapper around CURL),
but because I essentially had to keep cutting and pasting the same code about I
thought it'd be good to put it into a reusable library.

Usage
-----

Create your web service endpoint, specifying the base url.


```php

try {

    require_once('Webservice.php');
    require_once('JSONWebservice.php');

    $json = new \simple_webservices\JSONWebservice('https://example.com/rest/');

    $result = $json->get('path/to/query', ['param1' => 'foo', 'param2' => 'bar']);

} catch  (\simple_webservices\WebserviceException $e) {
    echo $e->getMessage();
}

```

Author
------

* Marcus Povey <marcus@marcus-povey.co.uk>
