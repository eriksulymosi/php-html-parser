<?php


use PHPHtmlParser\Dom\Node\HtmlNode;
use PHPHtmlParser\Dom\Tag;
use PHPHtmlParser\Selector\Parser;
use PHPHtmlParser\Selector\Selector;

test('parse selector string id', function (): void {
    $selector = new Selector('#all', new Parser());
    $selectors = $selector->getParsedSelectorCollectionDTO();
    expect($selectors->getParsedSelectorDTO()[0]->getRules()[0]->getKey())->toEqual('id');
});

test('parse selector string class', function (): void {
    $selector = new Selector('div.post', new Parser());
    $selectors = $selector->getParsedSelectorCollectionDTO();
    expect($selectors->getParsedSelectorDTO()[0]->getRules()[0]->getKey())->toEqual('class');
});

test('parse selector string attribute', function (): void {
    $selector = new Selector('div[visible=yes]', new Parser());
    $selectors = $selector->getParsedSelectorCollectionDTO();
    expect($selectors->getParsedSelectorDTO()[0]->getRules()[0]->getValue())->toEqual('yes');
});

test('parse selector string no key', function (): void {
    $selector = new Selector('div[!visible]', new Parser());
    $selectors = $selector->getParsedSelectorCollectionDTO();
    expect($selectors->getParsedSelectorDTO()[0]->getRules()[0]->isNoKey())->toBeTrue();
});

test('find', function (): void {
    $root = new HtmlNode('root');
    $parent = new HtmlNode('div');
    $child1 = new HtmlNode('a');
    $child2 = new HtmlNode('p');
    $parent->addChild($child1);
    $parent->addChild($child2);

    $root->addChild($parent);

    $selector = new Selector('div a', new Parser());
    expect($selector->find($root)[0]->id())->toEqual($child1->id());
});

test('find id', function (): void {
    $parent = new HtmlNode(new Tag('div'));
    $child1 = new HtmlNode(new Tag('a'));
    $child2 = new HtmlNode(new Tag('p'));
    $child2->getTag()->setAttributes([
        'id' => [
            'value'       => 'content',
            'doubleQuote' => true,
        ],
    ]);
    $parent->addChild($child1);
    $parent->addChild($child2);

    $selector = new Selector('#content', new Parser());
    expect($selector->find($parent)[0]->id())->toEqual($child2->id());
});

test('find class', function (): void {
    $parent = new HtmlNode(new Tag('div'));
    $child1 = new HtmlNode(new Tag('a'));
    $child2 = new HtmlNode(new Tag('p'));
    $child3 = new HtmlNode('a');
    $child3->getTag()->setAttributes([
        'class' => [
            'value'       => 'link',
            'doubleQuote' => true,
        ],
    ]);
    $parent->addChild($child1);
    $parent->addChild($child2);
    $parent->addChild($child3);

    $selector = new Selector('.link', new Parser());
    expect($selector->find($parent)[0]->id())->toEqual($child3->id());
});

test('find class multiple', function (): void {
    $parent = new HtmlNode(new Tag('div'));
    $child1 = new HtmlNode(new Tag('a'));
    $child2 = new HtmlNode(new Tag('p'));
    $child3 = new HtmlNode(new Tag('a'));
    $child3->getTag()->setAttributes([
        'class' => [
            'value'       => 'link outer',
            'doubleQuote' => false,
        ],
    ]);
    $parent->addChild($child1);
    $parent->addChild($child2);
    $parent->addChild($child3);

    $selector = new Selector('.outer', new Parser());
    expect($selector->find($parent)[0]->id())->toEqual($child3->id());
});

test('find wild', function (): void {
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
    expect($selector->find($root)[0]->id())->toEqual($child3->id());
});

test('find multiple selectors', function (): void {
    $root = new HtmlNode(new Tag('root'));
    $parent = new HtmlNode(new Tag('div'));
    $child1 = new HtmlNode(new Tag('a'));
    $child2 = new HtmlNode(new Tag('p'));
    $child3 = new HtmlNode(new Tag('a'));
    $root->addChild($parent);
    $parent->addChild($child1);
    $parent->addChild($child2);

    $child2->addChild($child3);

    $selector = new Selector('a, p', new Parser());
    expect(\count($selector->find($root)))->toEqual(3);
});

test('find xpath key selector', function (): void {
    $parent = new HtmlNode(new Tag('div'));
    $child1 = new HtmlNode(new Tag('a'));
    $child2 = new HtmlNode(new Tag('p'));
    $child3 = new HtmlNode(new Tag('a'));
    $child3->getTag()->setAttributes([
        'class' => [
            'value'       => 'link outer',
            'doubleQuote' => false,
        ],
    ]);
    $parent->addChild($child1);
    $parent->addChild($child2);
    $parent->addChild($child3);

    $selector = new Selector('div[1]', new Parser());
    expect($selector->find($parent)[0]->id())->toEqual($parent->id());
});

test('find child multiple levels deep', function (): void {
    $root = new HtmlNode(new Tag('root'));
    $parent = new HtmlNode(new Tag('div'));
    $child1 = new HtmlNode(new Tag('ul'));
    $child2 = new HtmlNode(new Tag('li'));
    $root->addChild($parent);
    $parent->addChild($child1);
    $child1->addChild($child2);

    $selector = new Selector('div li', new Parser());
    expect(\count($selector->find($root)))->toEqual(1);
});

test('find all children', function (): void {
    $root = new HtmlNode(new Tag('root'));
    $parent = new HtmlNode(new Tag('div'));
    $child1 = new HtmlNode(new Tag('ul'));
    $child2 = new HtmlNode(new Tag('span'));
    $child3 = new HtmlNode(new Tag('ul'));
    $root->addChild($parent);
    $parent->addChild($child1);
    $child2->addChild($child3);
    $parent->addChild($child2);

    $selector = new Selector('div ul', new Parser());
    expect(\count($selector->find($root)))->toEqual(2);
});

test('find child using child selector', function (): void {
    $root = new HtmlNode(new Tag('root'));
    $parent = new HtmlNode(new Tag('div'));
    $child1 = new HtmlNode(new Tag('ul'));
    $child2 = new HtmlNode(new Tag('span'));
    $child3 = new HtmlNode(new Tag('ul'));
    $root->addChild($parent);
    $parent->addChild($child1);
    $child2->addChild($child3);
    $parent->addChild($child2);

    $selector = new Selector('div > ul', new Parser());
    expect(\count($selector->find($root)))->toEqual(1);
});

test('find node by attribute only', function (): void {
    $root = new HtmlNode(new Tag('root'));
    $child1 = new HtmlNode(new Tag('ul'));
    $child1->setAttribute('custom-attr', null);

    $root->addChild($child1);

    $selector = new Selector('[custom-attr]', new Parser());
    expect(\count($selector->find($root)))->toEqual(1);
});

test('find multiple classes', function (): void {
    $root = new HtmlNode(new Tag('root'));
    $child1 = new HtmlNode(new Tag('a'));
    $child1->setAttribute('class', 'b');

    $child2 = new HtmlNode(new Tag('a'));
    $child2->setAttribute('class', 'b c');

    $root->addChild($child1);
    $root->addChild($child2);

    $selector = new Selector('a.b.c', new Parser());
    expect(\count($selector->find($root)))->toEqual(1);
});
