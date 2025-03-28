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
use Facebook\GraphNodes\GraphSessionInfo;
use Facebook\Tests\BaseTestCase;
use Mockery as m;
use Facebook\GraphNodes\GraphNodeFactory;

class GraphSessionInfoTest extends BaseTestCase
{

    protected FacebookResponse $responseMock;

    protected function setUp(): void
    {
        $this->responseMock = m::mock(FacebookResponse::class);
    }

    public function testDatesGetCastToDateTime()
    {
        $dataFromGraph = [
            'expires_at' => 123,
            'issued_at' => 1337,
        ];

        $this->responseMock
            ->shouldReceive('getDecodedBody')
            ->once()
            ->andReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock);

        /** @var GraphSessionInfo $graphNode */
        $graphNode = $factory->makeGraphNode(GraphSessionInfo::class);

        $expires = $graphNode->getExpiresAt();
        $issuedAt = $graphNode->getIssuedAt();

        $this->assertInstanceOf(DateTime::class, $expires);
        $this->assertInstanceOf(DateTime::class, $issuedAt);
    }
}
