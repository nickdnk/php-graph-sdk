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

use DateTime;

/**
 * Class GraphGroup
 *
 * @package Facebook
 * @link https://developers.facebook.com/docs/graph-api/reference/group
 */
class GraphGroup extends GraphNode
{
    /**
     * @var array Maps object key names to GraphNode types.
     */
    protected static array $graphObjectMap = [
        'cover' => GraphCoverPhoto::class
    ];

    public function getId(): ?string
    {
        return $this->getField('id');
    }

    public function getCover(): ?GraphCoverPhoto
    {
        return $this->getField('cover');
    }

    public function getDescription(): ?string
    {
        return $this->getField('description');
    }

    public function getEmail(): ?string
    {
        return $this->getField('email');
    }

    public function getIcon(): ?string
    {
        return $this->getField('icon');
    }

    public function getMemberCount(): ?int
    {
        return $this->getField('member_count');
    }

    public function getMemberRequestCount(): ?int
    {
        return $this->getField('member_request_count');
    }

    public function getName(): ?string
    {
        return $this->getField('name');
    }

    /**
     * @deprecated Deprecated in v9.0. Probably does not work anymore.
     */
    public function getOwner(): ?GraphNode
    {
        return $this->getField('owner');
    }

    public function getParent(): ?GraphNode
    {
        return $this->getField('parent');
    }

    public function getPermissions(): ?string
    {
        return $this->getField('permissions');
    }

    public function getPrivacy(): ?string
    {
        return $this->getField('privacy');
    }

    public function getUpdatedTime(): ?DateTime
    {
        return $this->getField('updated_time');
    }

}
