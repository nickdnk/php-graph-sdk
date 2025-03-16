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
namespace Facebook\Tests\PersistentData;

use Facebook\PersistentData\FacebookMemoryPersistentDataHandler;
use Facebook\PersistentData\FacebookSessionPersistentDataHandler;
use Facebook\PersistentData\PersistentDataFactory;
use Facebook\PersistentData\PersistentDataInterface;
use Facebook\Tests\BaseTestCase;

class PersistentDataFactoryTest extends BaseTestCase
{

    /**
     * @dataProvider persistentDataHandlerProviders
     */
    public function testCreatePersistentDataHandler(mixed $handler, string $expected)
    {
        $persistentDataHandler = PersistentDataFactory::createPersistentDataHandler($handler);

        $this->assertInstanceOf(PersistentDataInterface::class, $persistentDataHandler);
        $this->assertInstanceOf($expected, $persistentDataHandler);
    }

    public function persistentDataHandlerProviders(): array
    {
        $handlers = [
            ['memory', FacebookMemoryPersistentDataHandler::class],
            [new FacebookMemoryPersistentDataHandler(), FacebookMemoryPersistentDataHandler::class],
            [new FacebookSessionPersistentDataHandler(false), FacebookSessionPersistentDataHandler::class],
            [null, PersistentDataInterface::class],
        ];

        if (session_status() === PHP_SESSION_ACTIVE) {
            $handlers[] = ['session', FacebookSessionPersistentDataHandler::class];
        }

        return $handlers;
    }
}
