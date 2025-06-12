<?php

namespace Facebook\GraphNodes;

class GraphApplicationEventsConfig extends GraphNode
{

    public function getDefaultAteStatus(): ?int
    {
        return $this->getField('default_ate_status');
    }

    public function isAdvertiserIdCollectionEnabled(): ?bool
    {
        return $this->getField('advertiser_id_collection_enabled');
    }

    public function isEventCollectionEnabled(): ?bool
    {
        return $this->getField('event_collection_enabled');
    }
}