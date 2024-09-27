<?php

require_once 'tests/data/MockNode.php';

use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\Node\HtmlNode;
use PHPHtmlParser\Dom\Node\MockNode;
use PHPHtmlParser\Dom\Node\TextNode;
use PHPHtmlParser\Dom\Tag;
use PHPHtmlParser\Exceptions\ParentNotFoundException;
use PHPHtmlParser\Exceptions\UnknownChildTypeException;

test('inner html', function (): void {
    $div = new Tag('div');
    $div->setAttributes([
        'class' => [
            'value'       => 'all',
            'doubleQuote' => true,
        ],
    ]);
    $a = new Tag('a');
    $a->setAttributes([
        'href' => [
            'value'       => 'http://google.com',
            'doubleQuote' => false,
        ],
    ]);
    $br = new Tag('br');
    $br->selfClosing();

    $parent = new HtmlNode($div);
    $childa = new HtmlNode($a);
    $childbr = new HtmlNode($br);
    $parent->addChild($childa);
    $parent->addChild($childbr);

    $childa->addChild(new TextNode('link'));

    expect($parent->innerHtml())->toEqual("<a href='http://google.com'>link</a><br />");
});

test('inner html twice', function (): void {
    $div = new Tag('div');
    $div->setAttributes([
        'class' => [
            'value'       => 'all',
            'doubleQuote' => true,
        ],
    ]);
    $a = new Tag('a');
    $br = new Tag('br');
    $br->selfClosing();

    $parent = new HtmlNode($div);
    $childa = new HtmlNode($a);
    $childa->setAttribute('href', 'http://google.com', false);

    $childbr = new HtmlNode($br);
    $parent->addChild($childa);
    $parent->addChild($childbr);

    $childa->addChild(new TextNode('link'));

    $inner = $parent->innerHtml();
    expect($parent->innerHtml())->toEqual($inner);
});

test('inner html unkown child', function (): void {
    $div = new Tag('div');
    $div->setAttributes([
        'class' => [
            'value'       => 'all',
            'doubleQuote' => true,
        ],
    ]);
    $a = new Tag('a');
    $a->setAttributes([
        'href' => [
            'value'       => 'http://google.com',
            'doubleQuote' => false,
        ],
    ]);
    $br = new Tag('br');
    $br->selfClosing();

    $parent = new HtmlNode($div);
    $childa = new HtmlNode($a);
    $childbr = new MockNode($br);
    $parent->addChild($childa);
    $parent->addChild($childbr);

    $childa->addChild(new TextNode('link'));

    $inner = $parent->innerHtml();
    expect($parent->innerHtml())->toEqual($inner);
})->throws(UnknownChildTypeException::class);

test('inner html magic', function (): void {
    $parent = new HtmlNode('div');
    $parent->tag->setAttributes([
        'class' => [
            'value'       => 'all',
            'doubleQuote' => true,
        ],
    ]);
    $childa = new HtmlNode('a');
    $childa->getTag()->setAttributes([
        'href' => [
            'value'       => 'http://google.com',
            'doubleQuote' => false,
        ],
    ]);
    $childbr = new HtmlNode('br');
    $childbr->getTag()->selfClosing();

    $parent->addChild($childa);
    $parent->addChild($childbr);

    $childa->addChild(new TextNode('link'));

    expect($parent->innerHtml)->toEqual("<a href='http://google.com'>link</a><br />");
});

test('outer html', function (): void {
    $div = new Tag('div');
    $div->setAttributes([
        'class' => [
            'value'       => 'all',
            'doubleQuote' => true,
        ],
    ]);
    $a = new Tag('a');
    $a->setAttributes([
        'href' => [
            'value'       => 'http://google.com',
            'doubleQuote' => false,
        ],
    ]);
    $br = new Tag('br');
    $br->selfClosing();

    $parent = new HtmlNode($div);
    $childa = new HtmlNode($a);
    $childbr = new HtmlNode($br);
    $parent->addChild($childa);
    $parent->addChild($childbr);

    $childa->addChild(new TextNode('link'));

    expect($parent->outerHtml())->toEqual('<div class="all"><a href=\'http://google.com\'>link</a><br /></div>');
});

test('outer html twice', function (): void {
    $div = new Tag('div');
    $div->setAttributes([
        'class' => [
            'value'       => 'all',
            'doubleQuote' => true,
        ],
    ]);
    $a = new Tag('a');
    $a->setAttributes([
        'href' => [
            'value'       => 'http://google.com',
            'doubleQuote' => false,
        ],
    ]);
    $br = new Tag('br');
    $br->selfClosing();

    $parent = new HtmlNode($div);
    $childa = new HtmlNode($a);
    $childbr = new HtmlNode($br);
    $parent->addChild($childa);
    $parent->addChild($childbr);

    $childa->addChild(new TextNode('link'));

    $outer = $parent->outerHtml();
    expect($parent->outerHtml())->toEqual($outer);
});

