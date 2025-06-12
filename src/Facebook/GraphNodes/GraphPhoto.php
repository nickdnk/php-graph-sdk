<?php

namespace Facebook\GraphNodes;

use DateTime;

/**
 * @link https://developers.facebook.com/docs/graph-api/reference/photo/
 */
class GraphPhoto extends GraphNode
{

    protected static array $graphObjectMap = [
        'event'       => GraphEvent::class,
        'album'       => GraphAlbum::class,
        'images'      => GraphPlatformImageSource::class,
        'webp_images' => GraphPlatformImageSource::class,
        'place'       => GraphPlace::class,
    ];

    public function getId(): ?string
    {
        return $this->getField('id');
    }

    public function getAlbum(): ?GraphAlbum
    {
        return $this->getField('album');
    }

    public function getAltText(): ?string
    {
        return $this->getField('alt_text');
    }

    public function getAltTextCustom(): ?string
    {
        return $this->getField('alt_text_custom');
    }

    public function getBackDatedTime(): ?DateTime
    {
        return $this->getField('backdated_time');
    }

    public function canBackDate(): ?bool
    {
        return $this->getField('can_backdate');
    }

    public function canDelete(): ?bool
    {
        return $this->getField('can_delete');
    }

    public function canTag(): ?bool
    {
        return $this->getField('can_tag');
    }

    public function getCreatedTime(): ?DateTime
    {
        return $this->getField('created_time');
    }

    public function getEvent(): ?GraphEvent
    {
        return $this->getField('event');
    }

    public function getHeight(): ?int
    {
        return $this->getField('height');
    }

    public function getWidth(): ?int
    {
        return $this->getField('width');
    }

    public function getIcon(): ?string
    {
        return $this->getField('icon');
    }

    public function getLink(): ?string
    {
        return $this->getField('link');
    }

    public function getName(): ?string
    {
        return $this->getField('name');
    }

    public function getPageStoryId(): ?string
    {
        return $this->getField('page_story_id');
    }

    public function getPlace(): ?GraphPlace
    {
        return $this->getField('place');
    }

    public function getUpdatedTime(): ?DateTime
    {
        return $this->getField('updated_time');
    }

    /**
     * @return ?GraphPlatformImageSource[]
     */
    public function getWebPImages(): ?array
    {
        return $this->getField('webp_images');
    }

    /**
     * @return ?GraphPlatformImageSource[]
     */
    public function getImages(): ?array
    {
        return $this->getField('images');
    }

}