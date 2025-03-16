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

use DateTime;
use Facebook\FacebookResponse;
use Facebook\GraphNodes\GraphAlbum;
use Facebook\GraphNodes\GraphLocation;
use Facebook\GraphNodes\GraphPlace;
use Facebook\GraphNodes\GraphUser;
use Facebook\Tests\BaseTestCase;
use Mockery as m;
use Facebook\GraphNodes\GraphNodeFactory;

class GraphAlbumTest extends BaseTestCase
{

    protected FacebookResponse $responseMock;

    protected function setUp(): void
    {
        $this->responseMock = m::mock(FacebookResponse::class);
    }

    public function testDatesGetCastToDateTime()
    {
        $dataFromGraph = [
            'created_time' => '2014-07-15T03:54:34+0000',
            'updated_time' => '2014-07-12T01:24:09+0000',
            'id' => '123',
            'name' => 'Bar',
        ];

        $this->responseMock
            ->shouldReceive('getDecodedBody')
            ->once()
            ->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);
        /** @var GraphAlbum $graphNode */
        $graphNode = $factory->makeGraphNode(GraphAlbum::class);

        $createdTime = $graphNode->getCreatedTime();
        $updatedTime = $graphNode->getUpdatedTime();

        $this->assertInstanceOf(DateTime::class, $createdTime);
        $this->assertInstanceOf(DateTime::class, $updatedTime);
    }

    public function testFromGetsCastAsGraphUser()
    {
        $dataFromGraph = [
            'id' => '123',
            'from' => [
                'id' => '1337',
                'name' => 'Foo McBar',
            ],
        ];

        $this->responseMock
            ->shouldReceive('getDecodedBody')
            ->once()
            ->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);
        /** @var GraphAlbum $graphNode */
        $graphNode = $factory->makeGraphNode(GraphAlbum::class);

        $from = $graphNode->getFrom();

        $this->assertInstanceOf(GraphUser::class, $from);
    }

    public function testPlacePropertyWillGetCastAsGraphPlaceObject()
    {
        $dataFromGraph = [
            'id' => '123',
            'name' => 'Foo Album',
            'place' => [
                'id' => '1',
                'name' => 'For Bar Place',
                'overall_rating' => 4.5,
                'location' => [
                    'street' => 'Foo Street',
                    'city' => 'Bar City',
                    'state' => 'CA',
                    'country' => 'US',
                    'zip' => '90210',
                    'latitude' => 49.55,
                    'longitude' => -34.444
                ]
            ]
        ];

        $this->responseMock
            ->shouldReceive('getDecodedBody')
            ->once()
            ->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);
        /** @var GraphAlbum $graphNode */
        $graphNode = $factory->makeGraphNode(GraphAlbum::class);

        $place = $graphNode->getPlace();
        $location = $place->getLocation();

        $this->assertInstanceOf(GraphPlace::class, $place);
        $this->assertInstanceOf(GraphLocation::class, $place->getLocation());

        $this->assertEquals('For Bar Place', $place->getName());
        $this->assertEquals(4.5, $place->getOverallRating());

        $this->assertEquals('Foo Street', $location->getStreet());
        $this->assertEquals('Bar City', $location->getCity());
        $this->assertEquals('CA', $location->getState());
        $this->assertEquals('90210', $location->getZip());
        $this->assertEquals(49.55, $location->getLatitude());
        $this->assertEquals(-34.444, $location->getLongitude());
        $this->assertEquals('US', $location->getCountry());

    }
}
