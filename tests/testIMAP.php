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
require_once 'settings.php';

/**
 * The test class
 */
class testIMAP extends PHPUnit_Framework_TestCase 
{
    // contains the object handle of the Net_IMAP class
    protected $fixture;

    private $delimiter;

    private $reservedFolders;

    private $mailboxNames;

    function testIMAP() {
        $conn = new Net_IMAP(HOST, PORT);
        // we need to login for getting the delimiter
        $conn->login(USER, PASS);
        if (PEAR::isError($this->delimiter = $conn->getHierarchyDelimiter())) {
            echo 'Can not get hierarchy delimiter';
            exit;
        }
        $conn->disconnect();

        $this->reservedFolders = array( 'INBOX',
                                        'INBOX'.$this->delimiter.'Trash');
    }

    protected function setUp()
    {
        $this->fixture = new Net_IMAP();

        // some mailboxnames to test with
        $this->mailboxNames = array();
        $this->mailboxNames[] = 'INBOX'.$this->delimiter.'testcase';
        $this->mailboxNames[] = 'INBOX'.$this->delimiter.'umlautsöäüßÖÄÜ';

        // ToDo: insert some mails
        // $this->messages['test_mail1'] = file_get_contents('mails1.mbox');
        // $this->messages['test_mail2'] = file_get_contents('mails2.mbox');
        // $this->messages['test_mail3'] = file_get_contents('mails3.mbox');
    }

    protected function tearDown()
    {
        // delete all mailboxes except INBOX
        $mailboxes = $this->fixture->getMailboxes();
        foreach ($mailboxes as $mailbox) {
            if (in_array($mailbox, $this->reservedFolders)) {
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



    ///
    /// connection tests
    ///

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



    ///
    /// mailbox tests
    ///

    public function testCreateMailbox()
    {
        $this->login();
        $mailboxes = $this->fixture->getMailboxes();
        foreach ($this->mailboxNames as $mailbox) {
            if (in_array($mailbox, $this->reservedFolders)) {
                continue;
            }
            $this->fixture->deleteMailbox($mailbox);
        }
        foreach ($this->mailboxNames as $mailbox) {
            if ($mailbox == 'INBOX') {
                continue;
            }
            $result = $this->fixture->createMailbox($mailbox);
            $this->assertTrue($result, 'Can not create mailbox '.$mailbox);
        }
        // print_r($this->fixture->getMailboxes());
        $this->logout();
    }

    public function testGetMailboxes()
    {
        $this->login();
        $mailboxes = $this->fixture->getMailboxes();
        // print_r($mailboxes);
        $this->logout();

        $this->assertTrue(!PEAR::isError($mailboxes), 'Can not list mailboxes');
    }

    public function testSelectMailbox()
    {
        $this->login();
        $mailboxes = $this->fixture->getMailboxes();
        foreach ($mailboxes as $mailbox) {
            $result = $this->fixture->selectMailbox($mailbox);
            $this->assertTrue(!PEAR::isError($result), 'Can not select mailbox '.$mailbox);
        }
        $this->logout();
    }

    // examineMailbox needs some messages for testing

    public function testRenameMailbox()
    {
        $this->login();
        $mailboxes = $this->fixture->getMailboxes();
        // print_r($mailboxes);
        foreach ($mailboxes as $mailbox) {
            if (in_array($mailbox, $this->reservedFolders)) {
                continue;
            }
            $result = $this->fixture->renameMailbox($mailbox, $mailbox.'renamed');
            $this->assertTrue(!PEAR::isError($result), 'Can not rename mailbox '.$mailbox);
        }
        $mailboxes_new = $this->fixture->getMailboxes();
        for ($i=0; $i < sizeof($mailboxes_new); $i++) {
            if (in_array($mailboxes[$i], $this->reservedFolders)) {
                continue;
            }
            $this->assertTrue(($mailboxes[$i].'renamed' == $mailboxes_new[$i]), 'Mailbox '.$mailboxes[$i].' not renamed');
        }
        // print_r($mailboxes_new);

        $this->logout();
    }

    public function testMailboxExist()
    {
        $this->login();
        // this mailbox name must not exist before
        $mailboxname = 'INBOX'.$this->delimiter.'testMailboxExistöäüß';
        
        $result = $this->fixture->mailboxExist($mailboxname);
        $this->assertFalse($result, 'Mailbox should NOT exist');

        $result = $this->fixture->createMailbox($mailboxname);
        $this->assertTrue(!PEAR::isError($result), 'Can not create mailbox');
        
        $result = $this->fixture->mailboxExist($mailboxname);
        $this->assertTrue($result, 'Mailbox should exist');

        $this->logout();
    }

    public function testDeleteMailbox()
    {
        $this->login();
        $mailboxes = $this->fixture->getMailboxes();
        // print_r($mailboxes);
        foreach ($mailboxes as $mailbox) {
            if (in_array($mailbox, $this->reservedFolders)) {
                continue;
            }
            $result = $this->fixture->deleteMailbox($mailbox);
            $this->assertTrue(!PEAR::isError($result), 'Can not delete mailbox '.$mailbox);
        }
        // print_r($this->fixture->getMailboxes());

        $this->logout();
    }

    public function testGetMailboxSize()
    {
        $this->login();
        $mailboxes = $this->fixture->getMailboxes();
        foreach ($mailboxes as $mailbox) {
            $result = $this->fixture->getMailboxSize($mailbox);
            // print_r($result);
            $this->assertTrue(!PEAR::isError($result), 'Can not get size for mailbox '.$mailbox);
        }

        $this->logout();
    }



    ///
    /// suscribing tests
    ///

    public function testSubscribeMailbox()
    {
        $this->login();
        $this->fixture->createMailbox('INBOX'.$this->delimiter.'testSubscribe');
        $result = $this->fixture->subscribeMailbox('INBOX'.$this->delimiter.'testSubscribe');
        $this->assertTrue(!PEAR::isError($result), 'Can not subscribe Mailbox');

        $this->logout();
    }

    public function testListsubscribedMailboxes()
    {
        $this->login();
        $this->fixture->createMailbox('INBOX'.$this->delimimter.'testSubscribe');
        $this->fixture->subscribeMailbox('INBOX');
        $this->fixture->subscribeMailbox('INBOX'.$this->delimiter.'testSubscribe');
        $subscribed = $this->fixture->listsubscribedMailboxes();
        //print_r($subscribed);
        $this->logout();
        
        $this->assertTrue(!PEAR::isError($subscribed), 'Can not list subscribed mailboxes');
    }

    public function testUnsubscribeMailbox()
    {
        $this->login();
        $subscribed = $this->fixture->listsubscribedMailboxes();
        // print_r($subscribed);
        foreach ($subscribed as $mailbox) {
            if ($mailbox == 'INBOX') {
                continue;
            }
            $result = $this->fixture->unsubscribeMailbox($mailbox);
            $this->assertTrue(!PEAR::isError($result), 'Can not unsubscribe mailbox '.$mailbox);
        }
        // print_r($this->fixture->listsubscribedMailboxes());

        $this->logout();
    }

}

?>
