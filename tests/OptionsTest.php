<?php


use PHPHtmlParser\Options;

test('default whitespace text node', function (): void {
    $options = new Options();

    expect($options->isWhitespaceTextNode())->toBeTrue();
});

test('setting option', function (): void {
    $options = new Options();
    $options->setStrict(true);

    expect($options->isStrict())->toBeTrue();
});

test('overwriting option', function (): void {
    $options = new Options();
    $options->setStrict(false);

    $options2 = new Options();
    $options2->setStrict(true);
    $options2->setWhitespaceTextNode(false);

    $options = $options->setFromOptions($options2);

    expect($options->isStrict())->toBeTrue();
    expect($options->isWhitespaceTextNode())->toBeFalse();
});

test('setters', function (): void {
    $options = new Options();

    $options->setWhitespaceTextNode(true);

    expect($options->isWhitespaceTextNode())->toBeTrue();

    $options->setStrict(true);
    expect($options->isStrict())->toBeTrue();

    $options->setCleanupInput(true);
    expect($options->isCleanupInput())->toBeTrue();

    $options->setRemoveScripts(true);
    expect($options->isRemoveScripts())->toBeTrue();

    $options->setRemoveStyles(true);
    expect($options->isRemoveStyles())->toBeTrue();

    $options->setPreserveLineBreaks(true);
    expect($options->isPreserveLineBreaks())->toBeTrue();

    $options->setRemoveDoubleSpace(true);
    expect($options->isRemoveDoubleSpace())->toBeTrue();

    $options->setRemoveSmartyScripts(true);
    expect($options->isRemoveSmartyScripts())->toBeTrue();

    $options->setHtmlSpecialCharsDecode(true);
    expect($options->isHtmlSpecialCharsDecode())->toBeTrue();
});
