<?php
/**
 * This code is licensed under the MIT License.
 *
 * Copyright (c) 2018 Appwilio (http://appwilio.com), greabock (https://github.com/greabock), JhaoDa (https://github.com/jhaoda)
 * Copyright (c) 2018 Alexey Kopytko <alexey@kopytko.com> and contributors
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

declare(strict_types=1);

namespace CdekSDK\Responses;

use CdekSDK\Contracts\Response;
use CdekSDK\Responses\Types\Message;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class ErrorResponse implements Response, ResponseInterface
{
    /** @var ResponseInterface */
    private $response;

    private function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public static function withHTTPResponse(ResponseInterface $response): self
    {
        return new self($response);
    }

    public function hasErrors(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessages()
    {
        yield new Message($this->response->getReasonPhrase(), (string) $this->response->getStatusCode());
    }

    public function jsonSerialize()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress MixedArgument
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        return $this->response->withStatus($code, $reasonPhrase);
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress MixedArgument
     */
    public function hasHeader($name)
    {
        return $this->response->hasHeader($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->response->getHeaders();
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->response->getBody();
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress MixedArgument
     */
    public function withProtocolVersion($version)
    {
        return $this->response->withProtocolVersion($version);
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress MixedArgument
     */
    public function withoutHeader($name)
    {
        return $this->response->withoutHeader($name);
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress MixedArgument
     */
    public function getHeaderLine($name)
    {
        return $this->response->getHeaderLine($name);
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress MixedArgument
     */
    public function withHeader($name, $value)
    {
        return $this->response->withHeader($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function withBody(StreamInterface $body)
    {
        return $this->response->withBody($body);
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase()
    {
        return $this->response->getReasonPhrase();
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress MixedArgument
     */
    public function getHeader($name)
    {
        return $this->response->getHeader($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {
        return $this->response->getProtocolVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress MixedArgument
     */
    public function withAddedHeader($name, $value)
    {
        return $this->response->withAddedHeader($name, $value);
    }
}
