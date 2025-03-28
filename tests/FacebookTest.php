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

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\FacebookBatchRequest;
use Facebook\FacebookClient;
use Facebook\FacebookRequest;
use Facebook\Authentication\AccessToken;
use Facebook\FacebookResponse;
use Facebook\GraphNodes\GraphEdge;
use Facebook\GraphNodes\GraphUser;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookGuzzleHttpClient;
use Facebook\HttpClients\FacebookStreamHttpClient;
use Facebook\PersistentData\FacebookMemoryPersistentDataHandler;
use Facebook\PseudoRandomString\OpenSslPseudoRandomStringGenerator;
use Facebook\PseudoRandomString\RandomBytesPseudoRandomStringGenerator;
use Facebook\PseudoRandomString\UrandomPseudoRandomStringGenerator;
use Facebook\Tests\Fixtures\FakeGraphApiForResumableUpload;
use Facebook\Tests\Fixtures\FooBarPseudoRandomStringGenerator;
use Facebook\Tests\Fixtures\FooClientInterface;
use Facebook\Tests\Fixtures\FooPersistentDataInterface;
use Facebook\Tests\Fixtures\FooUrlDetectionInterface;
use Facebook\Url\FacebookUrlDetectionHandler;
use InvalidArgumentException;

class FacebookTest extends BaseTestCase
{
    protected array $config = [
        'app_id' => '1337',
        'app_secret' => 'foo_secret',
    ];

    public function testInstantiatingWithoutAppIdThrows()
    {
        $this->expectException(FacebookSDKException::class)
        ;
        // unset value so there is no fallback to test expected Exception
        putenv(Facebook::APP_ID_ENV_NAME.'=');
        $config = [
            'app_secret' => 'foo_secret',
        ];
        new Facebook($config);
    }

    public function testInstantiatingWithoutAppSecretThrows()
    {
        $this->expectException(FacebookSDKException::class);

        // unset value so there is no fallback to test expected Exception
        putenv(Facebook::APP_SECRET_ENV_NAME.'=');
        $config = [
            'app_id' => 'foo_id',
        ];
        new Facebook($config);
    }

    public function testSettingAnInvalidHttpClientHandlerThrows()
    {
        $this->expectException(InvalidArgumentException::class);

        $config = array_merge($this->config, [
            'http_client_handler' => 'foo_handler',
        ]);
        new Facebook($config);
    }

    public function testCurlHttpClientHandlerCanBeForced()
    {
        if (!extension_loaded('curl')) {
            $this->markTestSkipped('cURL must be installed to test cURL client handler.');
        }
        $config = array_merge($this->config, [
            'http_client_handler' => 'curl'
        ]);
        $fb = new Facebook($config);
        $this->assertInstanceOf(
            FacebookCurlHttpClient::class,
            $fb->getClient()->getHttpClientHandler()
        );
    }

    public function testStreamHttpClientHandlerCanBeForced()
    {
        $config = array_merge($this->config, [
            'http_client_handler' => 'stream'
        ]);
        $fb = new Facebook($config);
        $this->assertInstanceOf(
            FacebookStreamHttpClient::class,
            $fb->getClient()->getHttpClientHandler()
        );
    }

    public function testGuzzleHttpClientHandlerCanBeForced()
    {
        $config = array_merge($this->config, [
            'http_client_handler' => 'guzzle'
        ]);
        $fb = new Facebook($config);
        $this->assertInstanceOf(
            FacebookGuzzleHttpClient::class,
            $fb->getClient()->getHttpClientHandler()
        );
    }

    public function testSettingAnInvalidPersistentDataHandlerThrows()
    {
        $this->expectException(InvalidArgumentException::class);
        $config = array_merge($this->config, [
            'persistent_data_handler' => 'foo_handler',
        ]);
        new Facebook($config);
    }

    public function testPersistentDataHandlerCanBeForced()
    {
        $config = array_merge($this->config, [
            'persistent_data_handler' => 'memory'
        ]);
        $fb = new Facebook($config);
        $this->assertInstanceOf(
            FacebookMemoryPersistentDataHandler::class,
            $fb->getRedirectLoginHelper()->getPersistentDataHandler()
        );
    }

