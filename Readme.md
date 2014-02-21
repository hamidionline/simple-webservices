Simple Webservice
=================

This is a simple web services call library (essentially a wrapper around CURL),
but because I essentially had to keep cutting and pasting the same code about I
thought it'd be good to put it into a reusable library.

Usage
-----

Create your web service endpoint, specifying the base url.


```php

    require_once('simple_webservice/Webservice.php');
    require_once('simple_webservice/JSONWebservice.php');

    $json = new JSONWebservice('https://example.com/rest/');

    $result = $json->get('path/to/query', ['param1' => 'foo', 'param2' => 'bar']);

```

Author
------

* Marcus Povey <marcus@marcus-povey.co.uk>