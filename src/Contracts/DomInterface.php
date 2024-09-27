<?php


namespace PHPHtmlParser\Contracts;

use PHPHtmlParser\Options;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

interface DomInterface
{
    public function loadFromFile(string $file, ?Options $options = null): self;

    public function loadFromUrl(string $url, ?Options $options, ?ClientInterface $client = null, ?RequestInterface $request = null): self;

    public function loadStr(string $str, ?Options $options = null): self;

    public function setOptions(Options $options): self;

    public function find(string $selector, int $nth = null);
}
