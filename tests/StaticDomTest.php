<?php

use \PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\NotLoadedException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

test('load str', function (): void {
    $dom = (new Dom())->loadStr('<div class="all"><p>Hey bro, <a href="google.com">click here</a><br /> :)</p></div>');
    $div = $dom->find('div', 0);
    expect($div->outerHtml)->toEqual('<div class="all"><p>Hey bro, <a href="google.com">click here</a><br /> :)</p></div>');
});

test('load with file', function (): void {
    $dom = (new Dom())->loadFromFile('tests/data/files/small.html');
    expect($dom->find('.post-user font', 0)->text)->toEqual('VonBurgermeister');
});

test('load from file', function (): void {
    $dom = (new Dom())->loadFromFile('tests/data/files/small.html');
    
    expect($dom->find('.post-user font', 0)->text)->toEqual('VonBurgermeister');
});

test('find noload str', function (): void {
    (new Dom())->find('.post-user font', 0);
})->throws(NotLoadedException::class);

test('find i', function (): void {
    $dom = (new Dom())->loadFromFile('tests/data/files/big.html');
    expect($dom->find('i')[1]->innerHtml)->toEqual('В кустах блестит металл<br /> И искрится ток<br /> Человечеству конец');
});

test('load from url', function (): void {
    $streamMock = Mockery::mock(StreamInterface::class);
    $streamMock->shouldReceive('getContents')
        ->once()
        ->andReturn(\file_get_contents('tests/data/files/small.html'));
    $responseMock = Mockery::mock(ResponseInterface::class);
    $responseMock->shouldReceive('getBody')
        ->once()
        ->andReturn($streamMock);
    $clientMock = Mockery::mock(ClientInterface::class);
    $clientMock->shouldReceive('sendRequest')
        ->once()
        ->andReturn($responseMock);

    $dom = (new Dom())->loadFromUrl('http://google.com', null, $clientMock);
    expect($dom->find('.post-row div .post-user font', 0)->text)->toEqual('VonBurgermeister');
});
