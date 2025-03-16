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
namespace Facebook;

use Facebook\Authentication\AccessToken;
use Facebook\Authentication\OAuth2Client;
use Facebook\FileUpload\FacebookFile;
use Facebook\FileUpload\FacebookResumableUploader;
use Facebook\FileUpload\FacebookTransferChunk;
use Facebook\FileUpload\FacebookVideo;
use Facebook\GraphNodes\GraphEdge;
use Facebook\Url\UrlDetectionInterface;
use Facebook\Url\FacebookUrlDetectionHandler;
use Facebook\PseudoRandomString\PseudoRandomStringGeneratorFactory;
use Facebook\PseudoRandomString\PseudoRandomStringGeneratorInterface;
use Facebook\HttpClients\HttpClientsFactory;
use Facebook\PersistentData\PersistentDataFactory;
use Facebook\PersistentData\PersistentDataInterface;
use Facebook\Helpers\FacebookCanvasHelper;
use Facebook\Helpers\FacebookJavaScriptHelper;
use Facebook\Helpers\FacebookPageTabHelper;
use Facebook\Helpers\FacebookRedirectLoginHelper;
use Facebook\Exceptions\FacebookSDKException;
use InvalidArgumentException;

/**
 * Class Facebook
 *
 * @package Facebook
 */
class Facebook
{
    /**
     * @const string Version number of the Facebook PHP SDK.
     */
    const VERSION = '8.0.0';

    /**
     * @const string Default Graph API version for requests.
     */
    const DEFAULT_GRAPH_VERSION = 'v20.0';

    /**
     * @const string The name of the environment variable that contains the app ID.
     */
    const APP_ID_ENV_NAME = 'FACEBOOK_APP_ID';

    /**
     * @const string The name of the environment variable that contains the app secret.
     */
    const APP_SECRET_ENV_NAME = 'FACEBOOK_APP_SECRET';

    /**
     * @var FacebookApp The FacebookApp entity.
     */
    protected FacebookApp $app;

    /**
     * @var FacebookClient The Facebook client service.
     */
    protected FacebookClient $client;

    /**
     * @var OAuth2Client|null The OAuth 2.0 client service.
     */
    protected ?OAuth2Client $oAuth2Client = null;

    /**
     * @var UrlDetectionInterface The URL detection handler.
     */
    protected UrlDetectionInterface $urlDetectionHandler;

    /**
     * @var PseudoRandomStringGeneratorInterface The cryptographically secure pseudo-random string generator.
     */
    protected PseudoRandomStringGeneratorInterface $pseudoRandomStringGenerator;

    /**
     * @var AccessToken|null The default access token to use with requests.
     */
    protected AccessToken|null $defaultAccessToken;

    /**
     * @var string|null The default Graph version we want to use.
     */
    protected string|null $defaultGraphVersion;

    /**
     * @var PersistentDataInterface The persistent data handler.
     */
    protected PersistentDataInterface $persistentDataHandler;

    /**
     * @var FacebookResponse|FacebookBatchResponse|null Stores the last request made to Graph.
     */
    protected FacebookResponse|FacebookBatchResponse|null $lastResponse;

    /**
     * Instantiates a new Facebook super-class object.
     * @throws FacebookSDKException
     */
    public function __construct(array $config = [])
    {
        $config = array_merge([
            'app_id' => getenv(static::APP_ID_ENV_NAME),
            'app_secret' => getenv(static::APP_SECRET_ENV_NAME),
            'default_graph_version' => static::DEFAULT_GRAPH_VERSION,
            'enable_beta_mode' => false,
            'http_client_handler' => null,
            'persistent_data_handler' => null,
            'pseudo_random_string_generator' => null,
            'url_detection_handler' => null,
        ], $config);

        if (!$config['app_id']) {
            throw new FacebookSDKException('Required "app_id" key not supplied in config and could not find fallback environment variable "' . static::APP_ID_ENV_NAME . '"');
        }
        if (!$config['app_secret']) {
            throw new FacebookSDKException('Required "app_secret" key not supplied in config and could not find fallback environment variable "' . static::APP_SECRET_ENV_NAME . '"');
        }

        $this->app = new FacebookApp($config['app_id'], $config['app_secret']);
        $this->client = new FacebookClient(
            HttpClientsFactory::createHttpClient($config['http_client_handler']),
            $config['enable_beta_mode']
        );
        $this->pseudoRandomStringGenerator = PseudoRandomStringGeneratorFactory::createPseudoRandomStringGenerator(
            $config['pseudo_random_string_generator']
        );
        $this->setUrlDetectionHandler($config['url_detection_handler'] ?: new FacebookUrlDetectionHandler());
        $this->persistentDataHandler = PersistentDataFactory::createPersistentDataHandler(
            $config['persistent_data_handler']
        );

        if (isset($config['default_access_token'])) {
            $t = $config['default_access_token'];
            if (!is_string($t) && !($t instanceof AccessToken)) {
                throw new InvalidArgumentException('Access token must be of type string or ' . AccessToken::class . '.');
            }
            $this->setDefaultAccessToken($config['default_access_token']);
        }

        $this->defaultGraphVersion = $config['default_graph_version'];
    }

