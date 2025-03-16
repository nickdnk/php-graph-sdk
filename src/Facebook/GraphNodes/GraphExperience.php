<?php

namespace Facebook\GraphNodes;

/**
 * @link https://developers.facebook.com/docs/graph-api/reference/experience/
 */
class GraphExperience extends GraphNode
{

    protected static array $graphObjectMap = [
        'with' => GraphUser::class
    ];

    public function getId(): ?string
    {
        return $this->getField('id');
    }

    public function getDescription(): ?string
    {
        return $this->getField('description');
    }

    public function getName(): ?string
    {
        return $this->getField('name');
    }

    public function getWith(): ?GraphUser
    {
        return $this->getField('with');
    }
}