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
use Facebook\GraphNodes\GraphCoverPhoto;
use Facebook\GraphNodes\GraphEvent;
use Facebook\GraphNodes\GraphGroup;
use Facebook\GraphNodes\GraphPage;
use Facebook\Tests\BaseTestCase;
use Facebook\GraphNodes\GraphNodeFactory;
use Mockery;

class GraphEventTest extends BaseTestCase
{
    protected FacebookResponse $responseMock;

    protected function setUp(): void
    {
        $this->responseMock = Mockery::mock(FacebookResponse::class);
    }

    public function testCoverGetsCastAsGraphCoverPhoto()
    {
        $dataFromGraph = [
            'cover' => ['id' => '1337']
        ];

        $this->responseMock
            ->shouldReceive('getDecodedBody')
            ->once()
            ->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);
        /** @var GraphEvent $graphObject */
        $graphObject = $factory->makeGraphNode(GraphEvent::class);

        $cover = $graphObject->getCover();
        $this->assertInstanceOf(GraphCoverPhoto::class, $cover);
    }

    public function testPlaceGetsCastAsGraphPage()
    {
        $dataFromGraph = [
            'place' => ['id' => '1337']
        ];

        $this->responseMock
            ->shouldReceive('getDecodedBody')
            ->once()
            ->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);
        /** @var GraphEvent $graphObject */
        $graphObject = $factory->makeGraphNode(GraphEvent::class);

        $place = $graphObject->getPlace();
        $this->assertInstanceOf(GraphPage::class, $place);
    }

    public function testPictureGetsCastAsGraphCover()
    {
        $dataFromGraph = [
            'cover' => ['id' => '1337']
        ];

        $this->responseMock
            ->shouldReceive('getDecodedBody')
            ->once()
            ->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);
        /** @var GraphEvent $graphObject */
        $graphObject = $factory->makeGraphNode(GraphEvent::class);

        $picture = $graphObject->getCover();
        $this->assertInstanceOf(GraphCoverPhoto::class, $picture);
    }

    public function testParentGroupGetsCastAsGraphGroup()
    {
        $dataFromGraph = [
            'parent_group' => ['id' => '1337']
        ];

        $this->responseMock
            ->shouldReceive('getDecodedBody')
            ->once()
            ->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);
        /** @var GraphEvent $graphObject */
        $graphObject = $factory->makeGraphNode(GraphEvent::class);

        $parentGroup = $graphObject->getParentGroup();
        $this->assertInstanceOf(GraphGroup::class, $parentGroup);
    }
}
