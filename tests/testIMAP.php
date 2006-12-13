<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * Unit test for Net_IMAP  IMAP.php
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
 * We are testing IMAP.php
 */
require_once 'Net/IMAP.php';

/**
 * Use PHPUnit for testing
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Connection settings
 */
define('HOST',  'localtest');
define('PORT',  '143');
define('USER',  'testuser');
define('PASS',  'testpass');

/**
 * The test class
 */
class testIMAP extends PHPUnit_Framework_TestCase 
{
    // contains the object handle of the Net_IMAP class
    protected $fixture;

    private $mailboxnames;

    protected function setUp()
    {
        $this->fixture = new Net_IMAP();

        // some mailboxnames to test with
        $this->mailboxnames = array();
        $this->mailboxnames[] = 'INBOX/testcase';
        $this->mailboxnames[] = 'INBOX/umlautsöäüßÖÄÜ';

        // ToDo: insert some mails
        // $this->messages['test_mail1'] = file_get_contents('mails1.mbox');
        // $this->messages['test_mail2'] = file_get_contents('mails2.mbox');
        // $this->messages['test_mail3'] = file_get_contents('mails3.mbos');
    }

    protected function tearDown()
    {
        // delete all mailboxes except INBOX
        $mailboxes = $this->fixture->getMailboxes();
        foreach ($mailboxes as $mailbox) {
            if ($mailbox == 'INBOX') {
                continue;
            }
            $this->fixture->deleteMailbox($mailbox);
        }

        // delete instance
        unset($this->fixture);
    }

    protected function login()
    {
        $result = $this->fixture->connect(HOST, PORT);
        $this->assertTrue(!PEAR::isError($result), 'Can not connect');
        $result = $this->fixture->login(USER, PASS);
        $this->assertTrue(!PEAR::isError($result), 'Can not log in');
    }

    protected function logout()
    {
        $result = $this->fixture->disconnect();
        $this->assertTrue(!PEAR::isError($result), 'Error on disconnect');
    }

    public function testConnect()
    {
        $result = $this->fixture->connect(HOST, PORT);
        $this->assertTrue(!PEAR::isError($result), 'Can not connect');
    }

    public function testLogin()
    {
        $result = $this->fixture->connect(HOST, PORT);
        $this->assertTrue(!PEAR::isError($result), 'Can not connect');
        $result = $this->fixture->login(USER, PASS);
        $this->assertTrue(!PEAR::isError($result), 'Can not login');
    }

    public function testDisconnect()
    {
        $result = $this->fixture->connect(HOST, PORT);
        $this->assertTrue(!PEAR::isError($result), 'Can not connect');
        $result = $this->fixture->login(USER, PASS);
        $this->assertTrue($result, 'Can not login');
        $result = $this->fixture->disconnect();
        $this->assertTrue(!PEAR::isError($result), 'Error on disconnect');
    }

    public function testCreateMailbox()
    {
        $this->login();
        $mailboxes = $this->fixture->getMailboxes();
        foreach ($this->mailboxnames as $mailbox) {
            if ($mailbox == 'INBOX') {
                continue;
            }
            $this->fixture->deleteMailbox($mailbox);
        }
        foreach ($this->mailboxnames as $mailbox) {
            if ($mailbox == 'INBOX') {
                continue;
            }
            $result = $this->fixture->createMailbox($mailbox);
            $this->assertTrue($result, 'Can not create mailbox '.$mailbox);
        }
    }

    public function testListsubscribedMailboxes()
    {
        $this->login();
        $subscribed = $this->fixture->listsubscribedMailboxes();
        $this->logout();
        
        $this->assertTrue(!PEAR::isError($subscribed), 'Can not list subscribed mailboxes');
    }
        

    public function testGetMailboxes()
    {
        $this->login();
        $mailboxes = $this->fixture->getMailboxes();
        $this->logout();

        $this->assertTrue(!PEAR::isError($mailboxes), 'Can not list mailboxes');
    }

    public function testSelectMailbox()
    {
        $this->login();
        $mailboxes = $this->fixture->getMailboxes();
        foreach ($mailboxes as $mailbox) {
            $result = $this->fixture->selectMailbox($mailbox);
            $this->assertTrue($result, 'Can not select mailbox '.$mailbox);
        }
    }

    public function testRenameMailbox()
    {
        $this->login();
        $mailboxes = $this->fixture->getMailboxes();
        foreach ($mailboxes as $mailbox) {
            if ($mailbox == 'INBOX') {
                continue;
            }
            $result = $this->fixture->renameMailbox($mailbox, $mailbox.'renamed');
            $this->assertTrue(!PEAR::isError($result), 'Can not rename mailbox '.$mailbox);
        }
        // ToDo: get new list and check if rename works well
    }

}

?>
