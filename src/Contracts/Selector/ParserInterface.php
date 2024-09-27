<?php


namespace PHPHtmlParser\Contracts\Selector;

use PHPHtmlParser\DTO\Selector\ParsedSelectorCollectionDTO;

interface ParserInterface
{
    public function parseSelectorString(string $selector): ParsedSelectorCollectionDTO;
}