    /**
     * Returns the FacebookApp entity.
     */
    public function getApp(): FacebookApp
    {
        return $this->app;
    }

    /**
     * Returns the FacebookClient service.
     */
    public function getClient(): FacebookClient
    {
        return $this->client;
    }

    /**
     * Returns the OAuth 2.0 client service.
     */
    public function getOAuth2Client(): OAuth2Client
    {
        if (!$this->oAuth2Client) {
            $app = $this->getApp();
            $client = $this->getClient();
            $this->oAuth2Client = new OAuth2Client($app, $client, $this->defaultGraphVersion);
        }

        return $this->oAuth2Client;
    }

    /**
     * Returns the last response returned from Graph.
     */
    public function getLastResponse(): FacebookResponse|FacebookBatchResponse|null
    {
        return $this->lastResponse;
    }

    /**
     * Returns the URL detection handler.
     */
    public function getUrlDetectionHandler(): ?UrlDetectionInterface
    {
        return $this->urlDetectionHandler;
    }

    /**
     * Changes the URL detection handler.
     */
    private function setUrlDetectionHandler(UrlDetectionInterface $urlDetectionHandler): void
    {
        $this->urlDetectionHandler = $urlDetectionHandler;
    }

    /**
     * Returns the default AccessToken entity.
     */
    public function getDefaultAccessToken(): ?AccessToken
    {
        return $this->defaultAccessToken;
    }

    /**
     * Sets the default access token to use with requests.
     *
     * @throws InvalidArgumentException
     */
    public function setDefaultAccessToken(AccessToken|string $accessToken): void
    {
        if (is_string($accessToken)) {
            $this->defaultAccessToken = new AccessToken($accessToken);

            return;
        }

        if ($accessToken instanceof AccessToken) {
            $this->defaultAccessToken = $accessToken;

            return;
        }

        throw new InvalidArgumentException('The default access token must be of type "string" or Facebook\AccessToken');
    }

    public function getDefaultGraphVersion(): string
    {
        return $this->defaultGraphVersion;
    }

    public function getRedirectLoginHelper(): FacebookRedirectLoginHelper
    {
        return new FacebookRedirectLoginHelper(
            $this->getOAuth2Client(),
            $this->persistentDataHandler,
            $this->urlDetectionHandler,
            $this->pseudoRandomStringGenerator
        );
    }

    public function getJavaScriptHelper(): FacebookJavaScriptHelper
    {
        return new FacebookJavaScriptHelper($this->app, $this->client, $this->defaultGraphVersion);
    }

    public function getCanvasHelper(): FacebookCanvasHelper
    {
        return new FacebookCanvasHelper($this->app, $this->client, $this->defaultGraphVersion);
    }

    public function getPageTabHelper(): FacebookPageTabHelper
    {
        return new FacebookPageTabHelper($this->app, $this->client, $this->defaultGraphVersion);
    }

    /**
     * Sends a GET request to Graph and returns the result.
     *
     * @param string $endpoint
     * @param AccessToken|string|null $accessToken
     * @param string|null $eTag
     * @param string|null $graphVersion
     *
     * @return FacebookResponse
     *
     * @throws FacebookSDKException
     */
    public function get(string $endpoint, AccessToken|string|null $accessToken = null, ?string $eTag = null, ?string $graphVersion = null): FacebookResponse
    {
        return $this->sendRequest(
            'GET',
            $endpoint,
            [],
            $accessToken,
            $eTag,
            $graphVersion
        );
    }

