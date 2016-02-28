<?php

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;

$loader = require_once __DIR__.'/../app/autoload.php';

$loader = new ApcClassLoader(sha1(__FILE__), $loader);
$loader->register(true);

require_once __DIR__.'/../app/AppKernel.php';
//require_once __DIR__.'/../app/AppCache.php';

$kernel = new AppKernel('prod', false);
//$kernel = new AppCache($kernel);
Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
