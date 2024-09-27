<?php


namespace PHPHtmlParser\Dom\Node;

use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\LogicalException;

/**
 * Inner node of the html tree, might have children.
 */
abstract class InnerNode extends ArrayNode
{
    protected int $prev;
    
    protected int $next;

    /**
     * An array of all the children.
     *
     * @var array
     */
    protected $children = [];

    /**
     * Checks if this node has children.
     */
    public function hasChildren(): bool
    {
        return !empty($this->children);
    }

    /**
     * Returns the child by id.
     *
     * @throws ChildNotFoundException
     */
    public function getChild(int $id): AbstractNode
    {
        if (!isset($this->children[$id])) {
            throw new ChildNotFoundException(sprintf("Child '%d' not found in this node.", $id));
        }

        return $this->children[$id]['node'];
    }

    /**
     * Returns a new array of child nodes.
     */
    public function getChildren(): array
    {
        $nodes = [];
        $childrenIds = [];
        try {
            $child = $this->firstChild();
            do {
                $nodes[] = $child;
                $childrenIds[] = $child->id;
                $child = $this->nextChild($child->id());
                if (\in_array($child->id, $childrenIds, true)) {
                    throw new CircularException('Circular sibling referance found. Child with id ' . $child->id() . ' found twice.');
                }
            } while (true);
        } catch (ChildNotFoundException $childNotFoundException) {
            // we are done looking for children
            unset($childNotFoundException);
        }

        return $nodes;
    }

    /**
     * Counts children.
     */
    public function countChildren(): int
    {
        return \count($this->children);
    }

    /**
     * Adds a child node to this node and returns the id of the child for this
     * parent.
     *
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws LogicalException
     */
    public function addChild(AbstractNode $child, int $before = -1): bool
    {
        $key = null;

        // check integrity
        if ($this->isAncestor($child->id())) {
            throw new CircularException('Can not add child. It is my ancestor.');
        }

        // check if child is itself
        if ($child->id() === $this->id) {
            throw new CircularException('Can not set itself as a child.');
        }

        $next = null;

        if ($this->hasChildren()) {
            if (isset($this->children[$child->id()])) {
                // we already have this child
                return false;
            }

            if ($before >= 0) {
                if (!isset($this->children[$before])) {
                    return false;
                }

                $key = $this->children[$before]['prev'];

                if ($key) {
                    $this->children[$key]['next'] = $child->id();
                }

                $this->children[$before]['prev'] = $child->id();
                $next = $before;
            } else {
                $sibling = $this->lastChild();
                $key = $sibling->id();

                $this->children[$key]['next'] = $child->id();
            }
        }

        $keys = \array_keys($this->children);

        $insert = [
            'node' => $child,
            'next' => $next,
            'prev' => $key,
        ];

        $index = $key ? (int) (\array_search($key, $keys, true) + 1) : 0;
        \array_splice($keys, $index, 0, (string) $child->id());

        $children = \array_values($this->children);
        \array_splice($children, $index, 0, [$insert]);

        // add the child
        $combination = \array_combine($keys, $children);
        if ($combination === false) {
            // The number of elements for each array isn't equal or if the arrays are empty.
            throw new LogicalException('array combine failed during add child method call.');
        }

        $this->children = $combination;

        // tell child I am the new parent
        $child->setParent($this);

        //clear any cache
        $this->clear();

        return true;
    }

    /**
     * Insert element before child with provided id.
     *
     * @throws ChildNotFoundException
     * @throws CircularException
     */
    public function insertBefore(AbstractNode $child, int $id): bool
    {
        return $this->addChild($child, $id);
    }

    /**
     * Insert element before after with provided id.
     *
     * @throws ChildNotFoundException
     * @throws CircularException
     */
    public function insertAfter(AbstractNode $child, int $id): bool
    {
        if (!isset($this->children[$id])) {
            return false;
        }

        if (isset($this->children[$id]['next']) && \is_int($this->children[$id]['next'])) {
            return $this->addChild($child, $this->children[$id]['next']);
        }

        // clear cache
        $this->clear();

        return $this->addChild($child);
    }

    /**
     * Removes the child by id.
     */
    public function removeChild(int $id): InnerNode
    {
        if (!isset($this->children[$id])) {
            return $this;
        }

        // handle moving next and previous assignments.
        $next = $this->children[$id]['next'];
        $prev = $this->children[$id]['prev'];
        if (!\is_null($next)) {
            $this->children[$next]['prev'] = $prev;
        }

        if (!\is_null($prev)) {
            $this->children[$prev]['next'] = $next;
        }

        // remove the child
        unset($this->children[$id]);

        //clear any cache
        $this->clear();

        return $this;
    }

