<?php


use PHPHtmlParser\DTO\Selector\RuleDTO;
use PHPHtmlParser\Selector\Seeker;

test('seek return empty array', function (): void {
    $ruleDTO = RuleDTO::makeFromPrimitives(
        'tag',
        '=',
        null,
        null,
        false,
        false
    );
    $seeker = new Seeker();
    $results = $seeker->seek([], $ruleDTO, []);
    expect($results)->toHaveCount(0);
});
