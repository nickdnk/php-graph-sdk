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
namespace Facebook\Helpers;

use Facebook\FacebookApp;
use Facebook\FacebookClient;

/**
 * Class FacebookPageTabHelper
 *
 * @package Facebook
 */
class FacebookPageTabHelper extends FacebookCanvasHelper
{
    /**
     * @var array|null
     */
    protected ?array $pageData = null;

    /**
     * Initialize the helper and process available signed request data.
     *
     * @param FacebookApp    $app          The FacebookApp entity.
     * @param FacebookClient $client       The client to make HTTP requests.
     * @param string|null    $graphVersion The version of Graph to use.
     */
    public function __construct(FacebookApp $app, FacebookClient $client, ?string $graphVersion = null)
    {
        parent::__construct($app, $client, $graphVersion);

        if (!$this->signedRequest) {
            return;
        }

        $this->pageData = $this->signedRequest->get('page');
    }

    /**
     * Returns a value from the page data.
     */
    public function getPageData(string $key, mixed $default = null): mixed
    {
        if (isset($this->pageData[$key])) {
            return $this->pageData[$key];
        }

        return $default;
    }

    public function isAdmin(): bool
    {
        return $this->getPageData('admin') === true;
    }

    public function getPageId(): ?string
    {
        return $this->getPageData('id');
    }
}