    /**
     * Check if has next Child.
     *
     * @throws ChildNotFoundException
     *
     * @return mixed
     */
    public function hasNextChild(int $id)
    {
        $child = $this->getChild($id);

        return $this->children[$child->id()]['next'];
    }

    /**
     * Attempts to get the next child.
     *
     * @throws ChildNotFoundException
     *
     * @uses $this->getChild()
     */
    public function nextChild(int $id): AbstractNode
    {
        $child = $this->getChild($id);
        $next = $this->children[$child->id()]['next'];
        if (\is_null($next) || !\is_int($next)) {
            throw new ChildNotFoundException(sprintf("Child '%d' next sibling not found in this node.", $id));
        }

        return $this->getChild($next);
    }

    /**
     * Attempts to get the previous child.
     *
     * @throws ChildNotFoundException
     *
     * @uses $this->getChild()
     */
    public function previousChild(int $id): AbstractNode
    {
        $child = $this->getchild($id);
        $next = $this->children[$child->id()]['prev'];
        if (\is_null($next) || !\is_int($next)) {
            throw new ChildNotFoundException(sprintf("Child '%d' previous not found in this node.", $id));
        }

        return $this->getChild($next);
    }

    /**
     * Checks if the given node id is a child of the
     * current node.
     */
    public function isChild(int $id): bool
    {
        return in_array($id, \array_keys($this->children));
    }

    /**
     * Removes the child with id $childId and replace it with the new child
     * $newChild.
     *
     * @throws LogicalException
     */
    public function replaceChild(int $childId, AbstractNode $newChild): void
    {
        $oldChild = $this->children[$childId];

        $newChild->prev = (int) $oldChild['prev'];
        $newChild->next = (int) $oldChild['next'];

        $keys = \array_keys($this->children);
        $index = \array_search($childId, $keys, true);
        $keys[$index] = $newChild->id();
        $combination = \array_combine($keys, $this->children);
        if ($combination === false) {
            // The number of elements for each array isn't equal or if the arrays are empty.
            throw new LogicalException('array combine failed during replace child method call.');
        }

        $this->children = $combination;
        $this->children[$newChild->id()] = [
            'prev' => $oldChild['prev'],
            'node' => $newChild,
            'next' => $oldChild['next'],
        ];

        // change previous child id to new child
        if ($oldChild['prev'] && isset($this->children[$newChild->prev])) {
            $this->children[$oldChild['prev']]['next'] = $newChild->id();
        }

        // change next child id to new child
        if ($oldChild['next'] && isset($this->children[$newChild->next])) {
            $this->children[$oldChild['next']]['prev'] = $newChild->id();
        }

        // remove old child
        unset($this->children[$childId]);

        // clean out cache
        $this->clear();
    }

    /**
     * Shortcut to return the first child.
     *
     * @throws ChildNotFoundException
     *
     * @uses $this->getChild()
     */
    public function firstChild(): AbstractNode
    {
        if (\count($this->children) == 0) {
            // no children
            throw new ChildNotFoundException('No children found in node.');
        }

        $key = (int) array_key_first($this->children);

        return $this->getChild($key);
    }

    /**
     * Attempts to get the last child.
     *
     * @throws ChildNotFoundException
     *
     * @uses $this->getChild()
     */
    public function lastChild(): AbstractNode
    {
        if (\count($this->children) == 0) {
            // no children
            throw new ChildNotFoundException('No children found in node.');
        }

        $key = array_key_last($this->children);

        if (!\is_int($key)) {
            throw new LogicalException('Children array contain child with a key that is not an int.');
        }

        return $this->getChild($key);
    }

    /**
     * Checks if the given node id is a descendant of the
     * current node.
     */
    public function isDescendant(int $id): bool
    {
        if ($this->isChild($id)) {
            return true;
        }

        foreach ($this->children as $child) {
            /** @var InnerNode $node */
            $node = $child['node'];
            if ($node instanceof InnerNode
              && $node->hasChildren()
              && $node->isDescendant($id)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sets the parent node.
     *
     * @throws ChildNotFoundException
     * @throws CircularException
     */
    public function setParent(InnerNode $parent): AbstractNode
    {
        // check integrity
        if ($this->isDescendant($parent->id())) {
            throw new CircularException('Can not add descendant "' . $parent->id() . '" as my parent.');
        }

        // clear cache
        $this->clear();

        return parent::setParent($parent);
    }
}
