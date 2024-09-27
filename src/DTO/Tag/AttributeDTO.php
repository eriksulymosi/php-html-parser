<?php


namespace PHPHtmlParser\DTO\Tag;

final class AttributeDTO
{
    public readonly ?string $value;

    public function __construct(
        ?string $value = null,
        public readonly bool $isDoubleQuote = true,
        bool $htmlSpecialCharsDecode = false
    ) {
        $this->value = $htmlSpecialCharsDecode && !\is_null($value) ? \htmlspecialchars_decode($value) : $value;
    }
}
