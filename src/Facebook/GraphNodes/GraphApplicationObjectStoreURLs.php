<?php

namespace Facebook\GraphNodes;

/**
 * @link https://developers.facebook.com/docs/graph-api/reference/application-object-store-urls/
 */
class GraphApplicationObjectStoreURLs extends GraphNode
{

    public function getAmazonAppStore(): ?string
    {
        return $this->getField('amazon_app_store');
    }

    public function getFBCanvas(): ?string
    {
        return $this->getField('fb_canvas');
    }

    public function getFBGameRoom(): ?string
    {
        return $this->getField('fb_gameroom');
    }

    public function getGooglePlay(): ?string
    {
        return $this->getField('google_play');
    }

    public function getInstantGame(): ?string
    {
        return $this->getField('instant_game');
    }

    public function getItunes(): ?string
    {
        return $this->getField('itunes');
    }

    public function getItunesIpad(): ?string
    {
        return $this->getField('itunes_ipad');
    }

    public function getWindows10Store(): ?string
    {
        return $this->getField('windows_10_store');
    }
}
