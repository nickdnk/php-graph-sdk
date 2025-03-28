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

use DateTime;

/**
 * Birthday object to handle various Graph return formats
 *
 * @package Facebook
 */
class Birthday extends DateTime
{

    private bool $hasDate, $hasYear;

    /**
     * Parses Graph birthday format to set indication flags, possible values:
     *
     *  MM/DD/YYYY
     *  MM/DD
     *  YYYY
     *
     * @link https://developers.facebook.com/docs/graph-api/reference/user
     */
    public function __construct(string $date)
    {
        $parts = explode('/', $date);
        $count = count($parts);

        $this->hasYear = $count === 3 || $count === 1;
        $this->hasDate = $count === 3 || $count === 2;

        parent::__construct($date);
    }

    /**
     * Returns whether date object contains birth day and month
     */
    public function hasDate(): bool
    {
        return $this->hasDate;
    }

    /**
     * Returns whether date object contains birth year
     */
    public function hasYear(): bool
    {
        return $this->hasYear;
    }
}
