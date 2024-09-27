<?php


use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\NotLoadedException;

test('not loaded', function (): void {
    (new Dom())->find('div', 0);
})->throws(NotLoadedException::class);
