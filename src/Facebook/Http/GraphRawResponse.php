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
namespace Facebook\Http;

/**
 * Class GraphRawResponse
 *
 * @package Facebook
 */
class GraphRawResponse
{
    /**
     * @var array The response headers in the form of an associative array.
     */
    protected array $headers;

    /**
     * @var string|null The raw response body.
     */
    protected ?string $body;

    /**
     * @var int|null The HTTP status response code.
     */
    protected ?int $httpResponseCode;

    /**
     * Creates a new GraphRawResponse entity.
     *
     * @param string|array $headers The headers as a raw string or array.
     * @param string|null $body The raw response body.
     * @param int|null $httpStatusCode The HTTP response code (if sending headers as parsed array).
     */
    public function __construct(array|string $headers, ?string $body, ?int $httpStatusCode = null)
    {

        $this->httpResponseCode = $httpStatusCode;

        if (is_array($headers)) {
            $this->headers = $headers;
        } else {
            $this->setHeadersFromString($headers);
        }

        $this->body = $body;
    }

    /**
     * Return the response headers.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Return the body of the response.
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * Return the HTTP response code.
     */
    public function getHttpResponseCode(): ?int
    {
        return $this->httpResponseCode;
    }

    /**
     * Sets the HTTP response code from a raw header.
     */
    public function setHttpResponseCodeFromHeader(string $rawResponseHeader): void
    {
        // https://tools.ietf.org/html/rfc7230#section-3.1.2
        list(,$status) = array_pad(explode(' ', $rawResponseHeader, 3), 3, null);
        $this->httpResponseCode = (int)$status;
    }

    /**
     * Parse the raw headers and set as an array.
     *
     * @param string $rawHeaders The raw headers from the response.
     */
    protected function setHeadersFromString(string $rawHeaders): void
    {
        // Normalize line breaks
        $rawHeaders = str_replace("\r\n", "\n", $rawHeaders);

        // There will be multiple headers if a 301 was followed
        // or a proxy was followed, etc
        $headerCollection = explode("\n\n", trim($rawHeaders));
        // We just want the last response (at the end)
        $rawHeader = array_pop($headerCollection);

        $headerComponents = explode("\n", $rawHeader);
        foreach ($headerComponents as $line) {
            if (!str_contains($line, ': ')) {
                $this->setHttpResponseCodeFromHeader($line);
            } else {
                list($key, $value) = explode(': ', $line, 2);
                $this->headers[$key] = $value;
            }
        }
    }
}
