<?php


use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\LogicalException;
use PHPHtmlParser\Options;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
afterEach(function (): void {
    Mockery::close();
});

test('parsing cdata', function (): void {
    $html = "<script type=\"text/javascript\">/* <![CDATA[ */var et_core_api_spam_recaptcha = '';/* ]]> */</script>";
    $dom = new Dom();
    $dom->setOptions((new Options())->setCleanupInput(false));
    $dom->loadStr($html);

    expect($dom->root->outerHtml())->toBe($html);
});

test('load selfclosing attr', function (): void {
    $dom = new Dom();
    $dom->loadStr("<div class='all'><br  foo  bar  />baz</div>");

    $br = $dom->find('br', 0);
    expect($br->outerHtml)->toEqual('<br foo bar />');
});

test('load selfclosing attr to string', function (): void {
    $dom = new Dom();
    $dom->loadStr("<div class='all'><br  foo  bar  />baz</div>");

    $br = $dom->find('br', 0);
    expect((string) $br)->toEqual('<br foo bar />');
});

test('load no opening tag', function (): void {
    $dom = new Dom();
    $dom->loadStr('<div class="all"><font color="red"><strong>PR Manager</strong></font></b><div class="content">content</div></div>');

    expect($dom->find('.content', 0)->text)->toEqual('content');
});

test('load no value attribute', function (): void {
    $dom = new Dom();
    $dom->loadStr('<div class="content"><div class="grid-container" ui-view>Main content here</div></div>');

    expect($dom->innerHtml)->toEqual('<div class="content"><div class="grid-container" ui-view>Main content here</div></div>');
});

test('load backslash attribute value', function (): void {
    $dom = new Dom();
    $dom->loadStr('<div class="content"><div id="\" class="grid-container" ui-view>Main content here</div></div>');

    expect($dom->innerHtml)->toEqual('<div class="content"><div id="\" class="grid-container" ui-view>Main content here</div></div>');
});

test('load no value attribute before', function (): void {
    $dom = new Dom();
    $dom->loadStr('<div class="content"><div ui-view class="grid-container">Main content here</div></div>');

    expect($dom->innerHtml)->toEqual('<div class="content"><div ui-view class="grid-container">Main content here</div></div>');
});

test('load upper case', function (): void {
    $dom = new Dom();
    $dom->loadStr('<DIV CLASS="ALL"><BR><P>hEY BRO, <A HREF="GOOGLE.COM" DATA-QUOTE="\"">CLICK HERE</A></BR></DIV>');

    expect($dom->find('div', 0)->innerHtml)->toEqual('<br /><p>hEY BRO, <a href="GOOGLE.COM" data-quote="\"">CLICK HERE</a></p>');
});

test('load with file', function (): void {
    $dom = new Dom();
    $dom->loadFromFile('tests/data/files/small.html');

    expect($dom->find('.post-user font', 0)->text)->toEqual('VonBurgermeister');
});

test('load from file', function (): void {
    $dom = new Dom();
    $dom->loadFromFile('tests/data/files/small.html');

    expect($dom->find('.post-user font', 0)->text)->toEqual('VonBurgermeister');
});

test('load from file find', function (): void {
    $dom = new Dom();
    $dom->loadFromFile('tests/data/files/small.html');

    expect($dom->find('.post-row div .post-user font', 0)->text)->toEqual('VonBurgermeister');
});

test('load from file not found', function (): void {
    $dom = new Dom();
    $dom->loadFromFile('tests/data/files/unkowne.html');
})->throws(LogicalException::class);

test('load utf8', function (): void {
    $dom = new Dom();
    $dom->loadStr('<p>Dzień</p>');

    expect($dom->find('p', 0)->text)->toEqual('Dzień');
});

test('load file whitespace', function (): void {
    $dom = new Dom();
    $dom->setOptions((new Options())->setCleanupInput(false));
    $dom->loadFromFile('tests/data/files/whitespace.html');

    expect(\count($dom->find('.class')))->toEqual(1);
    expect((string) $dom)->toEqual('<span><span class="class"></span></span>');
});

test('load file big', function (): void {
    $dom = new Dom();
    $dom->loadFromFile('tests/data/files/big.html');

    expect(\count($dom->find('.content-border')))->toEqual(20);
});

