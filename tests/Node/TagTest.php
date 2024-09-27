<?php


use PHPHtmlParser\Dom\Tag;
use PHPHtmlParser\Exceptions\Tag\AttributeNotFoundException;

test('self closing', function (): void {
    $tag = new Tag('a');
    $tag->selfClosing();

    expect($tag->isSelfClosing())->toBeTrue();
});

test('set attributes', function (): void {
    $attr = [
        'href' => [
            'value'       => 'http://google.com',
            'doubleQuote' => false,
        ],
    ];

    $tag = new Tag('a');
    $tag->setAttributes($attr);

    expect($tag->getAttribute('href')->value)->toEqual('http://google.com');
});

test('remove attribute', function (): void {
    $tag = new Tag('a');
    $tag->setAttribute('href', 'http://google.com');
    $tag->removeAttribute('href');
    $tag->getAttribute('href');
})->throws(AttributeNotFoundException::class);

test('remove all attributes', function (): void {
    $tag = new Tag('a');
    $tag->setAttribute('href', 'http://google.com');
    $tag->setAttribute('class', 'clear-fix', true);
    $tag->removeAllAttributes();

    expect(\count($tag->getAttributes()))->toEqual(0);
});

test('set attribute no array', function (): void {
    $tag = new Tag('a');
    $tag->setAttribute('href', 'http://google.com');

    expect($tag->getAttribute('href')->value)->toEqual('http://google.com');
});

test('set attributes no double array', function (): void {
    $attr = [
        'href'  => 'http://google.com',
        'class' => 'funtimes',
    ];

    $tag = new Tag('a');
    $tag->setAttributes($attr);

    expect($tag->getAttribute('class')->value)->toEqual('funtimes');
});

test('update attributes', function (): void {
    $tag = new Tag('a');
    $tag->setAttributes([
        'href' => [
            'value'       => 'http://google.com',
            'doubleQuote' => false,
        ],
        'class' => [
            'value'       => null,
            'doubleQuote' => true,
        ],
    ]);

    expect($tag->getAttribute('class')->value)->toEqual(null);
    expect($tag->getAttribute('href')->value)->toEqual('http://google.com');

    $attr = [
        'href'  => 'https://www.google.com',
        'class' => 'funtimes',
    ];

    $tag->setAttributes($attr);
    expect($tag->getAttribute('class')->value)->toEqual('funtimes');
    expect($tag->getAttribute('href')->value)->toEqual('https://www.google.com');
});

test('noise', function (): void {
    $tag = new Tag('a');
    expect($tag->noise('noise') instanceof Tag)->toBeTrue();
});

test('get attribute magic', function (): void {
    $attr = [
        'href' => [
            'value'       => 'http://google.com',
            'doubleQuote' => false,
        ],
    ];

    $tag = new Tag('a');
    $tag->setAttributes($attr);

    expect($tag->getAttribute('href')->value)->toEqual('http://google.com');
});

test('set attribute magic', function (): void {
    $tag = new Tag('a');
    $tag->setAttribute('href', 'http://google.com');

    expect($tag->getAttribute('href')->value)->toEqual('http://google.com');
});

test('make opening tag', function (): void {
    $attr = [
        'href' => [
            'value'       => 'http://google.com',
            'doubleQuote' => true,
        ],
    ];

    $tag = new Tag('a');
    $tag->setAttributes($attr);

    expect($tag->makeOpeningTag())->toEqual('<a href="http://google.com">');
});

test('make opening tag empty attr', function (): void {
    $attr = [
        'href' => [
            'value'       => 'http://google.com',
            'doubleQuote' => true,
        ],
    ];

    $tag = new Tag('a');
    $tag->setAttributes($attr);
    $tag->setAttribute('selected', null);

    expect($tag->makeOpeningTag())->toEqual('<a href="http://google.com" selected>');
});

test('make opening tag self closing', function (): void {
    $attr = [
        'class' => [
            'value'       => 'clear-fix',
            'doubleQuote' => true,
        ],
    ];

    $tag = (new Tag('div'))
        ->selfClosing()
        ->setAttributes($attr);
    expect($tag->makeOpeningTag())->toEqual('<div class="clear-fix" />');
});

test('make closing tag', function (): void {
    $tag = new Tag('a');
    expect($tag->makeClosingTag())->toEqual('</a>');
});

test('make closing tag self closing', function (): void {
    $tag = new Tag('div');
    $tag->selfClosing();

    expect($tag->makeClosingTag())->toBeEmpty();
});

test('set tag attribute', function (): void {
    $tag = new Tag('div');
    $tag->setStyleAttributeValue('display', 'none');

    expect($tag->getAttribute('style')->value)->toEqual('display:none;');
});

test('get style attributes array', function (): void {
    $tag = new Tag('div');
    $tag->setStyleAttributeValue('display', 'none');
    
    expect($tag->getStyleAttributeArray())->toBeArray();
});
