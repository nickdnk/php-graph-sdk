<?php

namespace Facebook\GraphNodes;

/**
 * @link https://developers.facebook.com/docs/graph-api/reference/insights-range-value/
 */
class GraphInsightsRangeValue extends GraphNode
{

    public function getLowerBound(): ?string
    {
        return $this->getField('lower_bound');
    }

    public function getUpperBound(): ?string
    {
        return $this->getField('upper_bound');
    }
}