<?php


namespace PHPHtmlParser\Selector;

use PHPHtmlParser\Contracts\Selector\ParserInterface;
use PHPHtmlParser\DTO\Selector\ParsedSelectorCollectionDTO;
use PHPHtmlParser\DTO\Selector\ParsedSelectorDTO;
use PHPHtmlParser\DTO\Selector\RuleDTO;

/**
 * This is the default parser for the selector.
 */
class Parser implements ParserInterface
{
    /**
     * Pattern of CSS selectors, modified from 'mootools'.
     */
    private string $pattern = "/([\w\-:\*>]*)(?:\#([\w\-]+)|\.([\w\.\-]+))?(?:\[@?(!?[\w\-:]+)(?:([!*^$]?=)[\"']?(.*?)[\"']?)?\])?([\/, ]+)/is";

    /**
     * Parses the selector string.
     */
    public function parseSelectorString(string $selector): ParsedSelectorCollectionDTO
    {
        $selectors = [];
        $matches = [];
        $rules = [];
        \preg_match_all($this->pattern, \trim($selector) . ' ', $matches, PREG_SET_ORDER);

        // skip tbody
        foreach ($matches as $match) {
            // default values
            $tag = \mb_strtolower(\trim($match[1]));
            $operator = '=';
            $key = null;
            $value = null;
            $noKey = false;
            $alterNext = false;

            // check for elements that alter the behavior of the next element
            if ($tag === '>') {
                $alterNext = true;
            }

            // check for id selector
            if (isset($match[2]) && ($match[2] !== '' && $match[2] !== '0')) {
                $key = 'id';
                $value = $match[2];
            }

            // check for class selector
            if (isset($match[3]) && ($match[3] !== '' && $match[3] !== '0')) {
                $key = 'class';
                $value = \explode('.', $match[3]);
            }

            // and final attribute selector
            if (isset($match[4]) && ($match[4] !== '' && $match[4] !== '0')) {
                $key = \mb_strtolower($match[4]);
            }

            if (isset($match[5]) && ($match[5] !== '' && $match[5] !== '0')) {
                $operator = $match[5];
            }

            if (isset($match[6]) && ($match[6] !== '' && $match[6] !== '0')) {
                $value = $match[6];
                if (str_contains($value, '][')) {
                    // we have multiple type selectors
                    $keys = [];
                    $keys[] = $key;
                    $key = $keys;
                    $parts = \explode('][', $value);
                    $value = [];
                    foreach ($parts as $part) {
                        if (str_contains($part, '=')) {
                            [$first, $second] = \explode('=', $part);
                            $key[] = $first;
                            $value[] = $second;
                        } else {
                            $value[] = $part;
                        }
                    }
                }
            }

            // check for elements that do not have a specified attribute
            if (\is_string($key) && isset($key[0]) && $key[0] === '!') {
                $key = \substr($key, 1);
                $noKey = true;
            }

            $rules[] = RuleDTO::makeFromPrimitives(
                $tag,
                $operator,
                $key,
                $value,
                $noKey,
                $alterNext
            );
            if (isset($match[7]) && \is_string($match[7]) && \trim($match[7]) === ',') {
                $selectors[] = ParsedSelectorDTO::makeFromRules($rules);
                $rules = [];
            }
        }

        // save last results
        if ($rules !== []) {
            $selectors[] = ParsedSelectorDTO::makeFromRules($rules);
        }

        return ParsedSelectorCollectionDTO::makeCollection($selectors);
    }
}
