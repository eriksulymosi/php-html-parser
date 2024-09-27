<?php

use PHPHtmlParser\Dom;

beforeEach(function (): void {
    $dom = new Dom();
    $dom->loadStr('<div class="all"><br><p>Hey bro, <a href="google.com" id="78" data-quote="\"">click here</a></br></div><br class="both" />');
    $this->dom = $dom;
});

afterEach(function (): void {
    Mockery::close();
});

test('load escape quotes', function (): void {
    $a = $this->dom->find('a', 0);
    expect($a->outerHtml)->toEqual('<a href="google.com" id="78" data-quote="\"">click here</a>');
});

test('load no closing tag', function (): void {
    $p = $this->dom->find('p', 0);
    expect($p->innerHtml)->toEqual('Hey bro, <a href="google.com" id="78" data-quote="\"">click here</a>');
});

test('load closing tag on self closing', function (): void {
    expect($this->dom->find('br'))->toHaveCount(2);
});

test('incorrect access', function (): void {
    $div = $this->dom->find('div', 0);
    expect($div->foo)->toEqual(null);
});

test('load attribute on self closing', function (): void {
    $br = $this->dom->find('br', 1);
    expect($br->getAttribute('class'))->toEqual('both');
});

test('to string magic', function (): void {
    expect((string) $this->dom)->toEqual('<div class="all"><br /><p>Hey bro, <a href="google.com" id="78" data-quote="\"">click here</a></p></div><br class="both" />');
});

test('get magic', function (): void {
    expect($this->dom->innerHtml)->toEqual('<div class="all"><br /><p>Hey bro, <a href="google.com" id="78" data-quote="\"">click here</a></p></div><br class="both" />');
});

test('first child', function (): void {
    expect($this->dom->firstChild()->outerHtml)->toEqual('<div class="all"><br /><p>Hey bro, <a href="google.com" id="78" data-quote="\"">click here</a></p></div>');
});

test('last child', function (): void {
    expect($this->dom->lastChild()->outerHtml)->toEqual('<br class="both" />');
});

test('get element by id', function (): void {
    expect($this->dom->getElementById('78')->outerHtml)->toEqual('<a href="google.com" id="78" data-quote="\"">click here</a>');
});

test('get elements by tag', function (): void {
    expect($this->dom->getElementsByTag('p')[0]->outerHtml)->toEqual('<p>Hey bro, <a href="google.com" id="78" data-quote="\"">click here</a></p>');
});

test('get elements by class', function (): void {
    expect($this->dom->getElementsByClass('all')[0]->innerHtml)->toEqual('<br /><p>Hey bro, <a href="google.com" id="78" data-quote="\"">click here</a></p>');
});

test('delete node', function (): void {
    $a = $this->dom->find('a')[0];
    $a->delete();
    unset($a);

    expect((string) $this->dom)->toEqual('<div class="all"><br /><p>Hey bro, </p></div><br class="both" />');
});
