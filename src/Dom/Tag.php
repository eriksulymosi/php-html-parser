<?php


namespace PHPHtmlParser\Dom;

use PHPHtmlParser\DTO\Tag\AttributeDTO;
use PHPHtmlParser\Exceptions\Tag\AttributeNotFoundException;
use TypeError;

/**
 * Class Tag.
 */
class Tag
{
    /**
     * The name of the tag.
     */
    protected string $name;

    /**
     * The attributes of the tag.
     *
     * @var AttributeDTO[]
     */
    protected $attr = [];

    /**
     * Is this tag self closing.
     *
     * @var bool
     */
    protected $selfClosing = false;

    /**
     * If self-closing, will this use a trailing slash. />.
     *
     * @var bool
     */
    protected $trailingSlash = true;

    /**
     * Tag noise.
     */
    protected $noise = '';

    private bool $htmlSpecialCharsDecode = false;

    /**
     * What the opening of this tag will be.
     */
    private string $opening = '<';

    /**
     * What the closing tag for self-closing elements should be.
     */
    private string $closing = ' />';

    /**
     * Sets up the tag with a name.
     *
     * @param $name
     */
    public function __construct(string $name)
    {
        $this->name = \mb_strtolower($name);
    }

    /**
     * Returns the name of this tag.
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Sets the tag to be self closing.
     */
    public function selfClosing(): Tag
    {
        $this->selfClosing = true;

        return clone $this;
    }

    public function setOpening(string $opening): Tag
    {
        $this->opening = $opening;

        return clone $this;
    }

    public function setClosing(string $closing): Tag
    {
        $this->closing = $closing;

        return clone $this;
    }

    /**
     * Sets the tag to not use a trailing slash.
     */
    public function noTrailingSlash(): Tag
    {
        $this->trailingSlash = false;

        return clone $this;
    }

    /**
     * Checks if the tag is self closing.
     */
    public function isSelfClosing(): bool
    {
        return $this->selfClosing;
    }

    public function setHtmlSpecialCharsDecode(bool $htmlSpecialCharsDecode = false): void
    {
        $this->htmlSpecialCharsDecode = $htmlSpecialCharsDecode;
    }

    /**
     * Sets the noise for this tag (if any).
     */
    public function noise(string $noise): Tag
    {
        $this->noise = $noise;

        return clone $this;
    }

    /**
     * Set an attribute for this tag.
     */
    public function setAttribute(string $key, ?string $attributeValue, bool $doubleQuote = true): Tag
    {
        $attributeDTO = new AttributeDTO(
            $attributeValue,
            $doubleQuote,
            $this->htmlSpecialCharsDecode
        );

        $this->attr[\mb_strtolower($key)] = $attributeDTO;

        return clone $this;
    }

    /**
     * Set inline style attribute value.
     */
    public function setStyleAttributeValue(mixed $attr_key, mixed $attr_value): void
    {
        $style_array = $this->getStyleAttributeArray();
        $style_array[$attr_key] = $attr_value;

        $style_string = '';
        foreach ($style_array as $key => $value) {
            $style_string .= $key . ':' . $value . ';';
        }

        $this->setAttribute('style', $style_string);
    }

    /**
     * Get style attribute in array.
     */
    public function getStyleAttributeArray(): array
    {
        try {
            $value = $this->getAttribute('style')->value;
            if (\is_null($value)) {
                return [];
            }

            $value = \explode(';', \substr(\trim($value), 0, -1));
            $result = [];
            foreach ($value as $attr) {
                $attr = \explode(':', $attr);
                $result[$attr[0]] = $attr[1];
            }

            return $result;
        } catch (AttributeNotFoundException $attributeNotFoundException) {
            unset($attributeNotFoundException);

            return [];
        }
    }

    /**
     * Removes an attribute from this tag.
     *
     */
    public function removeAttribute(mixed $key): void
    {
        $key = \mb_strtolower((string) $key);
        unset($this->attr[$key]);
    }

    /**
     * Removes all attributes on this tag.
     */
    public function removeAllAttributes(): void
    {
        $this->attr = [];
    }

    /**
     * Sets the attributes for this tag.
     *
     * @return $this
     */
    public function setAttributes(array $attr): static
    {
        foreach ($attr as $key => $info) {
            if (\is_array($info)) {
                $this->setAttribute($key, $info['value'], $info['doubleQuote']);
            } else {
                $this->setAttribute($key, $info);
            }
        }

        return $this;
    }

    /**
     * Returns all attributes of this tag.
     *
     * @return AttributeDTO[]
     */
    public function getAttributes(): array
    {
        $return = [];
        foreach (\array_keys($this->attr) as $attr) {
            try {
                $return[$attr] = $this->getAttribute($attr);
            } catch (AttributeNotFoundException) {}
        }

        return $return;
    }

    /**
     * Returns an attribute by the key.
     *
     * @throws AttributeNotFoundException
     */
    public function getAttribute(string $key): AttributeDTO
    {
        $key = \mb_strtolower($key);
        if (!isset($this->attr[$key])) {
            throw new AttributeNotFoundException("Attribute with key \"$key\" not found.");
        }

        $attributeDTO = $this->attr[$key];

        return $attributeDTO;
    }

    /**
     * Returns TRUE if node has attribute.
     *
     * @return bool
     */
    public function hasAttribute(string $key)
    {
        return isset($this->attr[$key]);
    }

    /**
     * Generates the opening tag for this object.
     */
    public function makeOpeningTag(): string
    {
        $return = $this->opening . $this->name;

        // add the attributes
        foreach (\array_keys($this->attr) as $key) {
            try {
                $attributeDTO = $this->getAttribute($key);
            } catch (AttributeNotFoundException) {
                // attribute that was in the array not found in the array... let's continue.
                continue;
            } catch (TypeError) {
              $val = null;
            }

            $val = $attributeDTO->value;
            if (\is_null($val)) {
                $return .= ' ' . $key;
            } elseif ($attributeDTO->isDoubleQuote) {
                $return .= ' ' . $key . '="' . $val . '"';
            } else {
                $return .= ' ' . $key . "='" . $val . "'";
            }
        }

        if ($this->selfClosing && $this->trailingSlash) {
            return $return . $this->closing;
        }

        return $return . '>';
    }

    /**
     * Generates the closing tag for this object.
     */
    public function makeClosingTag(): string
    {
        if ($this->selfClosing) {
            return '';
        }

        return '</' . $this->name . '>';
    }
}
