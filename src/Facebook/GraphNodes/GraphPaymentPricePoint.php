<?php

namespace Facebook\GraphNodes;

/**
 * @link https://developers.facebook.com/docs/graph-api/reference/payment-pricepoint/
 */
class GraphPaymentPricePoint extends GraphNode
{
    public function getCredits(): ?float
    {
        return $this->getField('credits');
    }

    public function getCurrency(): ?string
    {
        return $this->getField('currency');
    }

    public function getUserPrice(): ?string
    {
        return $this->getField('user_price');
    }
}