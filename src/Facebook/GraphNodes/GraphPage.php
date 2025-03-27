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

namespace Facebook\GraphNodes;

/**
 * Class GraphPage
 *
 * @package Facebook
 * @link https://developers.facebook.com/docs/graph-api/reference/page/
 */
class GraphPage extends GraphNode
{
    protected static array $graphObjectMap = [
        'featured_video'  => GraphVideo::class,
        'engagement'      => GraphEngagement::class,
        'contact_address' => GraphMailingAddress::class,
        'best_page'       => GraphPage::class,
        'location'        => GraphLocation::class,
        'cover'           => GraphCoverPhoto::class
    ];

    public function getId(): ?string
    {
        return $this->getField('id');
    }

    public function getAbout(): ?string
    {
        return $this->getField('about');
    }

    public function getCategory(): ?string
    {
        return $this->getField('category');
    }

    public function getDescription(): ?string
    {
        return $this->getField('description');
    }

    public function getDescriptionHtml(): ?string
    {
        return $this->getField('description_html');
    }

    /**
     * @return ?string[]
     */
    public function getEmails(): ?array
    {
        return $this->getField('emails');
    }

    public function getEngagement(): ?GraphEngagement
    {
        return $this->getField('engagement');
    }

    public function getName(): ?string
    {
        return $this->getField('name');
    }

    public function getAppId(): ?string
    {
        return $this->getField('app_id');
    }

    public function getBestPage(): ?GraphPage
    {
        return $this->getField('best_page');
    }

    public function getLocation(): ?GraphLocation
    {
        return $this->getField('location');
    }

    public function getCover(): ?GraphCoverPhoto
    {
        return $this->getField('cover');
    }

    public function getAccessToken(): ?string
    {
        return $this->getField('access_token');
    }

    public function getAffiliation(): ?string
    {
        return $this->getField('affiliation');
    }

    public function getFanCount(): ?int
    {
        return $this->getField('fan_count');
    }

    public function getFeaturedVideo(): ?GraphVideo
    {
        return $this->getField('featured_video');
    }

    public function getContactAddress(): ?GraphMailingAddress
    {
        return $this->getField('contact_address');
    }
}