test('load file big twice', function (): void {
    $dom = new Dom();
    $dom->loadFromFile('tests/data/files/big.html');

    $post = $dom->find('.post-row', 0);
    expect($post->find('.post-message', 0)->innerHtml)->toEqual(' <p>Журчанье воды<br /> Черно-белые тени<br /> Вновь на фонтане</p> ');
});

test('load file big twice preserve option', function (): void {
    $dom = new Dom();
    $dom->loadFromFile('tests/data/files/big.html',
        (new Options())->setPreserveLineBreaks(true));
    $post = $dom->find('.post-row', 0);
    expect(\trim($post->find('.post-message', 0)->innerHtml))->toEqual("<p>Журчанье воды<br />\nЧерно-белые тени<br />\nВновь на фонтане</p>");
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

    $dom = new Dom();
    $dom->loadFromUrl('http://google.com', null, $clientMock);

    expect($dom->find('.post-row div .post-user font', 0)->text)->toEqual('VonBurgermeister');
});

test('script cleaner script tag', function (): void {
    $dom = new Dom();
    $dom->loadStr('
        <p>.....</p>
        <script>
        Some code ... 
        document.write("<script src=\'some script\'><\/script>") 
        Some code ... 
        </script>
        <p>....</p>');
    expect($dom->getElementsByTag('p')[1]->innerHtml)->toEqual('....');
});

test('closing span', function (): void {
    $dom = new Dom();
    $dom->loadStr("<div class='foo'></span>sometext</div>");

    expect($dom->getElementsByTag('div')[0]->innerHtml)->toEqual('sometext');
});

test('multiple double quotes', function (): void {
    $dom = new Dom();
    $dom->loadStr('<a title="This is a "test" of double quotes" href="http://www.example.com">Hello</a>');

    expect($dom->getElementsByTag('a')[0]->title)->toEqual('This is a "test" of double quotes');
});

test('multiple single quotes', function (): void {
    $dom = new Dom();
    $dom->loadStr("<a title='Ain't this the best' href=\"http://www.example.com\">Hello</a>");

    expect($dom->getElementsByTag('a')[0]->title)->toEqual("Ain't this the best");
});

test('before closing tag', function (): void {
    $dom = new Dom();
    $dom->loadStr('<div class="stream-container "  > <div class="stream-item js-new-items-bar-container"> </div> <div class="stream">');

    expect((string) $dom)->toEqual('<div class="stream-container "> <div class="stream-item js-new-items-bar-container"> </div> <div class="stream"></div></div>');
});

test('code tag', function (): void {
    $dom = new Dom();
    $dom->loadStr('<strong>hello</strong><code class="language-php">$foo = "bar";</code>');

    expect((string) $dom)->toEqual('<strong>hello</strong><code class="language-php">$foo = "bar";</code>');
});

test('count children', function (): void {
    $dom = new Dom();
    $dom->loadStr('<strong>hello</strong><code class="language-php">$foo = "bar";</code>');

    expect($dom->countChildren())->toEqual(2);
});

test('get children array', function (): void {
    $dom = new Dom();
    $dom->loadStr('<strong>hello</strong><code class="language-php">$foo = "bar";</code>');
    
    expect($dom->getChildren())->toBeArray();
});

test('has children', function (): void {
    $dom = new Dom();
    $dom->loadStr('<strong>hello</strong><code class="language-php">$foo = "bar";</code>');

    expect($dom->hasChildren())->toBeTrue();
});

test('whitespace in text', function (): void {
    $dom = new Dom();
    $dom->setOptions((new Options())->setRemoveDoubleSpace(false));
    $dom->loadStr('<pre>    Hello world</pre>');

    expect((string) $dom)->toEqual('<pre>    Hello world</pre>');
});

test('get complex attribute', function (): void {
    $dom = new Dom();
    $dom->loadStr('<a href="?search=Fort+William&session_type=face&distance=100&uqs=119846&page=4" class="pagination-next">Next <span class="chevron">&gt;</span></a>');

    $href = $dom->find('a', 0)->href;
    expect($href)->toEqual('?search=Fort+William&session_type=face&distance=100&uqs=119846&page=4');
});

test('get complex attribute html special chars decode', function (): void {
    $dom = new Dom();
    $dom->setOptions((new Options())->setHtmlSpecialCharsDecode(true));
    $dom->loadStr('<a href="?search=Fort+William&amp;session_type=face&amp;distance=100&amp;uqs=119846&amp;page=4" class="pagination-next">Next <span class="chevron">&gt;</span></a>');

    $a = $dom->find('a', 0);
    expect($a->innerHtml)->toEqual('Next <span class="chevron">></span>');
    $href = $a->href;
    expect($href)->toEqual('?search=Fort+William&session_type=face&distance=100&uqs=119846&page=4');
});

test('get children no children', function (): void {
    $dom = new Dom();
    $dom->loadStr('<div>Test <img src="test.jpg"></div>');

    $imgNode = $dom->root->find('img');
    $children = $imgNode->getChildren();
    expect(\count($children) === 0)->toBeTrue();
});

test('infinite loop not happening', function (): void {
    $dom = new Dom();
    $dom->loadStr('<html>
                <head>
                <meta http-equiv="refresh" content="5; URL=http://www.example.com">
                <meta http-equiv="cache-control" content="no-cache">
                <meta http-equiv="pragma" content="no-cache">
                <meta http-equiv="expires" content="0">
                </head>
                <');

    $metaNodes = $dom->root->find('meta');
    expect(\count($metaNodes))->toEqual(4);
});

test('find order', function (): void {
    $str = '<p><img src="http://example.com/first.jpg"></p><img src="http://example.com/second.jpg">';
    $dom = new Dom();
    $dom->loadStr($str);

    $images = $dom->find('img');

    expect((string) $images[0])->toEqual('<img src="http://example.com/first.jpg" />');
});

test('case in sensitivity', function (): void {
    $str = "<FooBar Attribute='asdf'>blah</FooBar>";
    $dom = new Dom();
    $dom->loadStr($str);

    $FooBar = $dom->find('FooBar');
    expect($FooBar->getAttribute('attribute'))->toEqual('asdf');
});

test('case sensitivity', function (): void {
    $str = "<FooBar Attribute='asdf'>blah</FooBar>";
    $dom = new Dom();
    $dom->loadStr($str);

    $FooBar = $dom->find('FooBar');
    expect($FooBar->Attribute)->toEqual('asdf');
});

test('empty attribute', function (): void {
    $str = '<ul class="summary"><li class></li>blah<li class="foo">what</li></ul>';
    $dom = new Dom();
    $dom->loadStr($str);

    $items = $dom->find('.summary .foo');
    expect(\count($items))->toEqual(1);
});

test('inner text', function (): void {
    $html = <<<EOF
<body class="" style="" data-gr-c-s-loaded="true">123<a>456789</a><span>101112</span></body>
EOF;
    $dom = new Dom();
    $dom->loadStr($html);

    expect('123456789101112')->toEqual($dom->innerText);
});

test('multiple square selector', function (): void {
    $dom = new Dom();
    $dom->loadStr('<input name="foo" type="text" baz="fig">');

    $items = $dom->find('input[type=text][name=foo][baz=fig]');
    expect(\count($items))->toEqual(1);
});

test('not square selector', function (): void {
    $dom = new Dom();
    $dom->loadStr('<input name="foo" type="text" baz="fig">');

    $items = $dom->find('input[type!=foo]');
    expect(\count($items))->toEqual(1);
});

test('start square selector', function (): void {
    $dom = new Dom();
    $dom->loadStr('<input name="foo" type="text" baz="fig">');

    $items = $dom->find('input[name^=f]');
    expect(\count($items))->toEqual(1);
});

test('end square selector', function (): void {
    $dom = new Dom();
    $dom->loadStr('<input name="foo" type="text" baz="fig">');

    $items = $dom->find('input[baz$=g]');
    expect(\count($items))->toEqual(1);
});

test('star square selector', function (): void {
    $dom = new Dom();
    $dom->loadStr('<input name="foo" type="text" baz="fig">');

    $items = $dom->find('input[baz*=*]');
    expect(\count($items))->toEqual(1);
});

test('star full regex square selector', function (): void {
    $dom = new Dom();
    $dom->loadStr('<input name="foo" type="text" baz="fig">');

    $items = $dom->find('input[baz*=/\w+/]');
    expect(\count($items))->toEqual(1);
});

test('failed square selector', function (): void {
    $dom = new Dom();
    $dom->loadStr('<input name="foo" type="text" baz="fig">');

    $items = $dom->find('input[baz%=g]');
    expect(\count($items))->toEqual(1);
});

test('load get attribute with backslash', function (): void {
    $dom = new Dom();
    $dom->loadStr('<div><a href="/test/"><img alt="\" src="/img/test.png" /><br /></a><a href="/demo/"><img alt="demo" src="/img/demo.png" /></a></div>');

    $imgs = $dom->find('img', 0);
    expect($imgs->getAttribute('src'))->toEqual('/img/test.png');
});

test('25 children found', function (): void {
    $dom = new Dom();
    $dom->setOptions((new Options())->setWhitespaceTextNode(false));
    $dom->loadFromFile('tests/data/files/51children.html');

    $children = $dom->find('#red-line-g *');
    expect(\count($children))->toEqual(25);
});

test('html5 pageload str', function (): void {
    $dom = new Dom();
    $dom->loadFromFile('tests/data/files/html5.html');

    $div = $dom->find('div.d-inline-block', 0);
    expect($div->getAttribute('style'))->toEqual('max-width: 29px');
});

test('find attribute in both parent and child', function (): void {
    $dom = new Dom();
    $dom->loadStr('<parent attribute="something">
    <child attribute="anything"></child>
</parent>');

    $nodes = $dom->find('[attribute]');
    expect($nodes)->toHaveCount(2);
});

test('less than character in javascript', function (): void {
    $results = (new Dom())->loadStr('<html><head><script type="text/javascript">
            console.log(1 < 3);
        </script></head><body><div id="panel"></div></body></html>',
        (new Options())->setCleanupInput(false)
            ->setRemoveScripts(false)
        )->find('body');
    expect($results)->toHaveCount(1);
});

test('unique id for all objects', function (): void {
    // Create a dom which will be used as a parent/container for a paragraph
    $dom1 = new Dom();
    $dom1->loadStr('<div>A container div</div>');

    // Resets the counter (doesn't matter here as the counter was 0 even without resetting)
    $div = $dom1->firstChild();

    // Create a paragraph outside of the first dom
    $dom2 = new Dom();
    $dom2->loadStr('<p>Our new paragraph.</p>');

    // Resets the counter
    $paragraph = $dom2->firstChild();

    $div->addChild($paragraph);

    expect($div->innerhtml)->toEqual('A container div<p>Our new paragraph.</p>');
});

test('find descendants of match', function (): void {
    $dom = new Dom();
    $dom->loadStr('<p>
        <b>
            test
            <b>testing</b>
            <b>This is a test</b>
            <i>italic</i>
            <b>password123</b>
        </b>
        <i><b>another</b></i>
    </p>');

    $nodes = $dom->find('b');
    expect($nodes)->toHaveCount(5);
});

test('compatible with word press shortcode', function (): void {
    $dom = new Dom();
    $dom->loadStr('<p>
[wprs_alert type="success" content="this is a short code" /]
</p>');

    $node = $dom->find('p', 0);
    expect($node->innerHtml)->toEqual(' [wprs_alert type="success" content="this is a short code" /] ');
});

test('broken html', function (): void {
    $dom = new Dom();
    $dom->loadStr('<the thing broke itV');

    expect($dom->outerHtml)->toEqual('<the thing broke itv></the>');
});

test('xmlopening token', function (): void {
    $dom = new Dom();
    $dom->loadStr('<?xml version="1.0" encoding="UTF-8"?><p>fun time</p>');

    expect($dom->outerHtml)->toEqual('<?xml version="1.0" encoding="UTF-8" ?><p>fun time</p>');
});

test('random tag in middle of text', function (): void {
    $dom = new Dom();
    $dom->loadStr('<p>Hello, this is just a test in which <55 names with some other text > should be interpreted as text</p>');

    expect($dom->outerHtml)->toEqual('<p>Hello, this is just a test in which <55 names with some other text> should be interpreted as text</55></p>');
});

test('http call', function (): void {
    $dom = new Dom();
    $dom->loadFromUrl('http://google.com');

    expect($dom->outerHtml)->not->toBeEmpty();
});
