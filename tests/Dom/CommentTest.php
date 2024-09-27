<?php

use PHPHtmlParser\Dom;
use PHPHtmlParser\Options;

test('load comment inner html', function (): void {
    $dom = (new Dom())
        ->setOptions((new Options())->setCleanupInput(false))
        ->loadStr('<!-- test comment with number 2 -->');

    expect($dom->innerHtml)->toEqual('<!-- test comment with number 2 -->');
});
