<?php


use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\Node\TextNode;

test('text', function (): void {
    $node = new TextNode('foo bar');
    expect($node->text())->toEqual('foo bar');
});

test('get tag', function (): void {
    $node = new TextNode('foo bar');
    expect($node->getTag()->name())->toEqual('text');
});

test('ancestor by tag', function (): void {
    $node = new TextNode('foo bar');
    $text = $node->ancestorByTag('text');
    expect($text)->toEqual($node);
});

test('preserve entity', function (): void {
    $node = new TextNode('&#x69;');
    $text = $node->outerhtml;
    expect($text)->toEqual('&#x69;');
});

test('is text node', function (): void {
    $node = new TextNode('text');
    expect($node->isTextNode())->toEqual(true);
});

test('text in text node', function (): void {
    $node = new TextNode('foo bar');
    expect($node->outerHtml())->toEqual('foo bar');
});

test('set text to text node', function (): void {
    $node = new TextNode('');
    $node->setText('foo bar');

    expect($node->innerHtml())->toEqual('foo bar');
});

test('set text', function (): void {
    $dom = new Dom();
    $dom->loadStr('<div class="all"><p>Hey bro, <a href="google.com">click here</a><br /> :)</p></div>');

    $a = $dom->find('a')[0];
    $a->firstChild()->setText('biz baz');
    expect((string) $dom)->toEqual('<div class="all"><p>Hey bro, <a href="google.com">biz baz</a><br /> :)</p></div>');
});
