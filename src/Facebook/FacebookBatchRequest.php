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

use ArrayIterator;
use InvalidArgumentException;
use IteratorAggregate;
use ArrayAccess;
use Facebook\Authentication\AccessToken;
use Facebook\Exceptions\FacebookSDKException;

/**
 * Class BatchRequest
 *
 * @package Facebook
 */
class FacebookBatchRequest extends FacebookRequest implements IteratorAggregate, ArrayAccess
{
    /**
     * @var array An array of FacebookRequest entities to send.
     */
    protected array $requests = [];

    /**
     * Creates a new Request entity.
     *
     * @param FacebookApp|null $app
     * @param FacebookRequest[] $requests
     * @param AccessToken|string|null $accessToken
     * @param string|null $graphVersion
     * @throws FacebookSDKException
     */
    public function __construct(?FacebookApp $app = null, array $requests = [], AccessToken|string|null $accessToken = null, ?string $graphVersion = null)
    {
        parent::__construct($app, $accessToken, 'POST', '', [], null, $graphVersion);

        $this->add($requests);
    }

    /**
     * Adds a new request to the array.
     *
     * @param FacebookRequest|FacebookRequest[] $request
     * @param array|string|null $options Array of batch request options e.g. 'name', 'omit_response_on_success'.
     *                                       If a string is given, it is the value of the 'name' option.
     *
     * @return FacebookBatchRequest
     *
     * @throws FacebookSDKException
     */
    public function add(array|FacebookRequest $request, int|array|string|null $options = null): self
    {
        if (is_array($request)) {
            foreach ($request as $key => $req) {
                $this->add($req, $key);
            }

            return $this;
        }

        if (!$request instanceof FacebookRequest) {
            throw new InvalidArgumentException('Argument for add() must be of type array or FacebookRequest.');
        }

        if (null === $options) {
            $options = [];
        } elseif (!is_array($options)) {
            $options = ['name' => $options];
        }

        $this->addFallbackDefaults($request);

        // File uploads
        $attachedFiles = $this->extractFileAttachments($request);

        $name = $options['name'] ?? null;

        unset($options['name']);

        $requestToAdd = [
            'name'           => $name,
            'request'        => $request,
            'options'        => $options,
            'attached_files' => $attachedFiles,
        ];

        $this->requests[] = $requestToAdd;

        return $this;
    }

    /**
     * Ensures that the FacebookApp and access token fall back when missing.
     *
     * @throws FacebookSDKException
     */
    public function addFallbackDefaults(FacebookRequest $request): void
    {
        if (!$request->getApp()) {
            $app = $this->getApp();
            if (!$app) {
                throw new FacebookSDKException('Missing FacebookApp on FacebookRequest and no fallback detected on FacebookBatchRequest.');
            }
            $request->setApp($app);
        }

        if (!$request->getAccessToken()) {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                throw new FacebookSDKException('Missing access token on FacebookRequest and no fallback detected on FacebookBatchRequest.');
            }
            $request->setAccessToken($accessToken);
        }
    }

    /**
     * Extracts the files from a request.
     */
    public function extractFileAttachments(FacebookRequest $request): ?string
    {
        if (!$request->containsFileUploads()) {
            return null;
        }

        $files = $request->getFiles();
        $fileNames = [];
        foreach ($files as $file) {
            $fileName = uniqid();
            $this->addFile($fileName, $file);
            $fileNames[] = $fileName;
        }

        $request->resetFiles();

        // @TODO Does Graph support multiple uploads on one endpoint?
        return implode(',', $fileNames);
    }

    /**
     * Return the FacebookRequest entities.
     *
     * @return FacebookRequest[]
     */
    public function getRequests(): array
    {
        return $this->requests;
    }

    /**
     * Prepares the requests to be sent as a batch request.
     * @throws FacebookSDKException
     */
    public function prepareRequestsForBatch(): void
    {
        $this->validateBatchRequestCount();

        $params = [
            'batch'           => $this->convertRequestsToJson(),
            'include_headers' => true,
        ];
        $this->setParams($params);
    }

    /**
     * Converts the requests into a JSON(P) string.
     * @throws FacebookSDKException
     */
    public function convertRequestsToJson(): string
    {
        $requests = [];
        foreach ($this->requests as $request) {
            $options = [];

            if (null !== $request['name']) {
                $options['name'] = $request['name'];
            }

            $options += $request['options'];

            $requests[] = $this->requestEntityToBatchArray($request['request'], $options, $request['attached_files']);
        }

        return json_encode($requests);
    }

    /**
     * Validate the request count before sending them as a batch.
     *
     * @throws FacebookSDKException
     */
    public function validateBatchRequestCount(): void
    {
        $batchCount = count($this->requests);
        if ($batchCount === 0) {
            throw new FacebookSDKException('There are no batch requests to send.');
        } elseif ($batchCount > 50) {
            // Per: https://developers.facebook.com/docs/graph-api/making-multiple-requests#limits
            throw new FacebookSDKException('You cannot send more than 50 batch requests at a time.');
        }
    }

    /**
     * Converts a Request entity into an array that is batch-friendly.
     *
     * @param FacebookRequest $request The request entity to convert.
     * @param string|null|array $options Array of batch request options e.g. 'name', 'omit_response_on_success'.
     *                                         If a string is given, it is the value of the 'name' option.
     * @param string|null $attachedFiles Names of files associated with the request.
     * @throws FacebookSDKException
     */
    public function requestEntityToBatchArray(FacebookRequest $request, array|string|null $options = null, ?string $attachedFiles = null): array
    {

        if (null === $options) {
            $options = [];
        } elseif (!is_array($options)) {
            $options = ['name' => $options];
        }

        $compiledHeaders = [];
        $headers = $request->getHeaders();
        foreach ($headers as $name => $value) {
            $compiledHeaders[] = $name . ': ' . $value;
        }

        $batch = [
            'headers'      => $compiledHeaders,
            'method'       => $request->getMethod(),
            'relative_url' => $request->getUrl(),
        ];

        // Since file uploads are moved to the root request of a batch request,
        // the child requests will always be URL-encoded.
        $body = $request->getUrlEncodedBody()->getBody();
        if ($body) {
            $batch['body'] = $body;
        }

        $batch += $options;

        if (null !== $attachedFiles) {
            $batch['attached_files'] = $attachedFiles;
        }

        return $batch;
    }

    /**
     * Get an iterator for the items.
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->requests);
    }

    /**
     * @inheritdoc
     * @throws FacebookSDKException
     */
    public function offsetSet($offset, $value): void
    {
        $this->add($value, $offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->requests[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->requests[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset): mixed
    {
        return $this->requests[$offset] ?? null;
    }
}