    public function testSettingAnInvalidUrlHandlerThrows()
    {
        $expectedException = (PHP_MAJOR_VERSION > 5 && class_exists('TypeError'))
            ? 'TypeError'
            : 'PHPUnit_Framework_Error';

        $this->expectException($expectedException);

        $config = array_merge($this->config, [
            'url_detection_handler' => 'foo_handler',
        ]);
        new Facebook($config);
    }

    public function testTheUrlHandlerWillDefaultToTheFacebookImplementation()
    {
        $fb = new Facebook($this->config);
        $this->assertInstanceOf(FacebookUrlDetectionHandler::class, $fb->getUrlDetectionHandler());
    }

    public function testAnAccessTokenCanBeSetAsAString()
    {
        $fb = new Facebook($this->config);
        $fb->setDefaultAccessToken('foo_token');
        $accessToken = $fb->getDefaultAccessToken();

        $this->assertInstanceOf(AccessToken::class, $accessToken);
        $this->assertEquals('foo_token', (string)$accessToken);
    }

    public function testAnAccessTokenCanBeSetAsAnAccessTokenEntity()
    {
        $fb = new Facebook($this->config);
        $fb->setDefaultAccessToken(new AccessToken('bar_token'));
        $accessToken = $fb->getDefaultAccessToken();

        $this->assertInstanceOf(AccessToken::class, $accessToken);
        $this->assertEquals('bar_token', (string)$accessToken);
    }

    public function testSettingAnInvalidPseudoRandomStringGeneratorThrows()
    {
        $this->expectException(InvalidArgumentException::class);
        $config = array_merge($this->config, [
            'pseudo_random_string_generator' => 'foo_generator',
        ]);
        new Facebook($config);
    }

    public function testRandomBytesCsprgCanBeForced()
    {

        $config = array_merge($this->config, [
            'persistent_data_handler' => 'memory', // To keep session errors from happening
            'pseudo_random_string_generator' => 'random_bytes'
        ]);
        $fb = new Facebook($config);
        $this->assertInstanceOf(
            RandomBytesPseudoRandomStringGenerator::class,
            $fb->getRedirectLoginHelper()->getPseudoRandomStringGenerator()
        );
    }

    public function testOpenSslCsprgCanBeForced()
    {
        if (!function_exists('openssl_random_pseudo_bytes')) {
            $this->markTestSkipped(
                'The OpenSSL extension must be enabled to test openssl_random_pseudo_bytes().'
            );
        }

        $config = array_merge($this->config, [
            'persistent_data_handler' => 'memory', // To keep session errors from happening
            'pseudo_random_string_generator' => 'openssl'
        ]);
        $fb = new Facebook($config);
        $this->assertInstanceOf(
            OpenSslPseudoRandomStringGenerator::class,
            $fb->getRedirectLoginHelper()->getPseudoRandomStringGenerator()
        );
    }

    public function testUrandomCsprgCanBeForced()
    {
        if (ini_get('open_basedir')) {
            $this->markTestSkipped(
                'Cannot test /dev/urandom generator due to open_basedir constraint.'
            );
        }

        if (!is_readable('/dev/urandom')) {
            $this->markTestSkipped(
                '/dev/urandom not found or is not readable.'
            );
        }

        $config = array_merge($this->config, [
            'persistent_data_handler' => 'memory', // To keep session errors from happening
            'pseudo_random_string_generator' => 'urandom'
        ]);
        $fb = new Facebook($config);
        $this->assertInstanceOf(
            UrandomPseudoRandomStringGenerator::class,
            $fb->getRedirectLoginHelper()->getPseudoRandomStringGenerator()
        );
    }

    public function testSettingAnAccessThatIsNotStringOrAccessTokenThrows()
    {
        $this->expectException(InvalidArgumentException::class);
        $config = array_merge($this->config, [
            'default_access_token' => 123,
        ]);
        new Facebook($config);
    }

    public function testCreatingANewRequestWillDefaultToTheProperConfig()
    {
        $config = array_merge($this->config, [
            'default_access_token' => 'foo_token',
            'enable_beta_mode' => true,
            'default_graph_version' => 'v1337',
        ]);
        $fb = new Facebook($config);

        $request = $fb->request('FOO_VERB', '/foo');
        $this->assertEquals('1337', $request->getApp()->getId());
        $this->assertEquals('foo_secret', $request->getApp()->getSecret());
        $this->assertEquals('foo_token', (string)$request->getAccessToken());
        $this->assertEquals('v1337', $request->getGraphVersion());
        $this->assertEquals(
            FacebookClient::BASE_GRAPH_URL_BETA,
            $fb->getClient()->getBaseGraphUrl()
        );
    }

