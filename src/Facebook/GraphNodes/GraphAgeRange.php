<?php

namespace Facebook\GraphNodes;

/**
 * @link https://developers.facebook.com/docs/graph-api/reference/age-range/
 */
class GraphAgeRange extends GraphNode
{
    public function getMax(): ?int
    {
        return $this->getField('max');
    }

    public function getMin(): ?int
    {
        return $this->getField('min');
    }
}
