<?php


namespace PHPHtmlParser\Dom\Node;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use PHPHtmlParser\Dom\Tag;

/**
 * Dom node object which will allow users to use it as
 * an array.
 */
abstract class ArrayNode extends AbstractNode implements IteratorAggregate, Countable
{
    
    /**
     * Remembers what the innerHtml was if it was scanned previously.
     */
    protected ?string $innerHtml = null;

    /**
     * Remembers what the outerHtml was if it was scanned previously.
     */
    protected ?string $outerHtml = null;

    /**
     * Remembers what the innerText was if it was scanned previously.
     */
    protected ?string $innerText = null;
    
    /**
     * Remembers what the text was if it was scanned previously.
     */
    protected ?string $text = null;

    /**
     * Gets the iterator.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->getIteratorArray());
    }

    /**
     * Returns the count of the iterator array.
     */
    public function count(): int
    {
        return \count($this->getIteratorArray());
    }

    /**
     * Returns the array to be used the the iterator.
     */
    abstract protected function getIteratorArray(): array;
}
