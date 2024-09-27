<?php


use PHPHtmlParser\Dom;
use PHPHtmlParser\Options;

test('cleanup input true', function (): void {
    $dom = new Dom();
    $dom->setOptions((new Options())->setCleanupInput(true));
    $dom->loadFromFile('tests/data/files/big.html');

    expect(\count($dom->find('style')))->toEqual(0);
    expect(\count($dom->find('script')))->toEqual(0);
});

test('cleanup input false', function (): void {
    $dom = new Dom();
    $dom->setOptions((new Options())->setCleanupInput(false));
    $dom->loadFromFile('tests/data/files/big.html');

    expect(\count($dom->find('style')))->toEqual(1);
    expect(\count($dom->find('script')))->toEqual(22);
});

test('remove styles true', function (): void {
    $dom = new Dom();
    $dom->setOptions((new Options())->setRemoveStyles(true));
    $dom->loadFromFile('tests/data/files/big.html');

    expect(\count($dom->find('style')))->toEqual(0);
});

test('remove styles false', function (): void {
    $dom = new Dom();
    $dom->setOptions((new Options())->setRemoveStyles(false));
    $dom->loadFromFile('tests/data/files/big.html');

    expect(\count($dom->find('style')))->toEqual(1);
    expect($dom->find('style')->getAttribute('type'))->toEqual('text/css');
});

test('remove scripts true', function (): void {
    $dom = new Dom();
    $dom->setOptions((new Options())->setRemoveScripts(true));
    $dom->loadFromFile('tests/data/files/big.html');

    expect(\count($dom->find('script')))->toEqual(0);
});

test('remove scripts false', function (): void {
    $dom = new Dom();
    $dom->setOptions((new Options())->setRemoveScripts(false));
    $dom->loadFromFile('tests/data/files/big.html');

    expect(\count($dom->find('script')))->toEqual(22);
    expect($dom->find('script')->getAttribute('type'))->toEqual('text/javascript');
});

test('smarty scripts', function (): void {
    $dom = new Dom();
    $dom->loadStr('
        aa={123}
        ');
    expect($dom->innerHtml)->toEqual(' aa= ');
});

test('smarty scripts disabled', function (): void {
    $dom = new Dom();
    $dom->setOptions((new Options())->setRemoveSmartyScripts(false));
    $dom->loadStr('
        aa={123}
        ');
    expect($dom->innerHtml)->toEqual(' aa={123} ');
});
