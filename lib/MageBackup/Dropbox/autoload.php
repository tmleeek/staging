<?php
namespace MageBackup\Dropbox;

function autoload($name) {
	// If the name doesn't start with "MageBackup\Dropbox\", then its not once of our classes.
	if (substr_compare($name, 'MageBackup\\Dropbox\\', 0, 19)) {
		return;
	}

	 // Take the "MageBackup\Dropbox\" prefix off.
	$stem			= \substr($name, 19);

	// Convert "\" and "_" to path separators.
	$pathified_stem = \str_replace(array("\\", "_"), '/', $stem);

	$path			= __DIR__ . "/Dropbox/" . $pathified_stem . ".php";

    if (\is_file($path)) {
        require_once $path;
    }
}

\spl_autoload_register('MageBackup\Dropbox\autoload', true, true);


//
$mapping	= array(
	'MageBackup\GuzzleHttp\Client' => __DIR__ . '/GuzzleHttp/Client.php',
    'MageBackup\GuzzleHttp\ClientInterface' => __DIR__ . '/GuzzleHttp/ClientInterface.php',
    'MageBackup\GuzzleHttp\Cookie\CookieJar' => __DIR__ . '/GuzzleHttp/Cookie/CookieJar.php',
    'MageBackup\GuzzleHttp\Cookie\CookieJarInterface' => __DIR__ . '/GuzzleHttp/Cookie/CookieJarInterface.php',
    'MageBackup\GuzzleHttp\Cookie\FileCookieJar' => __DIR__ . '/GuzzleHttp/Cookie/FileCookieJar.php',
    'MageBackup\GuzzleHttp\Cookie\SessionCookieJar' => __DIR__ . '/GuzzleHttp/Cookie/SessionCookieJar.php',
    'MageBackup\GuzzleHttp\Cookie\SetCookie' => __DIR__ . '/GuzzleHttp/Cookie/SetCookie.php',
    'MageBackup\GuzzleHttp\Exception\BadResponseException' => __DIR__ . '/GuzzleHttp/Exception/BadResponseException.php',
    'MageBackup\GuzzleHttp\Exception\ClientException' => __DIR__ . '/GuzzleHttp/Exception/ClientException.php',
    'MageBackup\GuzzleHttp\Exception\ConnectException' => __DIR__ . '/GuzzleHttp/Exception/ConnectException.php',
    'MageBackup\GuzzleHttp\Exception\GuzzleException' => __DIR__ . '/GuzzleHttp/Exception/GuzzleException.php',
    'MageBackup\GuzzleHttp\Exception\RequestException' => __DIR__ . '/GuzzleHttp/Exception/RequestException.php',
    'MageBackup\GuzzleHttp\Exception\SeekException' => __DIR__ . '/GuzzleHttp/Exception/SeekException.php',
    'MageBackup\GuzzleHttp\Exception\ServerException' => __DIR__ . '/GuzzleHttp/Exception/ServerException.php',
    'MageBackup\GuzzleHttp\Exception\TooManyRedirectsException' => __DIR__ . '/GuzzleHttp/Exception/TooManyRedirectsException.php',
    'MageBackup\GuzzleHttp\Exception\TransferException' => __DIR__ . '/GuzzleHttp/Exception/TransferException.php',
    'MageBackup\GuzzleHttp\functions' => __DIR__ . '/GuzzleHttp/functions.php',
    'MageBackup\GuzzleHttp\functions_include' => __DIR__ . '/GuzzleHttp/functions_include.php',
    'MageBackup\GuzzleHttp\Handler\CurlFactory' => __DIR__ . '/GuzzleHttp/Handler/CurlFactory.php',
    'MageBackup\GuzzleHttp\Handler\CurlFactoryInterface' => __DIR__ . '/GuzzleHttp/Handler/CurlFactoryInterface.php',
    'MageBackup\GuzzleHttp\Handler\CurlHandler' => __DIR__ . '/GuzzleHttp/Handler/CurlHandler.php',
    'MageBackup\GuzzleHttp\Handler\CurlMultiHandler' => __DIR__ . '/GuzzleHttp/Handler/CurlMultiHandler.php',
    'MageBackup\GuzzleHttp\Handler\EasyHandle' => __DIR__ . '/GuzzleHttp/Handler/EasyHandle.php',
    'MageBackup\GuzzleHttp\Handler\MockHandler' => __DIR__ . '/GuzzleHttp/Handler/MockHandler.php',
    'MageBackup\GuzzleHttp\Handler\Proxy' => __DIR__ . '/GuzzleHttp/Handler/Proxy.php',
    'MageBackup\GuzzleHttp\Handler\StreamHandler' => __DIR__ . '/GuzzleHttp/Handler/StreamHandler.php',
    'MageBackup\GuzzleHttp\HandlerStack' => __DIR__ . '/GuzzleHttp/HandlerStack.php',
    'MageBackup\GuzzleHttp\MessageFormatter' => __DIR__ . '/GuzzleHttp/MessageFormatter.php',
    'MageBackup\GuzzleHttp\Middleware' => __DIR__ . '/GuzzleHttp/Middleware.php',
    'MageBackup\GuzzleHttp\Pool' => __DIR__ . '/GuzzleHttp/Pool.php',
    'MageBackup\GuzzleHttp\PrepareBodyMiddleware' => __DIR__ . '/GuzzleHttp/PrepareBodyMiddleware.php',
    'MageBackup\GuzzleHttp\Promise\AggregateException' => __DIR__ . '/GuzzleHttp/Promise/AggregateException.php',
    'MageBackup\GuzzleHttp\Promise\CancellationException' => __DIR__ . '/GuzzleHttp/Promise/CancellationException.php',
    'MageBackup\GuzzleHttp\Promise\EachPromise' => __DIR__ . '/GuzzleHttp/Promise/EachPromise.php',
    'MageBackup\GuzzleHttp\Promise\FulfilledPromise' => __DIR__ . '/GuzzleHttp/Promise/FulfilledPromise.php',
    'MageBackup\GuzzleHttp\Promise\functions' => __DIR__ . '/GuzzleHttp/Promise/functions.php',
    'MageBackup\GuzzleHttp\Promise\functions_include' => __DIR__ . '/GuzzleHttp/Promise/functions_include.php',
    'MageBackup\GuzzleHttp\Promise\Promise' => __DIR__ . '/GuzzleHttp/Promise/Promise.php',
    'MageBackup\GuzzleHttp\Promise\PromiseInterface' => __DIR__ . '/GuzzleHttp/Promise/PromiseInterface.php',
    'MageBackup\GuzzleHttp\Promise\PromisorInterface' => __DIR__ . '/GuzzleHttp/Promise/PromisorInterface.php',
    'MageBackup\GuzzleHttp\Promise\RejectedPromise' => __DIR__ . '/GuzzleHttp/Promise/RejectedPromise.php',
    'MageBackup\GuzzleHttp\Promise\RejectionException' => __DIR__ . '/GuzzleHttp/Promise/RejectionException.php',
    'MageBackup\GuzzleHttp\Promise\TaskQueue' => __DIR__ . '/GuzzleHttp/Promise/TaskQueue.php',
    'MageBackup\GuzzleHttp\Psr7\AppendStream' => __DIR__ . '/GuzzleHttp/Psr7/AppendStream.php',
    'MageBackup\GuzzleHttp\Psr7\BufferStream' => __DIR__ . '/GuzzleHttp/Psr7/BufferStream.php',
    'MageBackup\GuzzleHttp\Psr7\CachingStream' => __DIR__ . '/GuzzleHttp/Psr7/CachingStream.php',
    'MageBackup\GuzzleHttp\Psr7\DroppingStream' => __DIR__ . '/GuzzleHttp/Psr7/DroppingStream.php',
    'MageBackup\GuzzleHttp\Psr7\FnStream' => __DIR__ . '/GuzzleHttp/Psr7/FnStream.php',
    'MageBackup\GuzzleHttp\Psr7\functions' => __DIR__ . '/GuzzleHttp/Psr7/functions.php',
    'MageBackup\GuzzleHttp\Psr7\functions_include' => __DIR__ . '/GuzzleHttp/Psr7/functions_include.php',
    'MageBackup\GuzzleHttp\Psr7\InflateStream' => __DIR__ . '/GuzzleHttp/Psr7/InflateStream.php',
    'MageBackup\GuzzleHttp\Psr7\LazyOpenStream' => __DIR__ . '/GuzzleHttp/Psr7/LazyOpenStream.php',
    'MageBackup\GuzzleHttp\Psr7\LimitStream' => __DIR__ . '/GuzzleHttp/Psr7/LimitStream.php',
    'MageBackup\GuzzleHttp\Psr7\MessageTrait' => __DIR__ . '/GuzzleHttp/Psr7/MessageTrait.php',
    'MageBackup\GuzzleHttp\Psr7\MultipartStream' => __DIR__ . '/GuzzleHttp/Psr7/MultipartStream.php',
    'MageBackup\GuzzleHttp\Psr7\NoSeekStream' => __DIR__ . '/GuzzleHttp/Psr7/NoSeekStream.php',
    'MageBackup\GuzzleHttp\Psr7\PumpStream' => __DIR__ . '/GuzzleHttp/Psr7/PumpStream.php',
    'MageBackup\GuzzleHttp\Psr7\Request' => __DIR__ . '/GuzzleHttp/Psr7/Request.php',
    'MageBackup\GuzzleHttp\Psr7\Response' => __DIR__ . '/GuzzleHttp/Psr7/Response.php',
    'MageBackup\GuzzleHttp\Psr7\ServerRequest' => __DIR__ . '/GuzzleHttp/Psr7/ServerRequest.php',
    'MageBackup\GuzzleHttp\Psr7\Stream' => __DIR__ . '/GuzzleHttp/Psr7/Stream.php',
    'MageBackup\GuzzleHttp\Psr7\StreamDecoratorTrait' => __DIR__ . '/GuzzleHttp/Psr7/StreamDecoratorTrait.php',
    'MageBackup\GuzzleHttp\Psr7\StreamWrapper' => __DIR__ . '/GuzzleHttp/Psr7/StreamWrapper.php',
    'MageBackup\GuzzleHttp\Psr7\UploadedFile' => __DIR__ . '/GuzzleHttp/Psr7/UploadedFile.php',
    'MageBackup\GuzzleHttp\Psr7\Uri' => __DIR__ . '/GuzzleHttp/Psr7/Uri.php',
    'MageBackup\GuzzleHttp\RedirectMiddleware' => __DIR__ . '/GuzzleHttp/RedirectMiddleware.php',
    'MageBackup\GuzzleHttp\RequestOptions' => __DIR__ . '/GuzzleHttp/RequestOptions.php',
    'MageBackup\GuzzleHttp\RetryMiddleware' => __DIR__ . '/GuzzleHttp/RetryMiddleware.php',
    'MageBackup\GuzzleHttp\TransferStats' => __DIR__ . '/GuzzleHttp/TransferStats.php',
    'MageBackup\GuzzleHttp\UriTemplate' => __DIR__ . '/GuzzleHttp/UriTemplate.php',
    'MageBackup\Psr\Http\Message\MessageInterface' => __DIR__ . '/Psr/Http/Message/MessageInterface.php',
    'MageBackup\Psr\Http\Message\RequestInterface' => __DIR__ . '/Psr/Http/Message/RequestInterface.php',
    'MageBackup\Psr\Http\Message\ResponseInterface' => __DIR__ . '/Psr/Http/Message/ResponseInterface.php',
    'MageBackup\Psr\Http\Message\ServerRequestInterface' => __DIR__ . '/Psr/Http/Message/ServerRequestInterface.php',
    'MageBackup\Psr\Http\Message\StreamInterface' => __DIR__ . '/Psr/Http/Message/StreamInterface.php',
    'MageBackup\Psr\Http\Message\UploadedFileInterface' => __DIR__ . '/Psr/Http/Message/UploadedFileInterface.php',
    'MageBackup\Psr\Http\Message\UriInterface' => __DIR__ . '/Psr/Http/Message/UriInterface.php',
);

spl_autoload_register(function ($class) use ($mapping) {
    if (isset($mapping[$class])) {
        require $mapping[$class];
    }
}, true, true);

require __DIR__ . '/GuzzleHttp/functions.php';
require __DIR__ . '/GuzzleHttp/Psr7/functions.php';
require __DIR__ . '/GuzzleHttp/Promise/functions.php';