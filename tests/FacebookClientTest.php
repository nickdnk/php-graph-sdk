<?php
/**
 * Copyright 2017 Facebook, Inc.
 *
 * You are hereby granted a non-exclusive, worldwide, royalty-free license to
 * use, copy, modify, and distribute this software in source code or binary
 * form for use in connection with the web services and APIs provided by
 * Facebook.
 *
 * As with any software that integrates with the Facebook platform, your use
 * of this software is subject to the Facebook Developer Principles and
 * Policies [http://developers.facebook.com/policy/]. This copyright notice
 * shall be included in all copies or substantial portions of the software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 */

namespace Facebook\Tests;

use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\FacebookApp;
use Facebook\FacebookRequest;
use Facebook\FacebookBatchRequest;
use Facebook\FacebookClient;
use Facebook\FileUpload\FacebookFile;
use Facebook\FileUpload\FacebookVideo;
use Facebook\HttpClients\FacebookGuzzleHttpClient;
use Facebook\Tests\Fixtures\MyFooBatchClientHandler;
use Facebook\Tests\Fixtures\MyFooClientHandler;
use Facebook\FacebookBatchResponse;
use Facebook\FacebookResponse;
use Facebook\HttpClients\FacebookStreamHttpClient;
use Facebook\HttpClients\FacebookCurlHttpClient;

class FacebookClientTest extends BaseTestCase
{
    public FacebookApp $fbApp;
    public FacebookClient $fbClient;
    public static ?FacebookApp $testFacebookApp = null;
    public static ?FacebookClient $testFacebookClient = null;

    protected function setUp(): void
    {
        $this->fbApp = new FacebookApp('id', 'shhhh!');
        $this->fbClient = new FacebookClient(new MyFooClientHandler());
    }

    public function testACustomHttpClientCanBeInjected(): void
    {
        $handler = new MyFooClientHandler();
        $client = new FacebookClient($handler);
        $httpHandler = $client->getHttpClientHandler();

        $this->assertInstanceOf(MyFooClientHandler::class, $httpHandler);
    }

    public function testTheHttpClientWillFallbackToDefault(): void
    {
        $client = new FacebookClient();
        $httpHandler = $client->getHttpClientHandler();

        if (class_exists('GuzzleHttp\Client')) {
            $this->assertInstanceOf(FacebookGuzzleHttpClient::class, $httpHandler);
        } else if (function_exists('curl_init')) {
            $this->assertInstanceOf(FacebookCurlHttpClient::class, $httpHandler);
        } else {
            $this->assertInstanceOf(FacebookStreamHttpClient::class, $httpHandler);
        }
    }

    public function testBetaModeCanBeDisabledOrEnabledViaConstructor(): void
    {
        $client = new FacebookClient(null, false);
        $url = $client->getBaseGraphUrl();
        $this->assertEquals(FacebookClient::BASE_GRAPH_URL, $url);

        $client = new FacebookClient(null, true);
        $url = $client->getBaseGraphUrl();
        $this->assertEquals(FacebookClient::BASE_GRAPH_URL_BETA, $url);
    }

    public function testBetaModeCanBeDisabledOrEnabledViaMethod(): void
    {
        $client = new FacebookClient();
        $client->enableBetaMode(false);
        $url = $client->getBaseGraphUrl();
        $this->assertEquals(FacebookClient::BASE_GRAPH_URL, $url);

        $client->enableBetaMode(true);
        $url = $client->getBaseGraphUrl();
        $this->assertEquals(FacebookClient::BASE_GRAPH_URL_BETA, $url);
    }

    public function testGraphVideoUrlCanBeSet(): void
    {
        $client = new FacebookClient();
        $client->enableBetaMode(false);
        $url = $client->getBaseGraphUrl($postToVideoUrl = true);
        $this->assertEquals(FacebookClient::BASE_GRAPH_VIDEO_URL, $url);

        $client->enableBetaMode(true);
        $url = $client->getBaseGraphUrl($postToVideoUrl = true);
        $this->assertEquals(FacebookClient::BASE_GRAPH_VIDEO_URL_BETA, $url);
    }