    /**
     * Sends a POST request to Graph and returns the result.
     *
     * @param string                  $endpoint
     * @param array                   $params
     * @param AccessToken|string|null $accessToken
     * @param string|null             $eTag
     * @param string|null             $graphVersion
     *
     * @return FacebookResponse
     *
     * @throws FacebookSDKException
     */
    public function post(string $endpoint, array $params = [], AccessToken|string|null $accessToken = null, ?string $eTag = null, ?string $graphVersion = null): FacebookResponse
    {
        return $this->sendRequest(
            'POST',
            $endpoint,
            $params,
            $accessToken,
            $eTag,
            $graphVersion
        );
    }

    /**
     * Sends a DELETE request to Graph and returns the result.
     *
     * @param string                  $endpoint
     * @param array                   $params
     * @param AccessToken|string|null $accessToken
     * @param string|null             $eTag
     * @param string|null             $graphVersion
     *
     * @return FacebookResponse
     *
     * @throws FacebookSDKException
     */
    public function delete(string $endpoint, array $params = [], AccessToken|string|null $accessToken = null, ?string $eTag = null, ?string $graphVersion = null): FacebookResponse
    {
        return $this->sendRequest(
            'DELETE',
            $endpoint,
            $params,
            $accessToken,
            $eTag,
            $graphVersion
        );
    }

    /**
     * Sends a request to Graph for the next page of results.
     *
     * @param GraphEdge $graphEdge The GraphEdge to paginate over.
     *
     * @return GraphEdge|null
     *
     * @throws FacebookSDKException
     */
    public function next(GraphEdge $graphEdge): ?GraphEdge
    {
        return $this->getPaginationResults($graphEdge, 'next');
    }

    /**
     * Sends a request to Graph for the previous page of results.
     *
     * @param GraphEdge $graphEdge The GraphEdge to paginate over.
     *
     * @return GraphEdge|null
     *
     * @throws FacebookSDKException
     */
    public function previous(GraphEdge $graphEdge): ?GraphEdge
    {
        return $this->getPaginationResults($graphEdge, 'previous');
    }

    /**
     * Sends a request to Graph for the next page of results.
     *
     * @param GraphEdge $graphEdge The GraphEdge to paginate over.
     * @param string    $direction The direction of the pagination: next|previous.
     *
     * @return GraphEdge|null
     *
     * @throws FacebookSDKException
     */
    public function getPaginationResults(GraphEdge $graphEdge, string $direction): ?GraphEdge
    {
        $paginationRequest = $graphEdge->getPaginationRequest($direction);
        if (!$paginationRequest) {
            return null;
        }

        $this->lastResponse = $this->client->sendRequest($paginationRequest);

        // Keep the same GraphNode subclass
        $subClassName = $graphEdge->getSubClassName();
        $graphEdge = $this->lastResponse->getGraphEdge($subClassName);

        return count($graphEdge) > 0 ? $graphEdge : null;
    }

    /**
     * Sends a request to Graph and returns the result.
     *
     * @param string                  $method
     * @param string                  $endpoint
     * @param array                   $params
     * @param AccessToken|string|null $accessToken
     * @param string|null             $eTag
     * @param string|null             $graphVersion
     *
     * @return FacebookResponse
     *
     * @throws FacebookSDKException
     */
    public function sendRequest(string $method, string $endpoint, array $params = [], AccessToken|string|null $accessToken = null, ?string $eTag = null, ?string $graphVersion = null): FacebookResponse
    {
        $accessToken = $accessToken ?: $this->defaultAccessToken;
        $graphVersion = $graphVersion ?: $this->defaultGraphVersion;
        $request = $this->request($method, $endpoint, $params, $accessToken, $eTag, $graphVersion);

        return $this->lastResponse = $this->client->sendRequest($request);
    }

    /**
     * Sends a batched request to Graph and returns the result.
     *
     * @param array                   $requests
     * @param AccessToken|string|null $accessToken
     * @param string|null             $graphVersion
     *
     * @return FacebookBatchResponse
     *
     * @throws FacebookSDKException
     */
    public function sendBatchRequest(array $requests, AccessToken|string|null $accessToken = null, ?string $graphVersion = null): FacebookBatchResponse
    {
        $accessToken = $accessToken ?: $this->defaultAccessToken;
        $graphVersion = $graphVersion ?: $this->defaultGraphVersion;
        $batchRequest = new FacebookBatchRequest(
            $this->app,
            $requests,
            $accessToken,
            $graphVersion
        );

        return $this->lastResponse = $this->client->sendBatchRequest($batchRequest);
    }

