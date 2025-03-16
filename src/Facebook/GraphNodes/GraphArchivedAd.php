<?php

namespace Facebook\GraphNodes;


use DateTime;

/**
 * @link https://developers.facebook.com/docs/marketing-api/reference/archived-ad/
 */
class GraphArchivedAd extends GraphNode
{

    protected static array $graphObjectMap = [
        'estimated_audience_size' => GraphInsightsRangeValue::class,
        'impressions'             => GraphInsightsRangeValue::class,
        'spend'                   => GraphInsightsRangeValue::class,
    ];

    public function getId(): ?string
    {
        return $this->getField('id');
    }

    public function getAdCreationTime(): ?DateTime
    {
        return $this->getField('ad_creation_time');
    }

    /**
     * @return ?string[]
     */
    public function getAdCreativeBodies(): ?array
    {
        return $this->getField('ad_creative_bodies');
    }

    /**
     * @return ?string[]
     */
    public function getAdCreativeLinkCaptions(): ?array
    {
        return $this->getField('ad_creative_link_captions');
    }

    /**
     * @return ?string[]
     */
    public function getAdCreativeLinkDescriptions(): ?array
    {
        return $this->getField('ad_creative_link_descriptions');
    }

    /**
     * @return ?string[]
     */
    public function getAdCreativeLinkTitles(): ?array
    {
        return $this->getField('ad_creative_link_titles');
    }

    public function getAdDeliveryStartTime(): ?DateTime
    {
        return $this->getField('ad_delivery_start_time');
    }

    public function getAdDeliveryStopTime(): ?DateTime
    {
        return $this->getField('ad_delivery_stop_time');
    }

    public function getAdSnapshotUrl(): ?string
    {
        return $this->getField('ad_snapshot_url');
    }

    public function getBrTotalReach(): ?int
    {
        return $this->getField('br_total_reach');
    }

    public function getBylines(): ?string
    {
        return $this->getField('bylines');
    }

    public function getCurrency(): ?string
    {
        return $this->getField('currency');
    }

    public function getEstimatedAudienceSize(): ?GraphInsightsRangeValue
    {
        return $this->getField('estimated_audience_size');
    }

    public function getEUTotalReach(): ?int
    {
        return $this->getField('eu_total_reach');
    }

    public function getImpressions(): ?GraphInsightsRangeValue
    {
        return $this->getField('impressions');
    }

    /**
     * @return ?string[]
     */
    public function getLanguages(): ?array
    {
        return $this->getField('languages');
    }

    public function getPageId(): ?string
    {
        return $this->getField('page_id');
    }

    public function getPageName(): ?string
    {
        return $this->getField('page_name');
    }

    /**
     * @return ?string[]
     */
    public function getPublisherPlatforms(): ?array
    {
        return $this->getField('publisher_platforms');
    }

    public function getSpend(): ?GraphInsightsRangeValue
    {
        return $this->getField('spend');
    }

    /**
     * @return ?string[]
     */
    public function getTargetAges(): ?array
    {
        return $this->getField('target_ages');
    }

    /**
     * @return ?string[]
     */
    public function getTargetGenders(): ?array
    {
        return $this->getField('target_genders');
    }

}