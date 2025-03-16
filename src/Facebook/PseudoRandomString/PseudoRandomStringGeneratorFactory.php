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

namespace Facebook\PseudoRandomString;

use InvalidArgumentException;

class PseudoRandomStringGeneratorFactory
{
    private function __construct()
    {
        // a factory constructor should never be invoked
    }

    /**
     * Pseudo random string generator creation.
     *
     * @param string|PseudoRandomStringGeneratorInterface|null $generator The pseudo random string generator must be set to "random_bytes", "openssl", or "urandom", or be an instance of Facebook\PseudoRandomString\PseudoRandomStringGeneratorInterface.
     * @throws InvalidArgumentException
     */
    public static function createPseudoRandomStringGenerator(string|null|PseudoRandomStringGeneratorInterface $generator): PseudoRandomStringGeneratorInterface
    {
        if (!$generator) {
            return self::detectDefaultPseudoRandomStringGenerator();
        }

        if ($generator instanceof PseudoRandomStringGeneratorInterface) {
            return $generator;
        }

        if ('random_bytes' === $generator) {
            return new RandomBytesPseudoRandomStringGenerator();
        }
        if ('openssl' === $generator) {
            return new OpenSslPseudoRandomStringGenerator();
        }
        if ('urandom' === $generator) {
            return new UrandomPseudoRandomStringGenerator();
        }

        throw new InvalidArgumentException('The pseudo random string generator must be set to "random_bytes", "openssl", "urandom" or be an instance of ' . PseudoRandomStringGeneratorInterface::class);
    }

    /**
     * Detects which pseudo-random string generator to use.
     *
     * @return PseudoRandomStringGeneratorInterface
     * @throws InvalidArgumentException If unable to detect a cryptographically secure pseudo-random string generator.
     *
     */
    private static function detectDefaultPseudoRandomStringGenerator(): PseudoRandomStringGeneratorInterface
    {
        // Check for PHP 7's CSPRNG first to keep mcrypt deprecation messages from appearing in PHP 7.1.
        if (function_exists('random_bytes')) {
            return new RandomBytesPseudoRandomStringGenerator();
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            return new OpenSslPseudoRandomStringGenerator();
        }

        if (!ini_get('open_basedir') && is_readable('/dev/urandom')) {
            return new UrandomPseudoRandomStringGenerator();
        }

        throw new InvalidArgumentException('Unable to detect a cryptographically secure pseudo-random string generator.');
    }
}
