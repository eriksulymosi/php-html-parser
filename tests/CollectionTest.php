<?php


use PHPHtmlParser\Dom\Node\Collection;
use PHPHtmlParser\Dom\Node\HtmlNode;
use PHPHtmlParser\Dom\Tag;
use PHPHtmlParser\Exceptions\EmptyCollectionException;
use PHPHtmlParser\Selector\Parser;
use PHPHtmlParser\Selector\Selector;

test('each', function (): void {
    $root = new HtmlNode(new Tag('root'));
    $parent = new HtmlNode(new Tag('div'));
    $child1 = new HtmlNode(new Tag('a'));
    $child2 = new HtmlNode(new Tag('p'));
    $child3 = new HtmlNode(new Tag('a'));
    $root->addChild($parent);
    $parent->addChild($child1);
    $parent->addChild($child2);

    $child2->addChild($child3);

    $selector = new Selector('a', new Parser());
    $collection = $selector->find($root);
    $count = 0;
    $collection->each(function ($node) use (&$count): void {
        ++$count;
    });
    expect($count)->toEqual(2);
});

test('call no nodes', function (): void {
    $collection = new Collection();
    $collection->innerHtml();
})->throws(EmptyCollectionException::class);

test('no node string', function (): void {
    $collection = new Collection();
    $string = (string) $collection;
    expect($string)->toBeEmpty();
});

test('call magic', function (): void {
    $root = new HtmlNode(new Tag('root'));
    $parent = new HtmlNode(new Tag('div'));
    $child1 = new HtmlNode(new Tag('a'));
    $child2 = new HtmlNode(new Tag('p'));
    $child3 = new HtmlNode(new Tag('a'));
    $root->addChild($parent);
    $parent->addChild($child1);
    $parent->addChild($child2);

    $child2->addChild($child3);

    $selector = new Selector('div * a', new Parser());
    expect($selector->find($root)->id())->toEqual($child3->id());
});

test('get magic', function (): void {
    $root = new HtmlNode(new Tag('root'));
    $parent = new HtmlNode(new Tag('div'));
    $child1 = new HtmlNode(new Tag('a'));
    $child2 = new HtmlNode(new Tag('p'));
    $child3 = new HtmlNode(new Tag('a'));
    $root->addChild($parent);
    $parent->addChild($child1);
    $parent->addChild($child2);

    $child2->addChild($child3);

    $selector = new Selector('div * a', new Parser());
    expect($selector->find($root)->innerHtml)->toEqual($child3->innerHtml);
});

test('get no nodes', function (): void {
    $collection = new Collection();
    $collection->innerHtml;
})->throws(EmptyCollectionException::class);

test('to string magic', function (): void {
    $root = new HtmlNode(new Tag('root'));
    $parent = new HtmlNode(new Tag('div'));
    $child1 = new HtmlNode(new Tag('a'));
    $child2 = new HtmlNode(new Tag('p'));
    $child3 = new HtmlNode(new Tag('a'));
    $root->addChild($parent);
    $parent->addChild($child1);
    $parent->addChild($child2);

    $child2->addChild($child3);

    $selector = new Selector('div * a', new Parser());
    expect((string) $selector->find($root))->toEqual((string) $child3);
});

test('to array', function (): void {
    $root = new HtmlNode(new Tag('root'));
    $parent = new HtmlNode(new Tag('div'));
    $child1 = new HtmlNode(new Tag('a'));
    $child2 = new HtmlNode(new Tag('p'));
    $child3 = new HtmlNode(new Tag('a'));
    $root->addChild($parent);
    $parent->addChild($child1);
    $parent->addChild($child2);

    $child2->addChild($child3);

    $selector = new Selector('a', new Parser());
    $collection = $selector->find($root);
    $array = $collection->toArray();
    $lastA = \end($array);
    expect($lastA->id())->toEqual($child3->id());
});

test('get iterator', function (): void {
    $collection = new Collection();
    $iterator = $collection->getIterator();
    expect($iterator instanceof ArrayIterator)->toBeTrue();
});

test('offset set', function (): void {
    $collection = new Collection();
    $collection->offsetSet(7, true);

    expect($collection->offsetGet(7))->toBeTrue();
});

test('offset unset', function (): void {
    $collection = new Collection();
    $collection->offsetSet(7, true);
    $collection->offsetUnset(7);

    expect(\is_null($collection->offsetGet(7)))->toBeTrue();
});
