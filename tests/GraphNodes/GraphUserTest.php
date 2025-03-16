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
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\GraphNodes\Birthday;
use Facebook\GraphNodes\GraphAgeRange;
use Facebook\GraphNodes\GraphAlbum;
use Facebook\GraphNodes\GraphEdge;
use Facebook\GraphNodes\GraphEvent;
use Facebook\GraphNodes\GraphExperience;
use Facebook\GraphNodes\GraphPage;
use Facebook\GraphNodes\GraphPaymentPricePoints;
use Facebook\GraphNodes\GraphPermission;
use Facebook\GraphNodes\GraphPhoto;
use Facebook\GraphNodes\GraphPicture;
use Facebook\GraphNodes\GraphPlace;
use Facebook\GraphNodes\GraphPlatformImageSource;
use Facebook\GraphNodes\GraphUser;
use Facebook\GraphNodes\GraphVideoUploadLimits;
use Facebook\Tests\BaseTestCase;
use Mockery as m;
use Facebook\GraphNodes\GraphNodeFactory;

class GraphUserTest extends BaseTestCase
{

    protected FacebookResponse $responseMock;

    protected function setUp(): void
    {
        $this->responseMock = m::mock(FacebookResponse::class);
    }

    public function testDatesGetCastToDateTime()
    {
        $dataFromGraph = [
            'updated_time' => '2016-04-26 13:22:05',
        ];

        $this->responseMock
            ->shouldReceive('getDecodedBody')
            ->once()
            ->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);
        /** @var GraphUser $graphNode */
        $graphNode = $factory->makeGraphNode(GraphUser::class);

        $updatedTime = $graphNode->getField('updated_time');

