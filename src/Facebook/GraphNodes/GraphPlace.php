<?php

namespace Facebook\GraphNodes;

/**
 * @link https://developers.facebook.com/docs/graph-api/reference/place
 */
class GraphPlace extends GraphNode
{

    protected static array $graphObjectMap = [
        'location' => GraphLocation::class
    ];

    public function getId(): ?string
    {
        return $this->getField('id');
    }

    public function getLocation(): ?GraphLocation
    {
        return $this->getField('location');
    }

    public function getName(): ?string
    {
        return $this->getField('name');
    }

    public function getOverallRating(): ?float
    {
        return $this->getField('overall_rating');
    }
}