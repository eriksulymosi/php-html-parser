<?php


use PHPHtmlParser\Dom;
use PHPHtmlParser\Options;

test('load closing tag add self closing tag', function (): void {
    $dom = new Dom();
    $dom->setOptions((new Options())->addSelfClosingTag('mytag'));
    $dom->loadStr('<div class="all"><mytag><p>Hey bro, <a href="google.com" data-quote="\"">click here</a></mytag></div>');

    expect($dom->find('div', 0)->innerHtml)->toEqual('<mytag /><p>Hey bro, <a href="google.com" data-quote="\"">click here</a></p>');
});

test('load closing tag add self closing tag array', function (): void {
    $dom = new Dom();
    $dom->setOptions((new Options())->addSelfClosingTags([
        'mytag',
        'othertag',
    ]));
    $dom->loadStr('<div class="all"><mytag><p>Hey bro, <a href="google.com" data-quote="\"">click here</a><othertag></div>');

    expect($dom->find('div', 0)->innerHtml)->toEqual('<mytag /><p>Hey bro, <a href="google.com" data-quote="\"">click here</a><othertag /></p>');
});

test('load closing tag remove self closing tag', function (): void {
    $dom = new Dom();
    $dom->setOptions((new Options())->removeSelfClosingTag('br'));
    $dom->loadStr('<div class="all"><br><p>Hey bro, <a href="google.com" data-quote="\"">click here</a></br></div>');

    expect($dom->find('div', 0)->innerHtml)->toEqual('<br><p>Hey bro, <a href="google.com" data-quote="\"">click here</a></p></br>');
});

test('load closing tag clear self closing tag', function (): void {
    $dom = new Dom();
    $dom->setOptions((new Options())->clearSelfClosingTags());
    $dom->loadStr('<div class="all"><br><p>Hey bro, <a href="google.com" data-quote="\"">click here</a></br></div>');

    expect($dom->find('div', 0)->innerHtml)->toEqual('<br><p>Hey bro, <a href="google.com" data-quote="\"">click here</a></p></br>');
});
