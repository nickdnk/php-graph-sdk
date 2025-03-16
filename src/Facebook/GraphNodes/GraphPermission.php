<?php

namespace Facebook\GraphNodes;

/**
 * @link https://developers.facebook.com/docs/graph-api/reference/permission/
 */
class GraphPermission extends GraphNode
{

    public function getPermission(): string
    {
        return $this->getField('permission');
    }

    public function getStatus(): string
    {
        return $this->getField('status');
    }
}