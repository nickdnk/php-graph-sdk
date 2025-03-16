<?php

namespace Facebook\GraphNodes;

/**
 * @link https://developers.facebook.com/docs/graph-api/reference/video-upload-limits/
 */
class GraphVideoUploadLimits extends GraphNode
{

    public function getLength(): ?int
    {
        return $this->getField('length');
    }

    public function getSize(): ?int
    {
        return $this->getField('size');
    }
}