        $this->assertInstanceOf(DateTime::class, $updatedTime);
    }

    public function testBirthdaysGetCastToBirthday()
    {
        $dataFromGraph = [
            'birthday' => '1984/01/01',
        ];

        $this->responseMock
            ->shouldReceive('getDecodedBody')
            ->once()
            ->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);
        /** @var GraphUser $graphNode */
        $graphNode = $factory->makeGraphNode(GraphUser::class);

        $birthday = $graphNode->getBirthday();

        // Test to ensure BC
        $this->assertInstanceOf(DateTime::class, $birthday);

        $this->assertInstanceOf(Birthday::class, $birthday);
        $this->assertTrue($birthday->hasDate());
        $this->assertTrue($birthday->hasYear());
        $this->assertEquals('1984/01/01', $birthday->format('Y/m/d'));
    }

    public function testBirthdayCastHandlesDateWithoutYear()
    {
        $dataFromGraph = [
            'birthday' => '03/21',
        ];

        $this->responseMock
            ->shouldReceive('getDecodedBody')
            ->once()
            ->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);
        /** @var GraphUser $graphNode */
        $graphNode = $factory->makeGraphNode(GraphUser::class);

        $birthday = $graphNode->getBirthday();

        $this->assertTrue($birthday->hasDate());
        $this->assertFalse($birthday->hasYear());
        $this->assertEquals('03/21', $birthday->format('m/d'));
    }

    public function testBirthdayCastHandlesYearWithoutDate()
    {
        $dataFromGraph = [
            'birthday' => '1984',
        ];

        $this->responseMock
            ->shouldReceive('getDecodedBody')
            ->once()
            ->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);
        /** @var GraphUser $graphNode */
        $graphNode = $factory->makeGraphNode(GraphUser::class);

        $birthday = $graphNode->getBirthday();

        $this->assertTrue($birthday->hasYear());
        $this->assertFalse($birthday->hasDate());
        $this->assertEquals('1984', $birthday->format('Y'));
    }

    public function testPagePropertiesWillGetCastAsGraphPageObjects()
    {
        $dataFromGraph = [
            'id' => '123',
            'name' => 'Foo User',
            'hometown' => [
                'id' => '1',
                'name' => 'Foo Place',
            ],
            'location' => [
                'id' => '2',
                'name' => 'Bar Place',
            ],
        ];

        $this->responseMock
            ->shouldReceive('getDecodedBody')
            ->once()
            ->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);
        /** @var GraphUser $graphNode */
        $graphNode = $factory->makeGraphNode(GraphUser::class);

        $hometown = $graphNode->getHometown();
        $location = $graphNode->getLocation();

        $this->assertInstanceOf(GraphPage::class, $hometown);
        $this->assertInstanceOf(GraphPage::class, $location);
    }

    public function testUserPropertiesWillGetCastAsGraphUserObjects()
    {
        $dataFromGraph = [
            'id'                   => '123',
            'name'                 => 'Foo User',
            'short_name'           => 'Foo B.',
            'age_range'            => [
                'min' => 20,
                'max' => 30
            ],
            'significant_other'    => [
                'id'   => '1337',
                'name' => 'Bar User',
            ],
            'video_upload_limits'  => [
                'size'   => 4,
                'length' => 5
            ],
            'token_for_business'   => 'token test',
            'favorite_teams'       => [
                [
                    'id'          => 'team id',
                    'description' => 'team description',
                    'name'        => 'team name',
                    'with'        => [
                        'id'   => 'with_user_id',
                        'name' => 'With Name'
                    ]
                ]
            ],
            'favorite_athletes'    => [
                [
                    'id' => 'athlete id'
                ]
            ],
            'languages'            => [
                [
                    'id'   => 'en',
                    'name' => 'English'
                ]
            ],
            'inspirational_people' => [
                [
                    'id' => '28424'
                ]
            ],
            'hometown' => [
                'id' => '454',
                'name' => 'New York'
            ],
            'payment_pricepoints' => [
                'mobile' => [
                    [
                        'credits' => 4545.34,
                        'currency' => 'USD',
                        'user_price' => '4,545.34 USD'
                    ]
                ]
            ]
        ];

        $this->responseMock
            ->shouldReceive('getDecodedBody')
            ->once()
            ->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);
        /** @var GraphUser $graphNode */
        $graphNode = $factory->makeGraphNode(GraphUser::class);

        $this->assertInstanceOf(GraphUser::class, $graphNode->getSignificantOther());

        $this->assertInstanceOf(GraphAgeRange::class, $graphNode->getAgeRange());
        $this->assertEquals(20, $graphNode->getAgeRange()->getMin());
        $this->assertEquals(30, $graphNode->getAgeRange()->getMax());

        $this->assertInstanceOf(GraphVideoUploadLimits::class, $graphNode->getVideoUploadLimits());
        $this->assertEquals(4, $graphNode->getVideoUploadLimits()->getSize());
        $this->assertEquals(5, $graphNode->getVideoUploadLimits()->getLength());

        $this->assertIsArray($graphNode->getFavoriteTeams());
        $this->assertIsArray($graphNode->getFavoriteAthletes());
        $this->assertIsArray($graphNode->getLanguages());
        $this->assertIsArray($graphNode->getInspirationalPeople());

        $this->assertInstanceOf(GraphExperience::class, $graphNode->getFavoriteTeams()[0]);
        $this->assertEquals('team description', $graphNode->getFavoriteTeams()[0]->getDescription());

        $this->assertInstanceOf(GraphPage::class, $graphNode->getHometown());
        $this->assertEquals('New York', $graphNode->getHometown()->getName());
        $this->assertEquals('454', $graphNode->getHometown()->getId());

        $this->assertInstanceOf(GraphPaymentPricePoints::class, $graphNode->getPaymentPricePoints());
        $this->assertIsArray($graphNode->getPaymentPricePoints()->getMobile());
        $this->assertEquals('4,545.34 USD', $graphNode->getPaymentPricePoints()->getMobile()[0]->getUserPrice());
        $this->assertEquals('USD', $graphNode->getPaymentPricePoints()->getMobile()[0]->getCurrency());
        $this->assertEquals(4545.34, $graphNode->getPaymentPricePoints()->getMobile()[0]->getCredits());

        $this->assertInstanceOf(GraphExperience::class, $graphNode->getFavoriteAthletes()[0]);
        $this->assertInstanceOf(GraphExperience::class, $graphNode->getLanguages()[0]);
        $this->assertInstanceOf(GraphExperience::class, $graphNode->getInspirationalPeople()[0]);

        $this->assertInstanceOf(GraphUser::class, $graphNode->getFavoriteTeams()[0]->getWith());

        $this->assertEquals('Foo B.', $graphNode->getShortName());
        $this->assertEquals('token test', $graphNode->getTokenForBusiness());
    }

    public function testPicturePropertiesWillGetCastAsGraphPictureObjects()
    {
        $dataFromGraph = [
            'id' => '123',
            'name' => 'Foo User',
            'picture' => [
                'is_silhouette' => true,
                'url' => 'http://foo.bar',
                'width' => 200,
                'height' => 200,
            ],
        ];

        $this->responseMock
            ->shouldReceive('getDecodedBody')
            ->once()
            ->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);
        /** @var GraphUser $graphNode */
        $graphNode = $factory->makeGraphNode(GraphUser::class);

        $Picture = $graphNode->getPicture();

        $this->assertInstanceOf(GraphPicture::class, $Picture);
        $this->assertTrue($Picture->isSilhouette());
        $this->assertEquals(200, $Picture->getWidth());
        $this->assertEquals(200, $Picture->getHeight());
        $this->assertEquals('http://foo.bar', $Picture->getUrl());
    }

    public function testPhotoPropertiesWillGetCastAsGraphPhotoObjects()
    {
        $dataFromGraph = [
            'id' => '123',
            'album' => [
                'id' => '844',
                'name' => 'album name'
            ],
            'alt_text' => 'alt text',
            'alt_text_custom' => 'alt text custom',
            'backdated_time' => 1742481204,
            'can_backdate' => true,
            'can_delete' => true,
            'can_tag' => true,
            'created_time' =>1742481234,
            'event' => [
                'id' => '3843',
                'name' => 'event name'
            ],
            'height' => 884,
            'width' => 483,
            'icon' => 'icon string',
            'link' => 'link string',
            'name' => 'photo name',
            'page_story_id' => '3848232',
            'place' => [
                'id' => '28523',
                'name' => 'some place name'
            ],
            'updated_time' => 1742481303,
            'webp_images' => [
                [
                    'height' => 422,
                    'width' => 433,
                    'source' => 'webp source'
                ]
            ],
            'images' => [
                [
                    'height' => 555,
                    'width' => 666,
                    'source' => 'image source'
                ]
            ]
        ];

        $this->responseMock
            ->shouldReceive('getDecodedBody')
            ->once()
            ->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);
        /** @var GraphPhoto $graphNode */
        $graphNode = $factory->makeGraphNode(GraphPhoto::class);

        $event = $graphNode->getEvent();
        $place = $graphNode->getPlace();
        $album = $graphNode->getAlbum();

        $this->assertInstanceOf(GraphEvent::class, $event);
        $this->assertEquals('3843', $event->getId());

        $this->assertInstanceOf(GraphPlace::class, $place);
        $this->assertEquals('some place name', $place->getName());

        $this->assertInstanceOf(GraphAlbum::class, $album);
        $this->assertEquals('album name', $album->getName());

        $this->assertEquals('alt text', $graphNode->getAltText());
        $this->assertEquals('alt text custom', $graphNode->getAltTextCustom());
        $this->assertEquals('3848232', $graphNode->getPageStoryId());
        $this->assertEquals('link string', $graphNode->getLink());
        $this->assertEquals('icon string', $graphNode->getIcon());

        $this->assertInstanceOf(DateTime::class, $graphNode->getUpdatedTime());
        $this->assertInstanceOf(DateTime::class, $graphNode->getCreatedTime());
        $this->assertInstanceOf(DateTime::class, $graphNode->getBackDatedTime());

        $this->assertIsArray($graphNode->getWebPImages());
        $this->assertIsArray($graphNode->getImages());

        $this->assertInstanceOf(GraphPlatformImageSource::class, $graphNode->getWebPImages()[0]);
        $this->assertInstanceOf(GraphPlatformImageSource::class, $graphNode->getImages()[0]);

        $this->assertEquals(422, $graphNode->getWebPImages()[0]->getHeight());
        $this->assertEquals(433, $graphNode->getWebPImages()[0]->getWidth());
        $this->assertEquals('webp source', $graphNode->getWebPImages()[0]->getSource());

    }

    public function testGraphNodeUserArraysAndListsRecursively()
    {

        $json = <<<JSON
{
  "id": "2848493",
  "name": "Foo Bar Name",
  "albums": {
    "data": [
      {
        "id": "2348957405",
        "name": "Profile pictures",
        "cover_photo": {
          "album": {
            "created_time": "2010-09-02T01:16:18+0000",
            "name": "Profile pictures",
            "id": "2348957405"
          },
          "id": "128583948"
        }
      }
    ],
    "paging": {
      "cursors": {
        "before": "MTM5ODk0NDk4NTA3",
        "after": "MTAxNTA1NzE3ODY3ODM1MDgZD"
      }
    }
  },
  "sports": [
    {
      "id": "258529396944",
      "description": "some description",
      "name": "SomeName"
    },
    {
      "id": "258529396946",
      "description": "some description 2",
      "name": "SomeName 2"
    }
  ],
  "string_array": ["foo", "bar", "baz"],
  "permissions": {
    "data": [
      {
        "permission": "user_likes",
        "status": "granted"
      },
      {
        "permission": "user_photos",
        "status": "granted"
      },
      {
        "permission": "public_profile",
        "status": "granted"
      }
    ]
  }
}
JSON;

        $factory = new GraphNodeFactory(new FacebookResponse(new FacebookRequest(), $json, 200));

        /** @var GraphUser $graphNode */
        $graphNode = $factory->makeGraphNode(GraphUser::class);

        $this->assertInstanceOf(GraphUser::class, $graphNode);
        $this->assertIsArray($graphNode->getSports());

        $this->assertCount(2, $graphNode->getSports());
        $this->assertInstanceOf(GraphExperience::class, $graphNode->getSports()[0]);
        $this->assertEquals('some description', $graphNode->getSports()[0]->getDescription());
        $this->assertEquals('some description 2', $graphNode->getSports()[1]->getDescription());

        $this->assertIsArray($graphNode->getField('string_array'));
        $this->assertCount(3, $graphNode->getField('string_array'));
        $this->assertEquals('foo', $graphNode->getField('string_array')[0]);
        $this->assertEquals('bar', $graphNode->getField('string_array')[1]);

        $this->assertInstanceOf(GraphEdge::class, $graphNode->getPermissions());
        $this->assertInstanceOf(GraphPermission::class, $graphNode->getPermissions()->all()[0]);

        $this->assertInstanceOf(GraphEdge::class, $graphNode->getAlbums());

        $album = $graphNode->getAlbums()->all()[0];
        $this->assertInstanceOf(GraphAlbum::class, $album);
        $this->assertInstanceOf(GraphPhoto::class, $album->getCoverPhoto());
        $this->assertInstanceOf(GraphAlbum::class, $album->getCoverPhoto()->getAlbum());

        $this->assertEquals($album->getCoverPhoto()->getAlbum()->getId(), $album->getId());

    }
}
