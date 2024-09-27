<?php


namespace PHPHtmlParser\Enum;

/**
 * @method static StringToken BLANK()
 * @method static StringToken EQUAL()
 * @method static StringToken SLASH()
 * @method static StringToken ATTR()
 * @method static StringToken CLOSECOMMENT()
 */
enum StringToken: string
{
    case BLANK = " \t\r\n";
    case EQUAL = ' =/>';
    case SLASH = " />\r\n\t";
    case ATTR = ' >';
    case CLOSECOMMENT = '-->';
}