test('outer html empty', function (): void {
    $a = new Tag('a');
    $a->setAttributes([
        'href' => [
            'value'       => 'http://google.com',
            'doubleQuote' => false,
        ],
    ]);
    $node = new HtmlNode($a);

    expect($node->OuterHtml())->toEqual("<a href='http://google.com'></a>");
});

test('outer html magic', function (): void {
    $parent = new HtmlNode('div');
    $parent->getTag()->setAttributes([
        'class' => [
            'value'       => 'all',
            'doubleQuote' => true,
        ],
    ]);
    $childa = new HtmlNode('a');
    $childa->getTag()->setAttributes([
        'href' => [
            'value'       => 'http://google.com',
            'doubleQuote' => false,
        ],
    ]);
    $childbr = new HtmlNode('br');
    $childbr->getTag()->selfClosing();

    $parent->addChild($childa);
    $parent->addChild($childbr);

    $childa->addChild(new TextNode('link'));

    expect($parent->outerHtml)->toEqual('<div class="all"><a href=\'http://google.com\'>link</a><br /></div>');
});

test('outer html no value attribute', function (): void {
    $parent = new HtmlNode('div');
    $parent->setAttribute('class', 'all');

    $childa = new HtmlNode('a');
    $childa->setAttribute('href', 'http://google.com', false);
    $childa->setAttribute('ui-view', null);

    $childbr = new HtmlNode('br');
    $childbr->getTag()->selfClosing();

    $parent->addChild($childa);
    $parent->addChild($childbr);

    $childa->addChild(new TextNode('link'));

    expect($parent->outerHtml)->toEqual('<div class="all"><a href=\'http://google.com\' ui-view>link</a><br /></div>');
});

test('outer html with changes', function (): void {
    $div = new Tag('div');
    $div->setAttributes([
        'class' => [
            'value'       => 'all',
            'doubleQuote' => true,
        ],
    ]);
    $a = new Tag('a');
    $a->setAttributes([
        'href' => [
            'value'       => 'http://google.com',
            'doubleQuote' => false,
        ],
    ]);
    $br = new Tag('br');
    $br->selfClosing();

    $parent = new HtmlNode($div);
    $childa = new HtmlNode($a);
    $childbr = new HtmlNode($br);
    $parent->addChild($childa);
    $parent->addChild($childbr);

    $childa->addChild(new TextNode('link'));

    expect($parent->outerHtml())->toEqual('<div class="all"><a href=\'http://google.com\'>link</a><br /></div>');

    $childa->setAttribute('href', 'https://www.google.com');

    expect($childa->outerHtml())->toEqual('<a href="https://www.google.com">link</a>');
});

test('text', function (): void {
    $a = new Tag('a');
    $node = new HtmlNode($a);
    $node->addChild(new TextNode('link'));

    expect($node->text())->toEqual('link');
});

test('text twice', function (): void {
    $a = new Tag('a');
    $node = new HtmlNode($a);
    $node->addChild(new TextNode('link'));

    $text = $node->text();
    expect($node->text())->toEqual($text);
});

test('text none', function (): void {
    $a = new Tag('a');
    $node = new HtmlNode($a);

    expect($node->text())->toBeEmpty();
});

test('text magic', function (): void {
    $node = new HtmlNode('a');
    $node->addChild(new TextNode('link'));

    expect($node->text)->toEqual('link');
});

test('text look in children', function (): void {
    $p = new HtmlNode('p');
    $a = new HtmlNode('a');
    $a->addChild(new TextNode('click me'));

    $p->addChild(new TextNode('Please '));
    $p->addChild($a);
    $p->addChild(new TextNode('!'));

    $node = new HtmlNode('div');
    $node->addChild($p);

    expect($node->text(true))->toEqual('Please click me!');
});

test('inner text', function (): void {
    $node = new HtmlNode('div');
    $node->addChild(new TextNode('123 '));

    $anode = new HtmlNode('a');
    $anode->addChild(new TextNode('456789 '));

    $span_node = new HtmlNode('span');
    $span_node->addChild(new TextNode('101112'));

    $node->addChild($anode);
    $node->addChild($span_node);

    expect('123 456789 101112')->toEqual($node->innerText);
});

test('text look in children and no children', function (): void {
    $p = new HtmlNode('p');
    $a = new HtmlNode('a');
    $a->addChild(new TextNode('click me'));

    $p->addChild(new TextNode('Please '));
    $p->addChild($a);
    $p->addChild(new TextNode('!'));

    $p->text;
    $p->text(true);

    expect($p->text(true))->toEqual('Please click me!');
});

