<?php
/**
 * Copyright 2017 Facebook, Inc.
 *
 * You are hereby granted a non-exclusive, worldwide, royalty-free license to
 * use, copy, modify, and distribute this software in source code or binary
 * form for use in connection with the web services and APIs provided by
 * Facebook.
 *
 * As with any software that integrates with the Facebook platform, your use
 * of this software is subject to the Facebook Developer Principles and
 * Policies [http://developers.facebook.com/policy/]. This copyright notice
 * shall be included in all copies or substantial portions of the software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 */
namespace Facebook\GraphNodes;

/**
 * Class GraphLocation
 *
 * @package Facebook
 * @link https://developers.facebook.com/docs/graph-api/reference/location/
 */
class GraphLocation extends GraphNode
{

    public function getStreet(): ?string
    {
        return $this->getField('street');
    }

    public function getCity(): ?string
    {
        return $this->getField('city');
    }

    public function getState(): ?string
    {
        return $this->getField('state');
    }

    public function getCountry(): ?string
    {
        return $this->getField('country');
    }

    public function getZip(): ?string
    {
        return $this->getField('zip');
    }

    public function getLatitude(): ?float
    {
        return $this->getField('latitude');
    }

    public function getLongitude(): ?float
    {
        return $this->getField('longitude');
    }
}
