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
namespace Facebook\HttpClients;

/**
 * Class FacebookStream
 *
 * Abstraction for the procedural stream elements so that the functions can be
 * mocked and the implementation can be tested.
 *
 * @package Facebook
 */
class FacebookStream
{
    /**
     * @var resource Context stream resource instance
     */
    protected $stream;

    /**
     * @var array Response headers from the stream wrapper
     */
    protected array $responseHeaders = [];

    /**
     * Make a new context stream reference instance
     */
    public function streamContextCreate(array $options): void
    {
        $this->stream = stream_context_create($options);
    }

    /**
     * The response headers from the stream wrapper
     * For some reason, this must be nullable or it fails when mocked in test.
     * This array is never null.
     */
    public function getResponseHeaders(): ?array
    {
        return $this->responseHeaders;
    }

    /**
     * Send a stream wrapped request
     *
     * @param string $url
     *
     * @return string|false
     */
    public function fileGetContents(string $url): string|false
    {
        $rawResponse = file_get_contents($url, false, $this->stream);
        $this->responseHeaders = $http_response_header ?: [];

        return $rawResponse;
    }
}
