<?
//require_once('../IMAPProtocol.php');
require_once('Net/IMAPProtocol.php');


$user="user";
$passwd="password";
$host="localhost";
$port="143";


//require_once("../passwords.php");


$a= new  Net_IMAPProtocol();

//$a->setDebug(true);
$a->setUnparsedResponse(true);
//$a->setUnparsedResponse(false);

$aaa=$a->cmdConnect($host,$port);

//$aaa=$a->cmdAuthenticate($user,$passwd, "CRAM-MD5");
$aaa=$a->cmdLogin($user,$passwd);
//$aaa=$a->login($user,$passwd);

//$aaa=$a->cmdSelect("user.damian");
$aaa=$a->cmdSelect("inbox");
//$aaa=$a->cmdExamine("inbox");

//Returns the Auth Methods the IMAP server Has
print_r($aaa=$a->getServerAuthMethods());


//$aaa=$a->cmdFetch("2","(BODY[HEADER] BODY[TEXT])");
//$aaa=$a->cmdFetch("1","BODY[HEADER]");
//$aaa=$a->cmdFetch("15","FULL");
//$aaa=$a->cmdFetch("1:3","(FLAGS RFC822.SIZE UID ENVELOPE INTERNALDATE)");
//$aaa=$a->cmdFetch("1","(FLAGS RFC822.SIZE UID ENVELOPE INTERNALDATE)");
//$aaa=$a->cmdFetch("1:3","BODY[HEADER.FIELDS (References)]");
//$aaa=$a->cmdFetch("1","(UID RFC822.SIZE)");
//$aaa=$a->cmdFetch("1:10","RFC822.SIZE");
//$aaa=$a->cmdFetch("1:10","INTERNALDATE");
//$aaa=$a->cmdFetch("2:6","BODY[TEXT]");
//$aaa=$a->cmdFetch("1:3","(FLAGS)");
//$aaa=$a->cmdFetch("26:32","BODY");
//$aaa=$a->cmdFetch("26:29","BODY");
//$aaa=$a->cmdFetch("1","RFC822");
//$aaa=$a->cmdFetch("28","BODY");
//$aaa=$a->cmdFetch("1","BODYSTRUCTURE");
//$aaa=$a->cmdFetch("27","BODYSTRUCTURE");
//$aaa=$a->cmdFetch("1:100","BODYSTRUCTURE");
//$aaa=$a->cmdFetch("2:10","(RFC822.SIZE FLAGS INTERNALDATE)");
//$aaa=$a->cmdFetch("1:10","INTERNALDATE");
//$aaa=$a->cmdFetch("1","ENVELOPE");
//$aaa=$a->cmdFetch("10,9:16","FLAGS");
//$aaa=$a->cmdFetch("10","BODY[TEXT]");
//$aaa=$a->cmdFetch("10","RFC822.HEADER");
//$aaa=$a->cmdFetch("10","RFC822.TEXT");
//$aaa=$a->cmdFetch("10","BODYSTRUCTURE");
//$aaa=$a->cmdFetch("10","RFC822.HEADER");
//$aaa=$a->cmdFetch("1:4","(BODY[HEADER] FLAGS RFC822.SIZE INTERNALDATE)");
//$aaa=$a->cmdFetch("1:4","(FLAGS RFC822.SIZE INTERNALDATE)");
//$aaa=$a->cmdFetch("10","BODY[1]");
//$aaa=$a->cmdFetch("1","RFC822.SIZE");
//$aaa=$a->cmdFetch("10","ENVELOPE");
//$aaa=$a->cmdFetch("10","RFC822");
//$aaa=$a->cmdFetch("10","ENVELOPE");
//$aaa=$a->cmdFetch("1:30","(RFC822.SIZE FLAGS)");
//$aaa=$a->cmdFetch("1:30","FLAGS");
//print_r($aaa=$a->cmdFetch(32,"FLAGS"));
//print_r($aaa=$a->cmdFetch("1:3","(FLAGS RFC822.SIZE UID ENVELOPE INTERNALDATE)"));
//print_r($aaa=$a->cmdFetch("1","(FLAGS RFC822.SIZE UID ENVELOPE INTERNALDATE)"));
//print_r($aaa=$a->cmdFetch("10","ENVELOPE"));
//print_r($aaa=$a->cmdFetch("10","FLAGS"));
//$aaa=$a->cmdUidFetch("1","FLAGS");


//print_r($aaa=$a->cmdCapability());

print_r($aaa=$a->cmdStore("1:3","+FLAGS","\Deleted"));

print_r($a->cmdExpunge());

//print_r($aaa=$a->cmdStatus("inbox","MESSAGES UNSEEN"));

//print_r($aaa=$a->cmdCheck());

//print_r($aaa=$a->cmdClose());

//print_r($aaa=$a->cmdNoop());

//print_r($aaa=$a->cmdRename("inbox.test2","inbox.test3"));

//print_r($aaa=$a->cmdSubscribe("inbox.test1"));

















//print_r($aaa=$a->cmdUnsubscribe("inbox.test1"));

//print_r($aaa=$a->cmdList("","*"));

//print_r($aaa=$a->cmdLsub("*","*"));

//print_r($aaa=$a->cmdSearch("ALL"));

//print_r($aaa=$a->cmdCopy("1","inbox.test1"));

//print_r($aaa=$a->cmdGetQuota("user.montoto"));

//print_r($aaa=$a->cmdMyRights("inbox"));

//print_r($aaa=$a->cmdListRights("inbox","montoto"));

//print_r($aaa=$a->cmdGetACL("user.montoto"));

//print_r($aaa=$a->cmdSetQuota("user.montoto","500000"));

//print_r($aaa=$a->cmdCheck());

//print_r($aaa=$a->cmdCreate("inbox.inbox3"));


//print_r($aaa=$a->cmdUidSearch("ALL"));


    
//print_r($a->cmdGetQuotaRoot("user.montoto"));

//print_r($a->cmdGetQuota("user.montoto"));

//print_r($a->cmdListRights("user.montoto","montoto"));

//print_r($a->cmdMyRights("user.montoto"));

//print_r($a->cmdGetACL("user.montoto"));



//print_r($a->cmdSearch("ALL"));

//print_r($a->cmdUidSearch("ALL"));

//print_r($a->cmdStatus("inbox","MESSAGES UNSEEN"));

//print_r($a->_serverSupportedCapabilities);//


//print_r($aaa);
$aaa=$a->cmdLogout();
//print_r($aaa);

?>