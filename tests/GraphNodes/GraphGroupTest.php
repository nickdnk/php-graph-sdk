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
use Facebook\GraphNodes\GraphGroup;
use Facebook\Tests\BaseTestCase;
use Mockery as m;
use Facebook\GraphNodes\GraphNodeFactory;

class GraphGroupTest extends BaseTestCase
{

    protected FacebookResponse $responseMock;

    protected function setUp(): void
    {
        $this->responseMock = m::mock(FacebookResponse::class);
    }

    public function testCoverGetsCastAsGraphCoverPhoto()
    {
        $dataFromGraph = [
            'id'                   => '12848493',
            'icon'                 => 'https://foo.bar.icon',
            'email'                => 'foo@bar.com',
            'description'          => 'foo description',
            'cover'                => [
                'id'       => '1337',
                'source'   => 'https://foo.bar',
                'offset_x' => 24,
                'offset_y' => 35
            ],
            'member_request_count' => 403,
            'member_count'         => 2844,
        ];

        $this->responseMock
            ->shouldReceive('getDecodedBody')
            ->once()
            ->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);
        /** @var GraphGroup $graphNode */
        $graphNode = $factory->makeGraphNode(GraphGroup::class);

        $cover = $graphNode->getCover();
        $this->assertInstanceOf(GraphCoverPhoto::class, $cover);
        $this->assertEquals('12848493', $graphNode->getId());
        $this->assertEquals(403, $graphNode->getMemberRequestCount());
        $this->assertEquals(2844, $graphNode->getMemberCount());
        $this->assertEquals('foo@bar.com', $graphNode->getEmail());
        $this->assertEquals('https://foo.bar.icon', $graphNode->getIcon());
        $this->assertEquals('foo description', $graphNode->getDescription());

        $this->assertEquals('1337', $cover->getId());
        $this->assertEquals(24, $cover->getOffsetX());
        $this->assertEquals(35, $cover->getOffsetY());
        $this->assertEquals('https://foo.bar', $cover->getSource());
    }

}
