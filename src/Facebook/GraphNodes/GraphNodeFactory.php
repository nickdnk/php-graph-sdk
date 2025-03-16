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

use Facebook\FacebookResponse;
use Facebook\Exceptions\FacebookSDKException;

/**
 * Class GraphNodeFactory
 *
 * @package Facebook
 *
 * ## Assumptions ##
 * GraphEdge - is ALWAYS a numeric array
 * GraphEdge - is ALWAYS an array of GraphNode types
 * GraphNode - is ALWAYS an associative array
 * GraphNode - MAY contain GraphNode's "recurrable"
 * GraphNode - MAY contain GraphEdge's "recurrable"
 * GraphNode - MAY contain DateTime's "primitives"
 * GraphNode - MAY contain string's "primitives"
 */
class GraphNodeFactory
{

    /**
     * @var FacebookResponse The response entity from Graph.
     */
    protected FacebookResponse $response;

    /**
     * @var array|null The decoded body of the FacebookResponse entity from Graph.
     */
    protected ?array $decodedBody;

    /**
     * Init this Graph object.
     *
     * @param FacebookResponse $response The response entity from Graph.
     */
    public function __construct(FacebookResponse $response)
    {
        $this->response = $response;
        $this->decodedBody = $response->getDecodedBody();
    }

    /**
     * Tries to convert a FacebookResponse entity into a GraphNode.
     *
     * @param string|null $subclassName The GraphNode sub class to cast to.
     *
     * @throws FacebookSDKException
     */
    public function makeGraphNode(?string $subclassName = null): GraphEdge|GraphNode
    {
        $this->validateResponseAsArray();
        $this->validateResponseCastableAsGraphNode();

        return $this->castAsGraphNodeOrGraphEdge($this->decodedBody, $subclassName);
    }

    /**
     * Tries to convert a FacebookResponse entity into a GraphEdge.
     *
     * @param string|null $subclassName The GraphNode sub class to cast the list items to.
     *
     * @return GraphEdge|GraphNode
     *
     * @throws FacebookSDKException
     */
    public function makeGraphEdge(?string $subclassName = null): GraphEdge|GraphNode
    {
        $this->validateResponseAsArray();
        $this->validateResponseCastableAsGraphEdge();

        return $this->castAsGraphNodeOrGraphEdge($this->decodedBody, $subclassName);
    }

    /**
     * Validates the decoded body.
     *
     * @throws FacebookSDKException
     */
    private function validateResponseAsArray(): void
    {
        if (!is_array($this->decodedBody)) {
            throw new FacebookSDKException('Unable to get response from Graph as array.', 620);
        }
    }

    /**
     * Validates that the return data can be cast as a GraphNode.
     *
     * @throws FacebookSDKException
     */
    public function validateResponseCastableAsGraphNode(): void
    {
        if (isset($this->decodedBody['data']) && static::isCastableAsGraphEdge($this->decodedBody['data'])) {
            throw new FacebookSDKException(
                'Unable to convert response from Graph to a GraphNode because the response looks like a GraphEdge. Try using GraphNodeFactory::makeGraphEdge() instead.',
                620
            );
        }
    }

    /**
     * Validates that the return data can be cast as a GraphEdge.
     *
     * @throws FacebookSDKException
     */
    public function validateResponseCastableAsGraphEdge(): void
    {
        if (!(isset($this->decodedBody['data']) && static::isCastableAsGraphEdge($this->decodedBody['data']))) {
            throw new FacebookSDKException(
                'Unable to convert response from Graph to a GraphEdge because the response does not look like a GraphEdge. Try using GraphNodeFactory::makeGraphNode() instead.',
                620
            );
        }
    }

