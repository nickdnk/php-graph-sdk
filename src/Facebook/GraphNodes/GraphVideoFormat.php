<?php

namespace Facebook\GraphNodes;

/**
 * @link https://developers.facebook.com/docs/graph-api/reference/video-format/
 */
class GraphVideoFormat extends GraphNode
{
    public function getEmbedHtml(): ?string
    {
        return $this->getField('embed_html');
    }

    public function getFilter(): ?string
    {
        return $this->getField('filter');
    }

    public function getHeight(): ?int
    {
        return $this->getField('height');
    }

    public function getWidth(): ?int
    {
        return $this->getField('width');
    }

    public function getPicture(): ?string
    {
        return $this->getField('picture');
    }
}