<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Damian Alejandro Fernandez Sosa <damlists@cnba.uba.ar>       |
// +----------------------------------------------------------------------+


//require_once '../IMAPProtocol.php';
require_once 'Net/IMAPProtocol.php';


/**
 * Provides an implementation of the IMAP protocol using PEAR's
 * Net_Socket:: class.
 *
 * @package Net_IMAP
 * @author  Damian Alejandro Fernandez Sosa <damlists@cnba.uba.ar>
 */
class Net_IMAP extends Net_IMAPProtocol {

    /**
     * Constructor
     *
     * Instantiates a new Net_SMTP object, overriding any defaults
     * with parameters that are passed in.
     *
     * @param string The server to connect to.
     * @param int The port to connect to.
     * @param string The value to give when sending EHLO or HELO.
     */
     
    function Net_IMAP($host = 'localhost', $port = 143)
    {
        $this->Net_IMAPProtocol();
        
        $ret = $this->cmdConnect( $host , $port );
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
    }
    
    
    
    
    
    
     /**
     * Attempt to connect to the IMAP server located at $host $port
     * @param string $host The IMAP server
     * @param string $port The IMAP port
     *
     *          It is only useful in a very few circunstances
     *          because the contructor already makes this job
     * @return true on success or PEAR_Error
     *
     * @access public
     * @since  1.0
     */   
    function connect($host, $port)
    {
        $ret=$this->cmdConnect($host,$port);
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
        return $ret;
    }
    
   
    
    
    
    
    
    
     
    
    /**
     * Attempt to authenticate to the IMAP server.
     * @param string $user The userid to authenticate as.
     * @param string $pass The password to authenticate with.
     * @param string $useauthenticate true: authenticate using
     *        the IMAP AUTHENTICATE command. false: authenticate using
     *        the IMAP AUTHENTICATE command. 'string': authenticate using
     *        the IMAP AUTHENTICATE command but using the authMethod in 'string'
     *
     *
     * @return true on success or PEAR_Error
     *
     * @access public
     * @since  1.0
     */
     
