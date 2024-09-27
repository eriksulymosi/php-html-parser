<?php


namespace PHPHtmlParser\Discovery;

use PHPHtmlParser\Contracts\Dom\CleanerInterface;
use PHPHtmlParser\Dom\Cleaner;

class CleanerDiscovery
{
    private static ?Cleaner $parser = null;

    public static function find(): CleanerInterface
    {
        if (self::$parser == null) {
            self::$parser = new Cleaner();
        }

        return self::$parser;
    }
}
