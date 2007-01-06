<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * Unit test for Net_IMAP
 *
 * PHP version 5
 *
 * LICENSE:  GPL.
 *
 * @category    Net
 * @package     Net_IMAP
 * @author      Sebastian Ebling <hudeldudel@php.net>
 * @copyright   2006 Sebastian Ebling
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     CVS: $Id$
 * @link        http://pear.php.net/package/Net_IMAP/
 */

/**
 * We use PHPUnit for testing
 */
require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';


/**
 * Include testcases
 */
require_once 'testIMAP.php';
// include more testcases here

/**
 * Run all tests
 * Run with "phpunit testAll" from comand line
 */
class testAll 
{
    public static function main() 
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite() 
    {
        $suite = new PHPUnit_Framework_TestSuite('Net_IMAP TestSuite');
        $suite->addTestSuite('testIMAP');
        // add more testcases here
        return $suite;
    }
}
