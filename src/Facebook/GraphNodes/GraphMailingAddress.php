<?php

namespace Facebook\GraphNodes;

/**
 * @link https://developers.facebook.com/docs/graph-api/reference/mailing-address/
 */
class GraphMailingAddress extends GraphNode
{

    protected static array $graphObjectMap = [
        'city_page' => GraphPage::class
    ];

    public function getId(): ?string
    {
        return $this->getField('id');
    }

    public function getCity(): ?string
    {
        return $this->getField('city');
    }

    public function getCountry(): ?string
    {
        return $this->getField('country');
    }

    public function getPostalCode(): ?string
    {
        return $this->getField('postal_code');
    }

    public function getRegion(): ?string
    {
        return $this->getField('region');
    }

    public function getStreet1(): ?string
    {
        return $this->getField('street1');
    }

    public function getStreet2(): ?string
    {
        return $this->getField('street2');
    }

    public function getCityPage(): ?GraphPage
    {
        return $this->getField('city_page');
    }

}