<?php


use PHPHtmlParser\Content;
use PHPHtmlParser\Enum\StringToken;

test('char', function (): void {
    $content = new Content('abcde');
    expect($content->char())->toEqual('a');
});

test('char selection', function (): void {
    $content = new Content('abcde');
    expect($content->char(3))->toEqual('d');
});

test('fast forward', function (): void {
    $content = new Content('abcde');
    $content->fastForward(2);

    expect($content->char())->toEqual('c');
});

test('rewind', function (): void {
    $content = new Content('abcde');
    $content->fastForward(2)
            ->rewind(1);
    expect($content->char())->toEqual('b');
});

test('rewind negative', function (): void {
    $content = new Content('abcde');
    $content->fastForward(2)
            ->rewind(100);
    expect($content->char())->toEqual('a');
});

test('copy until', function (): void {
    $content = new Content('abcdeedcba');
    expect($content->copyUntil('ed'))->toEqual('abcde');
});

test('copy until char', function (): void {
    $content = new Content('abcdeedcba');
    expect($content->copyUntil('edc', true))->toEqual('ab');
});

test('copy until escape', function (): void {
    $content = new Content('foo\"bar"bax');
    expect($content->copyUntil('"', false, true))->toEqual('foo\"bar');
});

test('copy until not found', function (): void {
    $content = new Content('foo\"bar"bax');
    expect($content->copyUntil('baz'))->toEqual('foo\"bar"bax');
});

test('copy by token', function (): void {
    $content = new Content('<a href="google.com">');
    $content->fastForward(3);

    expect($content->copyByToken(StringToken::ATTR, true))->toEqual('href="google.com"');
});

test('skip', function (): void {
    $content = new Content('abcdefghijkl');
    $content->skip('abcd');

    expect($content->char())->toEqual('e');
});

test('skip copy', function (): void {
    $content = new Content('abcdefghijkl');
    expect($content->skip('abcd', true))->toEqual('abcd');
});

test('skip by token', function (): void {
    $content = new Content(' b c');
    $content->fastForward(1);
    $content->skipByToken(StringToken::BLANK);

    expect($content->char())->toEqual('b');
});
