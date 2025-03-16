<?php

namespace Facebook\GraphNodes;

/**
 * @link https://developers.facebook.com/docs/graph-api/reference/application-restriction-info/
 */
class GraphApplicationRestrictions extends GraphNode
{
    public function getAge(): ?string
    {
        return $this->getField('age');
    }

    public function getAgeDistribution(): ?string
    {
        return $this->getField('age_distribution');
    }

    public function getLocation(): ?string
    {
        return $this->getField('location');
    }

    public function getType(): ?string
    {
        return $this->getField('type');
    }
}