test('get attribute', function (): void {
    $node = new HtmlNode('a');
    $node->getTag()->setAttributes([
        'href' => [
            'value'       => 'http://google.com',
            'doubleQuote' => false,
        ],
        'class' => [
            'value'       => 'outerlink rounded',
            'doubleQuote' => true,
        ],
    ]);

    expect($node->getAttribute('class'))->toEqual('outerlink rounded');
});

test('get attribute magic', function (): void {
    $node = new HtmlNode('a');
    $node->getTag()->setAttributes([
        'href' => [
            'value'       => 'http://google.com',
            'doubleQuote' => false,
        ],
        'class' => [
            'value'       => 'outerlink rounded',
            'doubleQuote' => true,
        ],
    ]);

    expect($node->href)->toEqual('http://google.com');
});

test('get attributes', function (): void {
    $node = new HtmlNode('a');
    $node->getTag()->setAttributes([
        'href' => [
            'value'       => 'http://google.com',
            'doubleQuote' => false,
        ],
        'class' => [
            'value'       => 'outerlink rounded',
            'doubleQuote' => true,
        ],
    ]);

    expect($node->getAttributes()['class'])->toEqual('outerlink rounded');
});

test('set attribute', function (): void {
    $node = new HtmlNode('a');
    $node->setAttribute('class', 'foo');

    expect($node->getAttribute('class'))->toEqual('foo');
});

test('remove attribute', function (): void {
    $node = new HtmlNode('a');
    $node->setAttribute('class', 'foo');
    $node->removeAttribute('class');
    $this->assertnull($node->getAttribute('class'));
});

test('remove all attributes', function (): void {
    $node = new HtmlNode('a');
    $node->setAttribute('class', 'foo');
    $node->setAttribute('href', 'http://google.com');
    $node->removeAllAttributes();

    expect(\count($node->getAttributes()))->toEqual(0);
});

test('set tag', function (): void {
    $node = new HtmlNode('div');
    expect($node->outerHtml())->toEqual('<div></div>');

    $node->setTag('p');
    expect($node->outerHtml())->toEqual('<p></p>');

    $node->setTag(new Tag('span'));
    expect($node->outerHtml())->toEqual('<span></span>');
});

test('countable', function (): void {
    $div = new Tag('div');
    $div->setAttributes([
        'class' => [
            'value'       => 'all',
            'doubleQuote' => true,
        ],
    ]);
    $a = new Tag('a');
    $a->setAttributes([
        'href' => [
            'value'       => 'http://google.com',
            'doubleQuote' => false,
        ],
    ]);
    $br = new Tag('br');
    $br->selfClosing();

    $parent = new HtmlNode($div);
    $childa = new HtmlNode($a);
    $childbr = new HtmlNode($br);
    $parent->addChild($childa);
    $parent->addChild($childbr);

    $childa->addChild(new TextNode('link'));

    expect(\count($parent))->toEqual(\count($parent->getChildren()));
});

test('iterator', function (): void {
    $div = new Tag('div');
    $div->setAttributes([
        'class' => [
            'value'       => 'all',
            'doubleQuote' => true,
        ],
    ]);
    $a = new Tag('a');
    $a->setAttributes([
        'href' => [
            'value'       => 'http://google.com',
            'doubleQuote' => false,
        ],
    ]);
    $br = new Tag('br');
    $br->selfClosing();

    $parent = new HtmlNode($div);
    $childa = new HtmlNode($a);
    $childbr = new HtmlNode($br);
    $parent->addChild($childa);
    $parent->addChild($childbr);

    $childa->addChild(new TextNode('link'));

    $children = 0;
    foreach ($parent as $child) {
        ++$children;
    }

    expect($children)->toEqual(2);
});

test('ancestor by tag failure', function (): void {
    $a = new Tag('a');
    $node = new HtmlNode($a);
    $node->ancestorByTag('div');
})->throws(ParentNotFoundException::class);

test('replace node', function (): void {
    $dom = new Dom();
    $dom->loadStr('<div class="all"><p>Hey bro, <a href="google.com">click here</a><br /> :)</p></div>');

    $id = $dom->find('p')[0]->id();
    $newChild = new HtmlNode('h1');
    $dom->find('p')[0]->getParent()->replaceChild($id, $newChild);
    expect((string) $dom)->toEqual('<div class="all"><h1></h1></div>');
});

test('text node first child', function (): void {
    $dom = new Dom();
    $dom->loadStr('<div class="all"><p>Hey bro, <a href="google.com">click here</a><br /> :)</p></div>');

    $p = $dom->find('p');
    foreach ($p as $element) {
        $child = $element->firstChild();
        expect($child)->toBeInstanceOf(TextNode::class);
        break;
    }
});
