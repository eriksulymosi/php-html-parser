<?php


use PHPHtmlParser\Dom;
use PHPHtmlParser\Options;

test('load closing tag on self closing no slash', function (): void {
    $dom = new Dom();
    $dom->setOptions((new Options())->addNoSlashTag('br'));

    $dom->loadStr('<div class="all"><br><p>Hey bro, <a href="google.com" data-quote="\"">click here</a></br></div>');

    expect($dom->find('div', 0)->innerHtml)->toEqual('<br><p>Hey bro, <a href="google.com" data-quote="\"">click here</a></p>');
});

test('load closing tag on self closing remove no slash', function (): void {
    $dom = new Dom();
    $dom->setOptions(
        (new Options())
            ->addNoSlashTag('br')
            ->removeNoSlashTag('br')
    );

    $dom->loadStr('<div class="all"><br><p>Hey bro, <a href="google.com" data-quote="\"">click here</a></br></div>');

    expect($dom->find('div', 0)->innerHtml)->toEqual('<br /><p>Hey bro, <a href="google.com" data-quote="\"">click here</a></p>');
});

test('load closing tag on self closing clear no slash', function (): void {
    $dom = new Dom();
    $dom->setOptions(
        (new Options())
            ->addNoSlashTag('br')
            ->clearNoSlashTags()
    );

    $dom->loadStr('<div class="all"><br><p>Hey bro, <a href="google.com" data-quote="\"">click here</a></br></div>');

    expect($dom->find('div', 0)->innerHtml)->toEqual('<br /><p>Hey bro, <a href="google.com" data-quote="\"">click here</a></p>');
});
