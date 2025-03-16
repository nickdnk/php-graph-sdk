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
 * Class FacebookCurl
 *
 * Abstraction for the procedural curl elements so that curl can be mocked and the implementation can be tested.
 *
 * @package Facebook
 */
class FacebookCurl
{

    /**
     * @var resource Curl resource instance
     */
    protected $curl;

    /**
     * Make a new curl reference instance
     */
    public function init()
    {
        $this->curl = curl_init();
    }

    /**
     * Set a curl option
     */
    public function setopt(int $key, mixed $value): void
    {
        curl_setopt($this->curl, $key, $value);
    }

    /**
     * Set an array of options to a curl resource
     */
    public function setoptArray(array $options): void
    {
        curl_setopt_array($this->curl, $options);
    }

    /**
     * Send a curl request
     */
    public function exec(): bool|string
    {
        return curl_exec($this->curl);
    }

    /**
     * Return the curl error number
     */
    public function errno(): ?int
    {
        // For some reason, adding int type to return here causes the mock testing framework to not work and return null.
        return curl_errno($this->curl);
    }

    /**
     * Return the curl error message
     */
    public function error(): string
    {
        return curl_error($this->curl);
    }

    /**
     * Get info from a curl reference
     */
    public function getinfo(?int $type): mixed
    {
        return curl_getinfo($this->curl, $type);
    }

    /**
     * Get the currently installed curl version
     *
     * @return array|false
     */
    public function version(): array|false
    {
        return curl_version();
    }

    /**
     * Close the resource connection to curl
     */
    public function close(): void
    {
        curl_close($this->curl);
    }
}
