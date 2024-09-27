<?php


namespace PHPHtmlParser\DTO;

use PHPHtmlParser\Dom\Node\HtmlNode;

final class TagDTO
{
    public function __construct(
        public readonly bool $status = false,
        public readonly bool $closing = false,
        public readonly ?HtmlNode $node = null,
        public readonly ?string $tag = null
    ) {}
}
