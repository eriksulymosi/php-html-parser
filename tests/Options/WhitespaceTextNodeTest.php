<?php


use PHPHtmlParser\Dom;
use PHPHtmlParser\Options;

test('config global no whitespace text node', function (): void {
    $dom = new Dom();
    $dom->setOptions((new Options())->setWhitespaceTextNode(false));
    $dom->loadStr('<div><p id="hey">Hey you</p> <p id="ya">Ya you!</p></div>');

    expect($dom->getElementById('hey')->nextSibling()->text)->toEqual('Ya you!');
});

test('config local override', function (): void {
    $dom = new Dom();
    $dom->setOptions((new Options())->setWhitespaceTextNode(false));
    $dom->loadStr('<div><p id="hey">Hey you</p> <p id="ya">Ya you!</p></div>', (new Options())->setWhitespaceTextNode(true));

    expect($dom->getElementById('hey')->nextSibling()->text)->toEqual(' ');
});
