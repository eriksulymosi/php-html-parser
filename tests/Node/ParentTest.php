<?php

require_once 'tests/data/MockNode.php';

use PHPHtmlParser\Dom\Node\MockNode;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;

test('has child', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $parent->addChild($child);
    expect($parent->hasChildren())->toBeTrue();
});

test('has child no children', function (): void {
    $node = new MockNode();
    expect($node->hasChildren())->toBeFalse();
});

test('add child', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    expect($parent->addChild($child))->toBeTrue();
});

test('add child two parent', function (): void {
    $parent = new MockNode();
    $parent2 = new MockNode();
    $child = new MockNode();
    $parent->addChild($child);
    $parent2->addChild($child);
    expect($parent->hasChildren())->toBeFalse();
});

test('get child', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $child2 = new MockNode();
    $parent->addChild($child);
    $parent->addChild($child2);

    expect($parent->getChild($child2->id()) instanceof MockNode)->toBeTrue();
});

test('remove child', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $parent->addChild($child);
    $parent->removeChild($child->id());

    expect($parent->hasChildren())->toBeFalse();
});

test('remove child not exists', function (): void {
    $parent = new MockNode();
    $parent->removeChild(1);

    expect($parent->hasChildren())->toBeFalse();
});

test('next child', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $child2 = new MockNode();
    $parent->addChild($child);
    $parent->addChild($child2);

    expect($parent->nextChild($child->id())->id())->toEqual($child2->id());
});

test('has next child', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $child2 = new MockNode();
    $parent->addChild($child);
    $parent->addChild($child2);

    expect($parent->hasNextChild($child->id()))->toEqual($child2->id());
});

test('has next child not exists', function (): void {
    $parent = new MockNode();
    $child = new MockNode();

    $parent->hasNextChild($child->id());
})->throws(ChildNotFoundException::class);

test('next child with remove', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $child2 = new MockNode();
    $child3 = new MockNode();
    $parent->addChild($child);
    $parent->addChild($child2);
    $parent->addChild($child3);

    $parent->removeChild($child2->id());

    expect($parent->nextChild($child->id())->id())->toEqual($child3->id());
});

test('previous child', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $child2 = new MockNode();
    $parent->addChild($child);
    $parent->addChild($child2);

    expect($parent->previousChild($child2->id())->id())->toEqual($child->id());
});

test('previous child with remove', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $child2 = new MockNode();
    $child3 = new MockNode();
    $parent->addChild($child);
    $parent->addChild($child2);
    $parent->addChild($child3);

    $parent->removeChild($child2->id());

    expect($parent->previousChild($child3->id())->id())->toEqual($child->id());
});

test('first child', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $child2 = new MockNode();
    $child3 = new MockNode();
    $parent->addChild($child);
    $parent->addChild($child2);
    $parent->addChild($child3);

    expect($parent->firstChild()->id())->toEqual($child->id());
});

test('last child', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $child2 = new MockNode();
    $child3 = new MockNode();
    $parent->addChild($child);
    $parent->addChild($child2);
    $parent->addChild($child3);

    expect($parent->lastChild()->id())->toEqual($child3->id());
});

test('insert before first', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $child2 = new MockNode();
    $child3 = new MockNode();
    $parent->addChild($child2);
    $parent->addChild($child3);

    $parent->insertBefore($child, $child2->id());

    expect($parent->isChild($child->id()))->toBeTrue();
    expect($child->id())->toEqual($parent->firstChild()->id());
    expect($child2->id())->toEqual($child->nextSibling()->id());
    expect($child3->id())->toEqual($child2->nextSibling()->id());
    expect($child3->id())->toEqual($parent->lastChild()->id());
});

test('insert before last', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $child2 = new MockNode();
    $child3 = new MockNode();
    $parent->addChild($child);
    $parent->addChild($child3);

    $parent->insertBefore($child2, $child3->id());

    expect($parent->isChild($child2->id()))->toBeTrue();
    expect($child->id())->toEqual($parent->firstChild()->id());
    expect($child2->id())->toEqual($child->nextSibling()->id());
    expect($child3->id())->toEqual($child2->nextSibling()->id());
    expect($child3->id())->toEqual($parent->lastChild()->id());
});

test('insert after first', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $child2 = new MockNode();
    $child3 = new MockNode();
    $parent->addChild($child);
    $parent->addChild($child3);

    $parent->insertAfter($child2, $child->id());

    expect($parent->isChild($child2->id()))->toBeTrue();
    expect($child->id())->toEqual($parent->firstChild()->id());
    expect($child2->id())->toEqual($child->nextSibling()->id());
    expect($child3->id())->toEqual($child2->nextSibling()->id());
    expect($child3->id())->toEqual($parent->lastChild()->id());
});

test('insert after last', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $child2 = new MockNode();
    $child3 = new MockNode();
    $parent->addChild($child);
    $parent->addChild($child2);

    $parent->insertAfter($child3, $child2->id());

    expect($parent->isChild($child2->id()))->toBeTrue();
    expect($child->id())->toEqual($parent->firstChild()->id());
    expect($child2->id())->toEqual($child->nextSibling()->id());
    expect($child3->id())->toEqual($child2->nextSibling()->id());
    expect($child3->id())->toEqual($parent->lastChild()->id());
});

test('replace child', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $child2 = new MockNode();
    $child3 = new MockNode();
    $parent->addChild($child);
    $parent->addChild($child2);
    $parent->replaceChild($child->id(), $child3);

    expect($parent->isChild($child->id()))->toBeFalse();
});

test('set parent descendant exception', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $parent->addChild($child);
    $parent->setParent($child);
})->throws(CircularException::class);

test('add child ancestor exception', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $parent->addChild($child);
    $child->addChild($parent);
})->throws(CircularException::class);

test('add itself as child', function (): void {
    $parent = new MockNode();
    $parent->addChild($parent);
})->throws(CircularException::class);

test('is ancestor parent', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $parent->addChild($child);
    expect($child->isAncestor($parent->id()))->toBeTrue();
});

test('get ancestor', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $parent->addChild($child);
    $ancestor = $child->getAncestor($parent->id());
    expect($ancestor->id())->toEqual($parent->id());
});

test('get great ancestor', function (): void {
    $parent = new MockNode();
    $child = new MockNode();
    $child2 = new MockNode();
    $parent->addChild($child);
    $child->addChild($child2);
    $ancestor = $child2->getAncestor($parent->id());
    expect($ancestor)->not->toBeNull();
    expect($ancestor->id())->toEqual($parent->id());
});

test('get ancestor not found', function (): void {
    $parent = new MockNode();
    $ancestor = $parent->getAncestor(1);
    expect($ancestor)->toBeNull();
});
