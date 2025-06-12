<?php

namespace Facebook\GraphNodes;

use DateTime;

/**
 * @link https://developers.facebook.com/docs/graph-api/reference/video/
 */
class GraphVideo extends GraphNode
{

    protected static array $graphObjectMap = [
        'event'  => GraphEvent::class,
        'format' => GraphVideoFormat::class,
        'place'  => GraphPlace::class,
    ];

    public function getId(): ?string
    {
        return $this->getField('id');
    }

    /**
     * @return ?int[]
     */
    public function adBreaks(): ?array
    {
        return $this->getField('ad_breaks');
    }

    public function getCreatedTime(): ?DateTime
    {
        return $this->getField('created_time');
    }

    /**
     * @return ?string[]
     */
    public function getCustomLabels(): ?array
    {
        return $this->getField('custom_labels');
    }

    public function getDescription(): ?string
    {
        return $this->getField('description');
    }

    public function getEmbedHtml(): ?string
    {
        return $this->getField('embed_html');
    }

    public function isEmbeddable(): ?bool
    {
        return $this->getField('embeddable');
    }

    public function getBackdatedTime(): ?DateTime
    {
        return $this->getField('backdated_time');
    }

    public function getEvent(): ?GraphEvent
    {
        return $this->getField('event');
    }

    /**
     * @return ?GraphVideoFormat[]
     */
    public function getFormat(): ?array
    {
        return $this->getField('format');
    }

    public function getLength(): ?float
    {
        return $this->getField('length');
    }

    public function getIcon(): ?string
    {
        return $this->getField('icon');
    }

    public function isCrossPostVideo(): ?bool
    {
        return $this->getField('is_crosspost_video');
    }

    public function isCrossPostEligible(): ?bool
    {
        return $this->getField('is_crossposting_eligible');
    }

    public function isEpisode(): ?bool
    {
        return $this->getField('is_episode');
    }

    public function isInstagramEligible(): ?bool
    {
        return $this->getField('is_instagram_eligible');
    }

    public function isReferenceOnly(): ?bool
    {
        return $this->getField('is_reference_only');
    }

    public function permaLinkUrl(): ?string
    {
        return $this->getField('permalink_url');
    }

    public function getPlace(): ?GraphPlace
    {
        return $this->getField('place');
    }

    public function getPostId(): ?string
    {
        return $this->getField('post_id');
    }

    public function getPostViews(): ?int
    {
        return $this->getField('post_views');
    }
}