<?php

namespace Facebook\GraphNodes;

/**
 * @link https://developers.facebook.com/docs/graph-api/reference/platform-image-source/
 */
class GraphPlatformImageSource extends GraphNode
{

    public function getHeight(): ?int
    {
        return $this->getField('height');
    }

    public function getWidth(): ?int
    {
        return $this->getField('width');
    }

    public function getSource(): ?string
    {
        return $this->getField('source');
    }

}