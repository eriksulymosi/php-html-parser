<?php

require_once 'tests/data/MockNode.php';

use PHPHtmlParser\Dom\Node\MockNode;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\ParentNotFoundException;

test('get parent', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $child->setParent($parent);

    expect($child->getParent()->id())->toEqual($parent->id());
});

test('set parent twice', function (): void {
    $parent = new MockNode();
    $parent2 = new MockNode();
    $child = new MockNode();
    $child->setParent($parent);
    $child->setParent($parent2);

    expect($child->getParent()->id())->toEqual($parent2->id());
});

test('next sibling', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $child2 = new MockNode();
    $child->setParent($parent);
    $child2->setParent($parent);
    expect($child->nextSibling()->id())->toEqual($child2->id());
});

test('next sibling not found', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $child->setParent($parent);
    $child->nextSibling();
})->throws(ChildNotFoundException::class);

test('next sibling no parent', function (): void {
    $child = new MockNode();
    $child->nextSibling();
})->throws(ParentNotFoundException::class);

test('previous sibling', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $child2 = new MockNode();
    $child->setParent($parent);
    $child2->setParent($parent);
    expect($child2->previousSibling()->id())->toEqual($child->id());
});

test('previous sibling not found', function (): void {
    $parent = new MockNode();
    $node = new MockNode();
    $node->setParent($parent);
    $node->previousSibling();
})->throws(ChildNotFoundException::class);

test('previous sibling no parent', function (): void {
    $child = new MockNode();
    $child->previousSibling();
})->throws(ParentNotFoundException::class);

test('get children', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $child2 = new MockNode();
    $child->setParent($parent);
    $child2->setParent($parent);
    expect($parent->getChildren()[0]->id())->toEqual($child->id());
});

test('count children', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $child2 = new MockNode();
    $child->setParent($parent);
    $child2->setParent($parent);
    expect($parent->countChildren())->toEqual(2);
});

test('is child', function (): void {
    $parent = new MockNode();
    $child1 = new MockNode();
    $child2 = new MockNode();

    $child1->setParent($parent);
    $child2->setParent($child1);

    expect($parent->isChild($child1->id()))->toBeTrue();
    expect($parent->isDescendant($child2->id()))->toBeTrue();
    expect($parent->isChild($child2->id()))->toBeFalse();
});
