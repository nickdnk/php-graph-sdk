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
namespace Facebook\Tests\GraphNodes;

use Facebook\FacebookResponse;
use Facebook\GraphNodes\GraphLocation;
use Facebook\GraphNodes\GraphPage;
use Facebook\GraphNodes\GraphVideo;
use Facebook\Tests\BaseTestCase;
use Mockery as m;
use Facebook\GraphNodes\GraphNodeFactory;

class GraphPageTest extends BaseTestCase
{
    protected FacebookResponse $responseMock;

    protected function setUp(): void
    {
        $this->responseMock = m::mock(FacebookResponse::class);
    }

    public function testPagePropertiesReturnGraphPageObjects()
    {
        $dataFromGraph = [
            'id' => '123',
            'name' => 'Foo Page',
            'best_page' => [
                'id' => '1',
                'name' => 'Bar Page',
            ],
            'featured_video' => [
                'id' => '2',
                'is_reference_only' => true,
            ],
            'contact_address' => [
                'street1' => '123 Fake St',
            ]
        ];

        $this->responseMock
            ->shouldReceive('getDecodedBody')
            ->once()
            ->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);
        /** @var GraphPage $graphNode */
        $graphNode = $factory->makeGraphNode(GraphPage::class);

        $bestPage = $graphNode->getBestPage();
        $video = $graphNode->getFeaturedVideo();

        $this->assertInstanceOf(GraphPage::class, $bestPage);
        $this->assertInstanceOf(GraphVideo::class, $video);
        $this->assertEquals('2', $video->getId());
        $this->assertTrue($video->isReferenceOnly());
    }

    public function testLocationPropertyWillGetCastAsGraphLocationObject()
    {
        $dataFromGraph = [
            'id'       => '123',
            'name'     => 'Foo Page',
            'location' => [
                'city'      => 'Washington',
                'country'   => 'United States',
                'latitude'  => 38.881634205431,
                'longitude' => -77.029121075722,
                'state'     => 'DC',
                'zip'       => '19933',
                'street'    => 'Pennsylvania Avenue 2',
            ],
        ];

        $this->responseMock
            ->shouldReceive('getDecodedBody')
            ->once()
            ->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);
        /** @var GraphPage $graphNode */
        $graphNode = $factory->makeGraphNode(GraphPage::class);

        $location = $graphNode->getLocation();

        $this->assertInstanceOf(GraphLocation::class, $location);
        $this->assertEquals('Washington', $location->getCity());
        $this->assertEquals(38.881634205431, $location->getLatitude());
        $this->assertEquals(-77.029121075722, $location->getLongitude());
        $this->assertEquals('DC', $location->getState());
        $this->assertEquals('United States', $location->getCountry());
        $this->assertEquals('19933', $location->getZip());
        $this->assertEquals('Pennsylvania Avenue 2', $location->getStreet());
    }
}
