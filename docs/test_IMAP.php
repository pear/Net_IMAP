<?
include_once('../IMAP.php');
//include_once('Net/IMAP.php');
error_reporting(E_ALL);
$user="user";
$passwd="password";
$host="localhost";
$port="143";


require_once("../passwords.php");

$imap= new  Net_IMAP($host,$port);


//The the Protocol debug feature
$imap->setDebug();
//$imap->setUnparsedResponse(true);

//$imap->cmdCapability();
// Login to the IMAP Server Using plain passwords ($authMethod=false)
// $authMethod can be true (dafault) , false or a string

$authMethod=false;
if ( PEAR::isError( $ret = $imap->login( $user , $passwd , $authMethod ) ) ) {
    echo "Unable to login! reason:" . $ret->getMessage() . "\n";
    exit();
}






/*********************
***
    Let's show the Mailbox  related methods
***
*********************/
$mailboxes=$imap->getMailboxes('');

//print_r($mailboxes);
//$mailboxes=0;
if( count($mailboxes) > 0 ){

    echo "You have " . count($mailboxes) . " Mailboxes\n\n";


    echo "Here is the mailbox status:\n\n";
    echo "+-----------------------------------------------------------------------------+\n";
    echo "|Mailbox                                           | Mbox Size  | Cant Mesages|\n";
    echo "+-----------------------------------------------------------------------------+\n";

    foreach($mailboxes as $mailbox){

        if ( PEAR::isError( $mbox_size =$imap->getMailboxSize( $mailbox  ) ) ){
            //echo "Unable to retr msg size" . $mbox_size->getMessage() . "|\n";
            $mbox_size="[ERROR]";
        }
        //print_r($mbox_size);
        if ( PEAR::isError( $num_messages = $imap->getNumberOfMessages( $mailbox  ) ) ){
            //echo "Unable to rert msg" . $num_messages->getMessage() . "|\n";
            $num_messages="[ERROR]";
        }


        echo "|";
        echo $mailbox;
        // Align the output
        for($i=strlen($mailbox) ; $i< 50 ; $i++)  echo ' ';
        echo "|";
        // Align the output
        for($i=strlen($mbox_size) ; $i< 12 ; $i++)  echo ' ';
        echo $mbox_size;
        
        
        echo "|";
        // Align the output
        for($i=strlen($num_messages) ; $i< 13 ; $i++)  echo ' ';
        echo $num_messages;

        
        
        echo "|";
        echo "\n";

    }
    echo "+-----------------------------------------------------------------------------+\n";
}else{
    echo "Warning!:\n   You have any mailboxes!!\n";
}


echo "PITERROR|" . print_r( $imap->_socket->eof()) . "|\n";

//echo $imap->getDebugDialog();
exit();



$folder_delim=$imap->getHierachyDelimiter();
echo "Folder Delim:|$folder_delim|\n";
$mailbox='Mail'.$folder_delim .'INBOX2';






echo "creating mailbox $mailbox ....";

if( ! $ret = $imap->createMailbox($mailbox) ){
    echo "\nCan't create the mailbox '$mailbox' because " . $ret->getMessage() . "\n";
} else{
    echo "OK!\n";
}




echo "\n\n\n+-----------------------------------------------------------------------------+\n";





//$mailbox='INBOX.INBOX2';



//print_r($imap->cmdList("","*"));



//$mailbox='INBOX'.$folder_delim .'INBOX2';
if( $imap->mailboxExist($mailbox) ){
    echo "The mailbox $mailbox exists\n";
}else{
    echo "The mailbox $mailbox don't exists!\n";
}








$email="From: <damian@cnba.uba.ar>\r\n";
$email.="To: <damian@localhost>\r\n";
$email.="Subject: Test\r\n";
$email.="\r\n";
$email.="\r\n";
$email.="test\r\n";



//$imap->cmdAppend("inbox",$email);










echo "renaming mailbox INBOX2 to INBOX3 : <br>\n";
$imap->renameMailbox('INBOX2', 'INBOX3');





echo "deleting mailbox INBOX3 : <br>\n";
$imap->deleteMailbox('INBOX3');
//echo 'deleting msg 1  : <br>\n';
//$imap->delete(1);
echo "creating mailbox TESTING : <br>\n";
$imap->createMailbox('TESTING');
echo "copying msg 1 INBOX to TESTING :<br>\n";
$imap->copyMessages(1, 'TESTING');








// Get the raw headers of message 1
echo "<h2>getRawHeaders()</h2>\n";
echo "<pre>" . htmlspecialchars($imap->getRawHeaders(1)) . "</pre>\n";


//* Get structured headers of message 1
echo "<h2>getParsedHeaders()</h2> <pre>\n";
print_r($imap->getParsedHeaders(1));
echo "</pre>\n";



//* Get body of message 1
echo "<h2>getBody()</h2>\n";
echo "<pre>" . htmlspecialchars($imap->getBody(1)) . "</pre>\n";



//* Get entire message
echo "<h2>getMsg()</h2>\n";
echo "<pre>" . htmlspecialchars($imap->getMsg(1)) . "</pre>\n";


//* Get number of messages in maildrop
echo "<h2>getNumMsg</h2>\n";
echo "<pre>" . $imap->numMsg() . "</pre>\n";


//* Get entire message
echo "<h2>getMsg()</h2>\n";
echo "<pre>" . htmlspecialchars($imap->getMsg(1)) . "</pre>\n";


//* Get listing details of the maildrop
echo "<h2>getListing()</h2>\n";
echo "<pre>\n";
print_r($imap->getListing());
echo "</pre>\n";


//* Get size of maildrop
echo "<h2>getSize()</h2>\n";
echo "<pre>" . $imap->getSize() . "</pre>\n";




//* Delete a msg

//echo '<h2>delete()</h2>\n';
// Use with CARE!!!
//echo '<pre>' . $imap->deleteMsg(1) . '</pre>\n';



$mailbox="inbox";

$imap->selectMailbox($mailbox);

$nummsg=$imap->numMsg();
echo "You have $nummsg in $mailbox folder\n";
//echo "See header in message number 1: " . $imap->top(1) . '<br>';
echo "See header in message number 1: " . htmlspecialchars($imap->getRawHeaders(1)) . '<br>\n';

echo "Read message number 1: " . htmlspecialchars($imap->getMsg(1)) . '<br>\n';



for($i=1; $i<=$nummsg;$i++) {
    if ($imap->isSeen($i)) {
        echo "message $i has been read before...<br>\n";
        //$msg = $imap->getMsg($i);
        #echo $msg;
	}
   if ($imap->isFlagged($i)) {
        echo "message $i has been Flagged...<br>\n";
        //$msg = $imap->getMsg($i);
        #echo $msg;
	}
   if ($imap->isDeleted($i)) {
        echo "message $i is marked as Deleted...<br>\n";
        //$msg = $imap->getMsg($i);
        #echo $msg;
	}

    
}

//print_r($imap->getMailboxes(''));

echo "creating mailbox INBOX2 : <br>\n";
$imap->createMailbox('INBOX2');
echo "renaming mailbox INBOX2 to INBOX3 : <br>\n";
$imap->renameMailbox('INBOX2', 'INBOX3');

echo "deleting mailbox INBOX3 : <br>\n";
$imap->deleteMailbox('INBOX3');
//echo 'deleting msg 1  : <br>\n';
//$imap->delete(1);
echo "creating mailbox TESTING : <br>\n";
$imap->createMailbox('TESTING');
echo "copying msg 1 INBOX to TESTING :<br>\n";
$imap->copyMessages(1, 'TESTING');


//* Disconnect

$imap->disconnect();


?>