    public function testAFacebookRequestEntityCanBeUsedToSendARequestToGraph(): void
    {
        $fbRequest = new FacebookRequest($this->fbApp, 'token', 'GET', '/foo');
        $response = $this->fbClient->sendRequest($fbRequest);

        $this->assertInstanceOf(FacebookResponse::class, $response);
        $this->assertEquals(200, $response->getHttpStatusCode());
        $this->assertEquals('{"data":[{"id":"123","name":"Foo"},{"id":"1337","name":"Bar"}]}', $response->getBody());
    }

    public function testAFacebookBatchRequestEntityCanBeUsedToSendABatchRequestToGraph(): void
    {
        $fbRequests = [
            new FacebookRequest($this->fbApp, 'token', 'GET', '/foo'),
            new FacebookRequest($this->fbApp, 'token', 'POST', '/bar'),
        ];
        $fbBatchRequest = new FacebookBatchRequest($this->fbApp, $fbRequests);

        $fbBatchClient = new FacebookClient(new MyFooBatchClientHandler());
        $response = $fbBatchClient->sendBatchRequest($fbBatchRequest);

        $this->assertInstanceOf(FacebookBatchResponse::class, $response);
        $this->assertEquals('GET', $response[0]->getRequest()->getMethod());
        $this->assertEquals('POST', $response[1]->getRequest()->getMethod());
    }

    public function testAFacebookBatchRequestWillProperlyBatchFiles(): void
    {
        $fbRequests = [
            new FacebookRequest($this->fbApp, 'token', 'POST', '/photo', [
                'message' => 'foobar',
                'source'  => new FacebookFile(__DIR__ . '/foo.txt'),
            ]),
            new FacebookRequest($this->fbApp, 'token', 'POST', '/video', [
                'message' => 'foobar',
                'source'  => new FacebookVideo(__DIR__ . '/foo2.txt'),
            ]),
        ];
        $fbBatchRequest = new FacebookBatchRequest($this->fbApp, $fbRequests);
        $fbBatchRequest->prepareRequestsForBatch();

        [$url, $method, $headers, $body] = $this->fbClient->prepareRequestMessage($fbBatchRequest);

        $this->assertEquals(FacebookClient::BASE_GRAPH_VIDEO_URL . '/' . Facebook::DEFAULT_GRAPH_VERSION, $url);
        $this->assertEquals('POST', $method);
        $this->assertStringContainsString('multipart/form-data; boundary=', $headers['Content-Type']);
        $this->assertStringContainsString('Content-Disposition: form-data; name="batch"', $body);
        $this->assertStringContainsString('Content-Disposition: form-data; name="include_headers"', $body);
        $this->assertStringContainsString('"name":0,"attached_files":', $body);
        $this->assertStringContainsString('"name":1,"attached_files":', $body);
        $this->assertStringContainsString('"; filename="foo.txt"', $body);
    }

    public function testARequestOfParamsWillBeUrlEncoded(): void
    {
        $fbRequest = new FacebookRequest($this->fbApp, 'token', 'POST', '/foo', ['foo' => 'bar']);
        $response = $this->fbClient->sendRequest($fbRequest);

        $headersSent = $response->getRequest()->getHeaders();

        $this->assertEquals('application/x-www-form-urlencoded', $headersSent['Content-Type']);
    }

    public function testARequestWithFilesWillBeMultipart(): void
    {
        $myFile = new FacebookFile(__DIR__ . '/foo.txt');
        $fbRequest = new FacebookRequest($this->fbApp, 'token', 'POST', '/foo', ['file' => $myFile]);
        $response = $this->fbClient->sendRequest($fbRequest);

        $headersSent = $response->getRequest()->getHeaders();

        $this->assertStringContainsString('multipart/form-data; boundary=', $headersSent['Content-Type']);
    }

    public function testAFacebookRequestValidatesTheAccessTokenWhenOneIsNotProvided(): void
    {
        $this->expectException(FacebookSDKException::class);

        $fbRequest = new FacebookRequest($this->fbApp, null, 'GET', '/foo');
        $this->fbClient->sendRequest($fbRequest);
    }

}
