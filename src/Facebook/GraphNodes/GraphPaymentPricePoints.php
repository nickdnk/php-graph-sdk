<?php

namespace Facebook\GraphNodes;

/**
 * @link https://developers.facebook.com/docs/graph-api/reference/payment-pricepoints/
 */
class GraphPaymentPricePoints extends GraphNode
{

    protected static array $graphObjectMap = [
        'mobile' => GraphPaymentPricePoint::class
    ];

    /**
     * @return ?GraphPaymentPricePoint[]
     */
    public function getMobile(): ?array
    {
        return $this->getField('mobile');
    }
}