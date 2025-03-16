<?php

namespace Facebook\GraphNodes;

/**
 * @link https://developers.facebook.com/docs/graph-api/reference/engagement/
 */
class GraphEngagement extends GraphNode
{

    public function getCount(): ?int
    {
        return $this->getField('count');
    }

    public function getCountString(): ?string
    {
        return $this->getField('count_string');
    }

    public function getCountStringWithLike(): ?string
    {
        return $this->getField('count_string_with_like');
    }

    public function getCountStringWithoutLike(): ?string
    {
        return $this->getField('count_string_without_like');
    }

    public function getSocialSentence(): ?string
    {
        return $this->getField('social_sentence');
    }

    public function getSocialSentenceWithLike(): ?string
    {
        return $this->getField('social_sentence_with_like');
    }

    public function getSocialSentenceWithoutLike(): ?string
    {
        return $this->getField('social_sentence_without_like');
    }

}