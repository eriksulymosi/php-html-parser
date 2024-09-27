<?php


namespace PHPHtmlParser;

use PHPHtmlParser\Dom\Node\AbstractNode;
use PHPHtmlParser\Dom\Node\InnerNode;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\ParentNotFoundException;

class Finder
{
    /**
     * Finder constructor.
     */
    public function __construct(public readonly int $id)
    {}

    /**
     * Find node in tree by id.
     *
     * @throws ChildNotFoundException
     * @throws ParentNotFoundException
     */
    public function find(AbstractNode $node): AbstractNode|bool
    {
        if (!$node->id() && $node instanceof InnerNode) {
            return $this->find($node->firstChild());
        }

        if ($node->id() === $this->id) {
            return $node;
        }

        if ($node->hasNextSibling()) {
            $nextSibling = $node->nextSibling();
            if ($nextSibling->id() === $this->id) {
                return $nextSibling;
            }

            if ($nextSibling->id() > $this->id && $node instanceof InnerNode) {
                return $this->find($node->firstChild());
            }

            if ($nextSibling->id() < $this->id) {
                return $this->find($nextSibling);
            }
        } elseif (!$node->isTextNode() && $node instanceof InnerNode) {
            return $this->find($node->firstChild());
        }

        return false;
    }
}