    public function testCreatingANewBatchRequestWillDefaultToTheProperConfig()
    {
        $config = array_merge($this->config, [
            'default_access_token' => 'foo_token',
            'enable_beta_mode' => true,
            'default_graph_version' => 'v1337',
        ]);
        $fb = new Facebook($config);

        $batchRequest = $fb->newBatchRequest();
        $this->assertEquals('1337', $batchRequest->getApp()->getId());
        $this->assertEquals('foo_secret', $batchRequest->getApp()->getSecret());
        $this->assertEquals('foo_token', (string)$batchRequest->getAccessToken());
        $this->assertEquals('v1337', $batchRequest->getGraphVersion());
        $this->assertEquals(
            FacebookClient::BASE_GRAPH_URL_BETA,
            $fb->getClient()->getBaseGraphUrl()
        );
        $this->assertInstanceOf(FacebookBatchRequest::class, $batchRequest);
        $this->assertCount(0, $batchRequest->getRequests());
    }

    public function testCanInjectCustomHandlers()
    {
        $config = array_merge($this->config, [
            'http_client_handler' => new FooClientInterface(),
            'persistent_data_handler' => new FooPersistentDataInterface(),
            'url_detection_handler' => new FooUrlDetectionInterface(),
            'pseudo_random_string_generator' => new FooBarPseudoRandomStringGenerator(),
        ]);
        $fb = new Facebook($config);

        $this->assertInstanceOf(
            FooClientInterface::class,
            $fb->getClient()->getHttpClientHandler()
        );
        $this->assertInstanceOf(
            FooPersistentDataInterface::class,
            $fb->getRedirectLoginHelper()->getPersistentDataHandler()
        );
        $this->assertInstanceOf(
            FooUrlDetectionInterface::class,
            $fb->getRedirectLoginHelper()->getUrlDetectionHandler()
        );
        $this->assertInstanceOf(
            FooBarPseudoRandomStringGenerator::class,
            $fb->getRedirectLoginHelper()->getPseudoRandomStringGenerator()
        );
    }

    public function testPaginationReturnsProperResponse()
    {
        $config = array_merge($this->config, [
            'http_client_handler' => new FooClientInterface(),
        ]);
        $fb = new Facebook($config);

        $request = new FacebookRequest($fb->getApp(), 'foo_token', 'GET');
        $graphEdge = new GraphEdge(
            $request,
            [],
            [
                'paging' => [
                    'cursors' => [
                        'after' => 'bar_after_cursor',
                        'before' => 'bar_before_cursor',
                    ],
                    'previous' => 'previous_url',
                    'next' => 'next_url',
                ]
            ],
            '/1337/photos',
            GraphUser::class
        );

        $nextPage = $fb->next($graphEdge);
        $this->assertInstanceOf(GraphEdge::class, $nextPage);
        $this->assertInstanceOf(GraphUser::class, $nextPage[0]);
        $this->assertEquals('Foo', $nextPage[0]['name']);

        $lastResponse = $fb->getLastResponse();
        $this->assertInstanceOf(FacebookResponse::class, $lastResponse);
        $this->assertEquals(1337, $lastResponse->getHttpStatusCode());
    }

    public function testCanGetSuccessfulTransferWithMaxTries()
    {
        $config = array_merge($this->config, [
          'http_client_handler' => new FakeGraphApiForResumableUpload(),
        ]);
        $fb = new Facebook($config);
        $response = $fb->uploadVideo('me', __DIR__.'/foo.txt', [], 'foo-token', 3);
        $this->assertEquals([
          'video_id' => '1337',
          'success' => true,
        ], $response);
    }

    public function testMaxingOutRetriesWillThrow()
    {
        $this->expectException(FacebookResponseException::class);
        $client = new FakeGraphApiForResumableUpload();
        $client->failOnTransfer();

        $config = array_merge($this->config, [
          'http_client_handler' => $client,
        ]);
        $fb = new Facebook($config);
        $fb->uploadVideo('4', __DIR__.'/foo.txt', [], 'foo-token', 3);
    }
}