    /**
     * Safely instantiates a GraphNode of $subclassName.
     *
     * @param array       $data         The array of data to iterate over.
     * @param string|null $subclassName The subclass to cast this collection to.
     *
     * @throws FacebookSDKException
     */
    private function safelyMakeGraphNode(array $data, ?string $subclassName = null): GraphNode
    {
        $subclassName = $subclassName ?: GraphNode::class;
        static::validateSubclass($subclassName);

        // Remember the parent node ID
        $parentNodeId = $data['id'] ?? null;

        $items = [];

        foreach ($data as $k => $v) {
            if (is_array($v)) {
                if (array_is_list($v)) {
                    // If the array is a list of objects, we want to cast each of those objects to the appropriate GraphNode
                    // subclass, but retain the array structure. Since everything is an array when we use associative arrays
                    // when reading the JSON, we need array_is_list to distinguish a JSON array from an object.
                    $children = [];
                    foreach ($v as $cv) {
                        if (is_array($cv)) {
                            $children[] = $this->castAsGraphNodeOrGraphEdge($cv, $subclassName::getObjectMap()[$k] ?? null, $k, $parentNodeId);
                        } else {
                            // Element in array is not an object, i.e. if a property is an array of plain strings.
                            $children[] = $cv;
                        }
                    }
                    $items[$k] = $children;
                } else {
                    // This means it's an object, not an array.
                    $items[$k] = $this->castAsGraphNodeOrGraphEdge($v, $subclassName::getObjectMap()[$k] ?? null, $k, $parentNodeId);
                }
            } else {
                // If it's not an "array" (or JSON object), use the value directly and don't attempt to cast it.
                $items[$k] = $v;
            }
        }

        return new $subclassName($items);
    }

    /**
     * Takes an array of values and determines how to cast each node.
     *
     * @param array       $data         The array of data to iterate over.
     * @param string|null $subclassName The subclass to cast this collection to.
     * @param string|null $parentKey    The key of this data (Graph edge).
     * @param string|null $parentNodeId The parent Graph node ID.
     *
     * @throws FacebookSDKException
     */
    private function castAsGraphNodeOrGraphEdge(array $data, ?string $subclassName = null, ?string $parentKey = null, ?string $parentNodeId = null): GraphNode|GraphEdge
    {
        if (isset($data['data'])) {
            // Create GraphEdge
            if (static::isCastableAsGraphEdge($data['data'])) {
                return $this->safelyMakeGraphEdge($data, $subclassName, $parentKey, $parentNodeId);
            }
            // Sometimes Graph is a weirdo and returns a GraphNode under the "data" key
            $outerData = $data;
            unset($outerData['data']);
            $data = $data['data'] + $outerData;
        }

        // Create GraphNode
        return $this->safelyMakeGraphNode($data, $subclassName);
    }

    /**
     * Return an array of GraphNode's.
     *
     * @param array       $data         The array of data to iterate over.
     * @param string|null $subclassName The GraphNode subclass to cast each item in the list to.
     * @param string|null $parentKey    The key of this data (Graph edge).
     * @param string|null $parentNodeId The parent Graph node ID.
     *
     * @throws FacebookSDKException
     */
    private function safelyMakeGraphEdge(array $data, ?string $subclassName = null, ?string $parentKey = null, ?string $parentNodeId = null): GraphEdge
    {
        if (!isset($data['data'])) {
            throw new FacebookSDKException('Cannot cast data to GraphEdge. Expected a "data" key.', 620);
        }

        $dataList = [];
        foreach ($data['data'] as $graphNode) {
            $dataList[] = $this->safelyMakeGraphNode($graphNode, $subclassName);
        }

        $metaData = $this->getMetaData($data);

        // We'll need to make an edge endpoint for this in case it's a GraphEdge (for cursor pagination)
        $parentGraphEdgeEndpoint = $parentNodeId && $parentKey ? '/' . $parentNodeId . '/' . $parentKey : null;

        return new GraphEdge($this->response->getRequest(), $dataList, $metaData, $parentGraphEdgeEndpoint, $subclassName);
    }

    /**
     * Get the meta data from a list in a Graph response.
     *
     * @param array $data The Graph response.
     *
     * @return array
     */
    public function getMetaData(array $data): array
    {
        unset($data['data']);

        return $data;
    }

    /**
     * Determines whether or not the data should be cast as a GraphEdge.
     */
    public static function isCastableAsGraphEdge(array $data): bool
    {
        if ($data === []) {
            return true;
        }

        // Checks for a sequential numeric array which would be a GraphEdge
        return array_is_list($data);
    }

    /**
     * Ensures that the subclass in question is valid.
     *
     * @param string $subclassName The GraphNode subclass to validate.
     *
     * @throws FacebookSDKException
     */
    public static function validateSubclass(string $subclassName): void
    {

        if (is_a($subclassName, GraphNode::class, true) || is_subclass_of($subclassName, GraphNode::class)) {
            return;
        }

        throw new FacebookSDKException('The given subclass "' . $subclassName . '" is not valid. Cannot cast to an object that is not a GraphNode subclass.', 620);
    }
}
