<?php


use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\StrictException;
use PHPHtmlParser\Options;

test('config strict', function (): void {
    $dom = new Dom();
    $dom->setOptions((new Options())->setStrict(true));
    $dom->loadStr('<div><p id="hey">Hey you</p> <p id="ya">Ya you!</p></div>');

    expect($dom->getElementById('hey')->nextSibling()->text)->toEqual(' ');
});

test('config strict missing self closing', function (): void {
    $dom = new Dom();
    $dom->setOptions((new Options())->setStrict(true));
    $dom->loadStr('<div><p id="hey">Hey you</p><br><p id="ya">Ya you!</p></div>');
})->throws(StrictException::class, "Tag 'br' is not self closing! (character #31)");

test('config strict missing attribute', function (): void {
    $dom = new Dom();
    $dom->setOptions((new Options())->setStrict(true));
    $dom->loadStr('<div><p id="hey" block>Hey you</p> <p id="ya">Ya you!</p></div>');
})->throws(StrictException::class, "Tag 'p' has an attribute 'block' with out a value! (character #22)");

test('config strict brtag', function (): void {
    $dom = new Dom();
    $dom->setOptions((new Options())->setStrict(true));
    $dom->loadStr('<br />');

    expect(true)->toBeTrue();
});
