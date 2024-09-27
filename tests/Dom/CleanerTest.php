<?php

use PHPHtmlParser\Dom\Cleaner;
use PHPHtmlParser\Options;

test('clean eregi failure file', function (): void {
    $string = (new Cleaner())->clean(\file_get_contents('tests/data/files/mvEregiReplaceFailure.html'), new Options(), 'utf-8');
    
    expect($string)->not->toHaveLength(0);
});