    /**
     * Instantiates an empty FacebookBatchRequest entity.
     *
     * @param  AccessToken|string|null $accessToken  The top-level access token. Requests with no access token
     *                                               will fallback to this.
     * @param  string|null             $graphVersion The Graph API version to use.
     * @return FacebookBatchRequest
     */
    public function newBatchRequest(AccessToken|string|null $accessToken = null, ?string $graphVersion = null): FacebookBatchRequest
    {
        $accessToken = $accessToken ?: $this->defaultAccessToken;
        $graphVersion = $graphVersion ?: $this->defaultGraphVersion;

        return new FacebookBatchRequest(
            $this->app,
            [],
            $accessToken,
            $graphVersion
        );
    }

    /**
     * Instantiates a new FacebookRequest entity.
     * @throws FacebookSDKException
     */
    public function request(string $method, string $endpoint, array $params = [], AccessToken|string|null $accessToken = null, ?string $eTag = null, ?string $graphVersion = null): FacebookRequest
    {
        $accessToken = $accessToken ?: $this->defaultAccessToken;
        $graphVersion = $graphVersion ?: $this->defaultGraphVersion;

        return new FacebookRequest(
            $this->app,
            $accessToken,
            $method,
            $endpoint,
            $params,
            $eTag,
            $graphVersion
        );
    }

    /**
     * Factory to create FacebookFile's.
     * @throws FacebookSDKException
     */
    public function fileToUpload(string $pathToFile): FacebookFile
    {
        return new FacebookFile($pathToFile);
    }

    /**
     * Factory to create FacebookVideo
     * @throws FacebookSDKException
     */
    public function videoToUpload(string $pathToFile): FacebookVideo
    {
        return new FacebookVideo($pathToFile);
    }

    /**
     * Upload a video in chunks.
     *
     * @param string $target The id of the target node before the /videos edge.
     * @param string $pathToFile The full path to the file.
     * @param array $metadata The metadata associated with the video file.
     * @param string|null $accessToken The access token.
     * @param int $maxTransferTries The max times to retry a failed upload chunk.
     * @param string|null $graphVersion The Graph API version to use.
     *
     * @throws FacebookSDKException
     */
    public function uploadVideo(string $target, string $pathToFile, array $metadata = [], ?string $accessToken = null, int $maxTransferTries = 5, ?string $graphVersion = null): array
    {
        $accessToken = $accessToken ?: $this->defaultAccessToken;
        $graphVersion = $graphVersion ?: $this->defaultGraphVersion;

        $uploader = new FacebookResumableUploader($this->app, $this->client, $accessToken, $graphVersion);
        $endpoint = '/'.$target.'/videos';
        $file = $this->videoToUpload($pathToFile);
        $chunk = $uploader->start($endpoint, $file);

        do {
            $chunk = $this->maxTriesTransfer($uploader, $endpoint, $chunk, $maxTransferTries);
        } while (!$chunk->isLastChunk());

        return [
          'video_id' => $chunk->getVideoId(),
          'success' => $uploader->finish($endpoint, $chunk->getUploadSessionId(), $metadata),
        ];
    }

    /**
     * Attempts to upload a chunk of a file in $retryCountdown tries.
     *
     * @param FacebookResumableUploader $uploader
     * @param string $endpoint
     * @param FacebookTransferChunk $chunk
     * @param int $retryCountdown
     *
     * @return FacebookTransferChunk
     *
     * @throws FacebookSDKException
     */
    private function maxTriesTransfer(FacebookResumableUploader $uploader, string $endpoint, FacebookTransferChunk $chunk, int $retryCountdown): FacebookTransferChunk
    {
        $newChunk = $uploader->transfer($endpoint, $chunk, $retryCountdown < 1);

        if ($newChunk !== $chunk) {
            return $newChunk;
        }

        $retryCountdown--;

        // If transfer() returned the same chunk entity, the transfer failed but is resumable.
        return $this->maxTriesTransfer($uploader, $endpoint, $chunk, $retryCountdown);
    }
}