    function login($user, $pass, $useauthenticate = true)
    {
        if ( $useauthenticate ){
            //$useauthenticate is a string if the user hardcodes an AUTHMethod
            // (the user calls $imap->login("user","password","CRAM-MD5"); for example!
            
            $method = is_string( $useauthenticate ) ? $useauthenticate : null;
            
            //Try the selected Auth method
            if ( PEAR::isError( $ret = $this->cmdAuthenticate( $user , $pass , $method  ) ) ) {
                    if($this->_serverAuthMethods ==null ){
                    // The server does not have any auth method, so I try LOGIN
                        if ( PEAR::isError( $ret = $this->cmdLogin( $user, $pass ) ) ) {
                            return $ret;
                        }
                    }else{
                        return $ret;
                    }
            }
            if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
                return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
            }
        }else{
            //The user request "PLAIN"  auth, we use the login command
            if ( PEAR::isError( $ret = $this->cmdLogin( $user, $pass ) ) ) {
                return $ret;
            }
            if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
                return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
            }
        }
        //Select INBOX
        if ( PEAR::isError( $ret=$this->cmdSelect( $this->getCurrentMailbox() ) ) ) {
            return $ret;
        }
        
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
        
        return true;
    }
    
    
    
    
    
      
    
    
    
        
    /*
    * Disconnect function. Sends the QUIT command
    * and closes the socket.
    *
    * @return bool Success/Failure
    */
    function disconnect($expungeOnExit = false)
    {
        if($expungeOnExit){
            $ret=$this->cmdExpunge();
            if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
                $ret=$this->cmdLogout();
                return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
            }
        }
        $ret=$this->cmdLogout();
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
        return true;
    }
    
    
    
    
    
    

     /*
    * Changes  the default/current mailbox th $mailbox
    * 
    *
    * @return bool Success/Pear_Error Failure
    */
    function selectMailbox($mailbox)
    {
        $ret=$this->cmdSelect($mailbox);
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
        return true;
    }

    
    
    
    
        
    
     /*
    * Checks  the mailbox $mailbox
    * 
    *
    * @return bool Success/Pear_Error Failure
    */
    function examineMailbox($mailbox)
    {
        $ret=$this->cmdExamine($mailbox);
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
        
        //$ret_aux["EXISTS"]=$ret["PARSED"]["EXISTS"];
        //$ret_aux["RECENT"]=$ret["PARSED"]["RECENT"];
        return $ret;
    }

    
    
    
    
    
    
    
    /*
    * Returns the raw headers of the specified message.
    *
    * @param  $msg_id Message number
    * @return mixed   Either raw headers or false on error
    */
    function getRawHeaders($msg_id)
    {
        $ret=$this->cmdFetch($msg_id, "BODY[HEADER]");
        if(strtoupper( $ret["RESPONSE"]["CODE"]) != "OK" ){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
        $ret=$ret["PARSED"][0]["EXT"]["BODY[HEADER]"]["CONTENT"];
        return $ret;    
    }

    
    
    
    /*
    * Returns the  headers of the specified message in an
    * associative array. Array keys are the header names, array
    * values are the header values. In the case of multiple headers
    * having the same names, eg Received:, the array value will be 
    * an indexed array of all the header values.
    *
    * @param  $msg_id Message number
    * @return mixed   Either array of headers or false on error
    */
    function getParsedHeaders($msg_id)
    {       
            $ret=$this->getRawHeaders($msg_id);
            
            $raw_headers = rtrim($ret);
            $raw_headers = preg_replace("/\r\n[ \t]+/", ' ', $raw_headers); // Unfold headers
            $raw_headers = explode("\r\n", $raw_headers);
            foreach ($raw_headers as $value) {
                $name  = substr($value, 0, $pos = strpos($value, ':'));
                $value = ltrim(substr($value, $pos + 1));
                if (isset($headers[$name]) AND is_array($headers[$name])) {
                    $headers[$name][] = $value;
                } elseif (isset($headers[$name])) {
                    $headers[$name] = array($headers[$name], $value);
                } else {
                    $headers[$name] = $value;
                }
            }

            return $headers;
    }


    
    
    
    
    
    
    
    function getMessagesList($msg_id = null)
    {
        if( $msg_id != null){
            $message_set=$msg_id;
        }else{
            $message_set="1:*";
        }
    
        $ret=$this->cmdFetch($message_set,"(RFC822.SIZE UID)");
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
        foreach($ret["PARSED"] as $msg){
            $ret_aux[]=array("msg_id"=>$msg["NRO"],"size" => $msg["EXT"]["RFC822.SIZE"],"uidl"=> $msg["EXT"]["UID"]);
        }
        return $ret_aux;        
    }
    
    
    
    
    
    
    
    
    
    
    function getSummary($msg_id = null)
    {
    
        
        if( $msg_id != null){
            if(is_array($msg_id)){
                $message_set=$this->_getSearchListFromArray($msg_id);
            }else{
                $message_set=$msg_id;
            }
        }else{
            $message_set="1:*";
        }
        $ret=$this->cmdFetch($message_set,"(RFC822.SIZE UID FLAGS ENVELOPE INTERNALDATE)");
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
/*        foreach($ret["PARSED"] as $msg){
            $ret_aux[]=array("msg_id"=>$msg["NRO"],"size" => $msg["EXT"]["RFC822.SIZE"],"uidl"=> $msg["EXT"]["UID"]);
        }
        return $ret_aux;        
        */
        return $ret;        
    }
    
    
    
    
    
    
    
    /*
    * Returns the body of the message with given message number.
    *
    * @param  $msg_id Message number
    * @return mixed   Either message body or false on error
    */
    function getBody($msg_id)
    {
        $ret=$this->cmdFetch($msg_id,"BODY[TEXT]");
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
        $ret=$ret["PARSED"][0]["EXT"]["BODY[TEXT]"]["CONTENT"];
        //$ret=$resp["PARSED"][0]["EXT"]["RFC822"]["CONTENT"];
        return $ret; 
    }
    
    
    
    
    
    
    
    
    /*
    * Returns the entire message with given message number.
    *
    * @param  $msg_id Message number
    * @return mixed   Either entire message or false on error
    */
    function getMessages($msg_id = null, $indexIsMessageNumber=true)
    {
        //$resp=$this->cmdFetch($msg_id,"(BODY[TEXT] BODY[HEADER])");
        if( $msg_id != null){
            if(is_array($msg_id)){
                $message_set=$this->_getSearchListFromArray($msg_id);
            }else{
                $message_set=$msg_id;
            }
        }else{
            $message_set="1:*";
        }

        $ret=$this->cmdFetch($message_set,"RFC822");
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
        if(isset($ret["PARSED"])){
            foreach($ret["PARSED"] as $msg){
                if(isset($msg["EXT"]["RFC822"]["CONTENT"])){
                    if($indexIsMessageNumber){
                        $ret_aux[$msg["NRO"]]=$msg["EXT"]["RFC822"]["CONTENT"];
                    }else{
                        $ret_aux[]=$msg["EXT"]["RFC822"]["CONTENT"];
                    }
        }
            }
            return $ret_aux;
       }
       return array();
    }


    
    
    
    
    
    
    
    
    
    /*
    * Returns number of messages in this mailbox 
    *
    * @return mixed Either number of messages or Pear_Error on error
    */
    function getNumberOfMessages($mailbox = '')
    {
        if ( $mailbox == '' ){
            $mailbox=$this->getCurrentMailbox();
        }
        $ret=$this->cmdStatus( $mailbox , "MESSAGES" );
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
        if( isset($ret["PARSED"]["STATUS"]["ATTRIBUTES"]["MESSAGES"] ) ){
            if( !is_numeric( $ret["PARSED"]["STATUS"]["ATTRIBUTES"]["MESSAGES"] ) ){
                // if this array does not exists means that there is no messages in the mailbox
                return 0;
            }else{
                return $ret["PARSED"]["STATUS"]["ATTRIBUTES"]["MESSAGES"];
            }

        }
        return 0;
    }
    
    
    
    
    
    
    
    
    
    
    
    /*
    * Returns the sum of all the sizes of messages in $mailbox
    *           WARNING!!!  The method's performance is not good 
    *                       if you have a lot of messages in the mailbox
    *                       Use with care!                
    * @return mixed Either size of maildrop or false on error
    */
    function getMailboxSize($mailbox = '')
    {

        if ( $mailbox != '' && $mailbox != $this->getCurrentMailbox() ){
            // store the actual selected mailbox name
            $mailbox_aux = $this->getCurrentMailbox();
            if ( PEAR::isError( $ret = $this->selectMailbox( $mailbox ) ) ) {
                return $ret;
            }
        }

        $ret=$this->cmdFetch("1:*","RFC822.SIZE");
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
                // Restore the default mailbox if it was changed
                if ( $mailbox != '' && $mailbox != $this->getCurrentMailbox() ){
                    if ( PEAR::isError( $ret = $this->selectMailbox( $mailbox_aux ) ) ) {
                        return $ret;
                    }
                }
                // return 0 because the server says that there is no message in the mailbox
                return 0;
        }

        $sum=0;
        
        if(!isset($ret["PARSED"]) ){
            // if the server does not return a "PARSED"  part
            // we think that it does not suppoprt select or has no messages in it.
            return 0;
        }
        foreach($ret["PARSED"] as $msgSize){
            if( isset($msgSize["EXT"]["RFC822.SIZE"]) ){
                $sum+= $msgSize["EXT"]["RFC822.SIZE"];
            }
        }

        if ( $mailbox != '' && $mailbox != $this->getCurrentMailbox() ){
            // re-select the  mailbox
            if ( PEAR::isError( $ret = $this->selectMailbox( $mailbox_aux ) ) ) {
                return $ret;
            }
        }

        return $sum;    
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /*
    * Marks a message for deletion. Only will be deleted if the
    * disconnect() method is called.
    *
    * @param  $msg_id Message to delete
    * @return bool Success/Failure
    */
    function deleteMessages($msg_id = null)
    {
        /* As said in RFC2060...
        C: A003 STORE 2:4 +FLAGS (\Deleted)
                S: * 2 FETCH FLAGS (\Deleted \Seen)
                S: * 3 FETCH FLAGS (\Deleted)
                S: * 4 FETCH FLAGS (\Deleted \Flagged \Seen)
                S: A003 OK STORE completed
        */
        //Called without parammeters deletes all the messages in the mailbox
        // You can also provide an array of numbers to delete those emails
        if( $msg_id != null){
            if(is_array($msg_id)){
                $message_set=$this->_getSearchListFromArray($msg_id);
            }else{
                $message_set=$msg_id;
            }
        }else{
            $message_set="1:*";
        }

        
        $dataitem="+FLAGS.SILENT";
        $value="\Deleted";
        $ret=$this->cmdStore($message_set,$dataitem,$value);
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
        return true;
    }


    
    
    
    
    
    //$restriction_search==false means match any mailbox that begins with $mailbox_base
    //$restriction_search==true means match the mailbox that is equual to  $mailbox_base
    //$restriction_search== string match those mailboxes which name is $mailbox_base + $restriction_search
    
    
    function getMailboxes($mailbox_base  , $restriction_search = false)
    {
        if( !$restriction_search ){
            $restriction_search="*";
        }else
            if( $restriction_search == true )
                $restriction_search='';                
        
//        if( $mailbox_base == null )
//            $mailbox_base=$this->getCurrentMailbox();
        
        $ret = $this->cmdList($mailbox_base,$restriction_search);
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
        
        $ret_aux=array();
        if( isset($ret["PARSED"]) ){
            foreach( $ret["PARSED"] as $mbox ){
            //for($i=0;$i<count($ret["PARSED"]) ; $i++){ $mbox=$ret["PARSED"][$i];
            //If the folder has the \NoSelect atribute we don't put in the list 
            // it solves a bug in wu-imap that crash the IMAP server if we select that mailbox
                if( isset($mbox["EXT"]["LIST"]["NAME_ATTRIBUTES"]) ){
                    if( !in_array('\NoSelect',$mbox["EXT"]["LIST"]["NAME_ATTRIBUTES"]) ){
                        $ret_aux[]=$mbox["EXT"]["LIST"]["MAILBOX_NAME"];
                    }
                }
            }
        }
        return $ret_aux;
    }
    
    
    
    
    
    
    
    function mailboxExist($mailbox)
    {
        // true means do an exact match
        if( PEAR::isError( $ret = $this->getMailboxes(  $mailbox , true ) ) ){
            return $ret;
        }
        if( count( $ret ) > 0 ){
            return true;        
        }
        return false;
    }
    
    
    
    
    
    
    
    
    
    function createMailbox($mailbox)
    {
        $ret=$this->cmdCreate($mailbox);
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
        return true;
    }
    
    
    
    
    
    
    
    
    
    
    function deleteMailbox($mailbox)
    {
        $ret=$this->cmdDelete($mailbox);
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
        return true;
    }
    
    
    
    
    
    
    
    
    
    
    function renameMailbox($oldmailbox, $newmailbox)
    {
        $ret=$this->cmdRename($oldmailbox,$newmailbox);
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
        return true;
    }
    

    
    
    
    
    function subscribeMailbox($mailbox)
    {
        $ret=$this->cmdSubscribe($mailbox);
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
        return true;
    }
    
    

    
    
    
    function unsubscribeMailbox($mailbox)
    {
        $ret=$this->cmdUnsubscribe($mailbox);
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
        return true;
    }
    
    
    
    
    
    function listsubscribedMailboxes($mailbox_base, $mailbox_name)
    {
        $ret=$this->cmdLsub($mailbox_base, $mailbox_name);
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
        return true;
    }
    
    
    
    
    
    
    function copyMessages($message_set, $mailbox)
    {
        $ret=$this->cmdCopy($message_set,$mailbox);
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
        return true;
    }
    
    
    
    
    
    
    
    
    
    
    /******************************************************************
    **                                                               **
    **           FLAGS METHODS                                       **
    **                                                               **
    ******************************************************************/
    
    
    
    function getFlags($message_set)
    {
        $ret=$this->cmdFetch($message_set,"FLAGS");
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
        $flags=array();
        if(isset($ret["PARSED"])){
            foreach($ret["PARSED"] as $msg_flags){
                if(isset($msg_flags["EXT"]["FLAGS"])){
                    $flags[]=$msg_flags["EXT"]["FLAGS"];
                }
            }
        }
        return $flags;
    }
    
    
    
    
 
    
    function isSeen($message_nro)
    {
        if (PEAR::isError($resp=$this->getFlags($message_nro))) {
            return $resp;
        }
        if(isset($resp[0]) ){
            if(is_array($resp[0])){
                if(in_array("\\Seen" ,$resp[0]))
                    return true;
            }
        }
        return false;        
    }
    
    
    
    
    
    
    
    function isAnswered($message_nro)
    {
        if (PEAR::isError($resp=$this->getFlags($message_nro))) {
            return $resp;
        }
        if(isset($resp[0]) ){        
            if(is_array($resp[0])){
                if(in_array("\\Answered" ,$resp[0]))
                    return true;
            }
        }
        return false;        
    }
    
    
    
    
    
    
    function isFlagged($message_nro)
    {
        if ( PEAR::isError($resp=$this->getFlags( $message_nro ) ) ) {
            return $resp;
        }
        if(isset($resp[0]) ){        
            if( is_array( $resp[0] ) ){
                if( in_array( "\\Flagged" , $resp[0] ) )
                    return true;
            }
        }
        return false;        
    }

    
    
    
    
    
    
    function isDraft($message_nro)
    {
        if ( PEAR::isError($resp = $this->getFlags( $message_nro ) ) ) {
            return $resp;
        }
        if(isset($resp[0]) ){        
            if( is_array( $resp[0] ) ){
                if( in_array("\\Draft" ,$resp[0] ) )
                    return true;
            }
        }
        return false;        
    }
    
    
    
    
    
    
    
    

    function isDeleted($message_nro)
    {
        if ( PEAR::isError( $resp = $this->getFlags( $message_nro ) ) ) {
            return $resp;
        }
        if(isset($resp[0]) ){        
            if( is_array( $resp[0] ) ){
                if( in_array( "\\Deleted" , $resp[0] ) )
                    return true;
            }
        }
        return false;        
    }
    
    
    
    

    
    
    
    
    
    
    
    
    
        
    
    
        
    function appendMessage($rfc_message, $mailbox = null )
    {
    
        if($mailbox == null){
            $mailbox = $this->getCurrentMailbox();        
        }
        $ret=$this->cmdAppend($mailbox,$rfc_message);
        if(strtoupper($ret["RESPONSE"]["CODE"]) != "OK"){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }

        return true;    
    }
    
    
    
    
    
    
    /*
    * expunge function. Sends the EXPUNGE command
    * 
    *
    * @return bool Success/Failure
    */
    function expunge()
    {
        $ret = $this->cmdExpunge();
        if( strtoupper( $ret["RESPONSE"]["CODE"]) != "OK" ){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
        return true;
    }

   
    
    
    
    
    
    /*
    * search function. Sends the SEARCH command
    * 
    *
    * @return bool Success/Failure
    */
    function search($search_list)
    {
        $ret = $this->cmdSearch($search_list);
        if( strtoupper( $ret["RESPONSE"]["CODE"]) != "OK" ){
            return new PEAR_Error($ret["RESPONSE"]["CODE"] . ", " . $ret["RESPONSE"]["STR_CODE"]);
        }
        
        return $ret["PARSED"]["SEARCH"]["SEARCH_LIST"];
    }

   
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /*****************************************************
        Net_POP3 Compatibility functions:
        
        Warning!!! 
            Those functions could dissapear in the future
       
    *********************************************************/
    
    
    
    
    function getSize(){
        return $this->getMailboxSize();    
    }
    
    
    function numMsg($mailbox= null){
        return $this->getNumberOfMessages($mailbox);    
    }
    

        
    /*
    * Returns the entire message with given message number.
    *
    * @param  $msg_id Message number
    * @return mixed   Either entire message or false on error
    */
    function getMsg($msg_id)
    {
        $ret=$this->getMessages($msg_id,false);
        // false means that getMessages() must not use the msg number as array key
        if(isset($ret[0])){
            return $ret[0];
        }else{
            return $ret;
        }
        
    }
    
    
    function getListing($msg_id = null)
    {
        return $this->getMessagesList($msg_id);
    }
    
    
    function deleteMsg($msg_id){
        return $this->deleteMessages($msg_id);
    }
    
    
     
    /*
    *   Transform an array to a list to be used in the cmdFetch method
    *
    */    
    function _getSearchListFromArray($arr){

        $txt=implode(',' , $arr);
        return $txt;
    }




    
    
    
    
    
    
    
    
    
    
    
}
?>
