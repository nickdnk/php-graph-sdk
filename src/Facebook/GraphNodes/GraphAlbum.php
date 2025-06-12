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
 * Class GraphAlbum
 *
 * @package Facebook
 * @link https://developers.facebook.com/docs/graph-api/reference/album/
 */
class GraphAlbum extends GraphNode
{

    protected static array $graphObjectMap = [
        'from'        => GraphUser::class,
        'place'       => GraphPlace::class,
        'event'       => GraphEvent::class,
        'cover_photo' => GraphPhoto::class,
    ];

    public function getId(): ?string
    {
        return $this->getField('id');
    }

    public function getBackdatedTime(): ?DateTime
    {
        return $this->getField('backdated_time');
    }

    public function getCanUpload(): ?bool
    {
        return $this->getField('can_upload');
    }

    public function getCount(): ?int
    {
        return $this->getField('count');
    }

    public function getCoverPhoto(): ?GraphPhoto
    {
        return $this->getField('cover_photo');
    }

    public function getCreatedTime(): ?DateTime
    {
        return $this->getField('created_time');
    }

    public function getUpdatedTime(): ?DateTime
    {
        return $this->getField('updated_time');
    }

    public function getDescription(): ?string
    {
        return $this->getField('description');
    }

    public function getFrom(): ?GraphUser
    {
        return $this->getField('from');
    }

    public function getPlace(): ?GraphPlace
    {
        return $this->getField('place');
    }

    public function getLink(): ?string
    {
        return $this->getField('link');
    }

    public function getLocation(): ?string
    {
        return $this->getField('location');
    }

    public function getName(): ?string
    {
        return $this->getField('name');
    }

    public function getPrivacy(): ?string
    {
        return $this->getField('privacy');
    }

    /**
     * One of `album`, `app`, `cover`, `profile`, `mobile`, `normal` or `wall`.
     */
    public function getType(): ?string
    {
        return $this->getField('type');
    }

    public function getEvent(): ?GraphEvent
    {
        return $this->getField('event');
    }
}
