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

namespace Facebook\Tests\Fixtures;

use Facebook\Http\GraphRawResponse;
use Facebook\HttpClients\FacebookHttpClientInterface;

class FakeGraphApiForResumableUpload implements FacebookHttpClientInterface
{
    public int $transferCount = 0;
    private string $respondWith = 'SUCCESS';

    public function failOnStart(): void
    {
        $this->respondWith = 'FAIL_ON_START';
    }

    public function failOnTransfer(): void
    {
        $this->respondWith = 'FAIL_ON_TRANSFER';
    }

    public function failOnTransferAndUploadNewChunk(): void
    {
        $this->respondWith = 'FAIL_ON_TRANSFER_AND_UPLOAD_NEW_CHUNK';
    }

    public function send(string $url, string $method, ?string $body, array $headers, int $timeOut): GraphRawResponse
    {
        // Could be start, transfer or finish
        if (str_contains($body, 'transfer')) {
            return $this->respondTransfer();
        } elseif (str_contains($body, 'finish')) {
            return $this->respondFinish();
        }

        return $this->respondStart();
    }

    private function respondStart(): GraphRawResponse
    {
        if ($this->respondWith == 'FAIL_ON_START') {
            return new GraphRawResponse(
                "HTTP/1.1 500 OK\r\nFoo: Bar",
                '{"error":{"message":"Error validating access token: Session has expired on Monday, ' .
                '10-Aug-15 01:00:00 PDT. The current time is Monday, 10-Aug-15 01:14:23 PDT.",' .
                '"type":"OAuthException","code":190,"error_subcode":463}}'
            );
        }

        return new GraphRawResponse(
            "HTTP/1.1 200 OK\r\nFoo: Bar",
            '{"video_id":"1337","start_offset":"0","end_offset":"20","upload_session_id":"42"}'
        );
    }

    private function respondTransfer(): GraphRawResponse
    {
        if ($this->respondWith == 'FAIL_ON_TRANSFER') {
            return new GraphRawResponse(
                "HTTP/1.1 500 OK\r\nFoo: Bar",
                '{"error":{"message":"There was a problem uploading your video. Please try uploading it again.",' .
                '"type":"FacebookApiException","code":6000,"error_subcode":1363019}}'
            );
        }

        if ($this->respondWith == 'FAIL_ON_TRANSFER_AND_UPLOAD_NEW_CHUNK') {
            return new GraphRawResponse(
                "HTTP/1.1 500 OK\r\nFoo: Bar",
                '{"error":{"message":"There was a problem uploading your video. Please try uploading it again.",' .
                '"type":"OAuthException","code":6001,"error_subcode":1363037,' .
                '"error_data":{"start_offset":40,"end_offset":50}}}'
            );
        }

        switch ($this->transferCount) {
            case 0:
                $data = ['start_offset' => 20, 'end_offset' => 40];
                break;
            case 1:
                $data = ['start_offset' => 40, 'end_offset' => 50];
                break;
            default:
                $data = ['start_offset' => 50, 'end_offset' => 50];
                break;
        }

        $this->transferCount++;

        return new GraphRawResponse(
            "HTTP/1.1 200 OK\r\nFoo: Bar",
            json_encode($data)
        );
    }

    private function respondFinish(): GraphRawResponse
    {
        return new GraphRawResponse(
            "HTTP/1.1 200 OK\r\nFoo: Bar",
            '{"success":true}'
        );
    }
}
