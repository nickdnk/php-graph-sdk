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
use Exception;

/**
 * Class GraphNode
 *
 * @package Facebook
 */
class GraphNode extends Collection
{
    /**
     * @var array Maps object key names to Graph object types.
     */
    protected static array $graphObjectMap = [];

    /**
     * Init this Graph object.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        parent::__construct($this->castItems($data));
    }

    /**
     * Iterates over an array and detects the types each node
     * should be cast to and returns all the items as an array.
     *
     * @TODO Add auto-casting to AccessToken entities.
     *
     * @param array $data The array to iterate over.
     *
     * @return array
     */
    public function castItems(array $data): array
    {
        $items = [];

        foreach ($data as $k => $v) {
            if ($this->shouldCastAsDateTime($k)
            ) {
                try {
                    $items[$k] = $this->castToDateTime($v);
                } catch (Exception) {
                    // If it cannot be parsed as a date but should be one, we cannot add it because of type checking.
                }
            } elseif ($k === 'birthday') {
                $items[$k] = $this->castToBirthday($v);
            } else {
                $items[$k] = $v;
            }
        }

        return $items;
    }

    /**
     * Uncasts any auto-casted datatypes.
     * Basically the reverse of castItems().
     *
     * @return array
     */
    public function uncastItems(): array
    {
        $items = $this->asArray();

        return array_map(function ($v) {
            if ($v instanceof DateTime) {
                return $v->format(DateTime::ISO8601);
            }

            return $v;
        }, $items);
    }

    /**
     * Get the collection of items as JSON.
     */
    public function asJson(int $options = 0): string
    {
        return json_encode($this->uncastItems(), $options);
    }

    /**
     * Determines if a value from Graph should be cast to DateTime.
     *
     * @param string $key
     *
     * @return boolean
     */
    public function shouldCastAsDateTime(string $key): bool
    {
        return in_array($key, [
            'created_time',
            'updated_time',
            'start_time',
            'end_time',
            'backdated_time',
            'issued_at',
            'expires_at',
            'publish_time',
            'joined'
        ], true);
    }

    /**
     * Casts a date value from Graph to DateTime.
     * On PHP 8.3+, this is DateMalformedStringException, so we catch everything.
     * @throws Exception
     */
    private function castToDateTime(int|string $value): DateTime
    {
        if (is_int($value)) {
            $dt = new DateTime();
            $dt->setTimestamp($value);
        } else {
            $dt = new DateTime($value);
        }

        return $dt;
    }

    /**
     * Casts a birthday value from Graph to Birthday
     */
    private function castToBirthday(string $value): Birthday
    {
        return new Birthday($value);
    }

    /**
     * Getter for $graphObjectMap.
     *
     * @return array
     */
    public static function getObjectMap(): array
    {
        return static::$graphObjectMap;
    }
}
