<?php
//
// Created on: <09-Jan-2006 02:25:03 oms>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// COPYRIGHT NOTICE: Copyright (C) 1999-2006 eZ systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

/*! \file check_bounce.php
*/

$extension = 'eznewsletter';

define( 'EZSOFTBOUNCE', 0 );
define( 'EZHARDBOUNCE', 1 );
define( 'EZNOBOUNCE',   2 );

// Fallback bounce identification
$bounce = array();
$bounce[450] = EZHARDBOUNCE;
$bounce[451] = EZSOFTBOUNCE;
$bounce[500] = EZSOFTBOUNCE;
$bounce[503] = EZSOFTBOUNCE;
$bounce[540] = EZHARDBOUNCE;
$bounce[550] = EZHARDBOUNCE;
$bounce[554] = EZHARDBOUNCE;

$cli = eZCLI::instance();

if( !extension_loaded( 'imap' ) )
{
    $cli->error( "The PHP IMAP-extension is required. Quitting." );
    return;
}

/*!
  Find bounce type
 */ 
function findBounceType( $errorcode ) {
    // Bounce status defined in RFC 1893
    // Format success.category.detail

    // Has to be changed to ini based setting in future
    if( strpos( $errorcode, "." ) )
    { 
        $dsn = explode( ".", $errorcode );
        switch( $dsn[0] ) {
            case 2:
                return EZNOBOUNCE; // Its no bounce just success message
            case 4:
                return EZSOFTBOUNCE;
        }
        switch( $dsn[1] ) {
            case 0:
            case 5:
            case 6:
            case 7:
                return EZHARDBOUNCE;
        }
        if( 1 == $dsn[1] && 5 != $dsn[2] ) {
            return EZHARDBOUNCE;
        }

        if( 2 == $dsn[1] && ( 0 != $dsn[2] || 1 != $dsn[2] ) ) {
            return EZHARDBOUNCE;
        }
        
        if( 3 == substr( $errorcode, 2, 1 ) ) {
            return EZSOFTBOUNCE;
        }

        if( 4 == substr( $errorcode, 2, 1 ) ) {
            return EZSOFTBOUNCE;
        }
    
    }
    else
    {
        // No valid status found using fallback to determine bounce
        if ( in_array( $errorcode, array_keys( $bounce ) ) )
        {
            return $bounce[$errorcode];
        } 
        else 
        {
            return EZSOFTBOUNCE; // No bounce status detected
        }
    }

    return EZSOFTBOUNCE; // Softbounce unknown status
}


/*!
  Fetches delivery status message information.
 */
function getDeliveryStatusType( $mbox, $mailNumber, $struct, $partNumber )
{
    $counter = 1;
    $errorCode = false;
    
    if ( $partNumber != "" ) 
    {
         $partNumber .= ".";
    }

    if ( isset( $struct->parts ) && count( $struct->parts ) > 0 )
    {
        foreach( $struct->parts as $part )
        {
            //The delivery status is in a message/delivery-status part we only fetch this part
            if ( $part->type == 2
                 && isset( $part->ifsubtype )
                 && $part->ifsubtype == 1
                 && $part->subtype == 'DELIVERY-STATUS'  )
            {
                //Get the bounce status
                $partNumber .= $counter;
                $message = imap_fetchbody( $mbox, $mailNumber, $partNumber );
                $errorCode = findErrorCode( $message );
                $address = findDestinationAddress( $message );
                $arrival = findBounceArrived( $message );
                
                return array( 'error_code' => $errorCode,
                              'address' => $address,
                              'bounce_arrived' => $arrival,
                              'bounce_message' => $message );
            }
            else
            {
                //Find the status message
                $errorCode = getDeliveryStatusType( $mbox, $mailNumber, $part, $partNumber );
            }
            $counter++;
        }
    }
    else
    {
       $bodyfetch = imap_fetchbody( $mbox, $mailNumber, $partNumber );
       $search = 'mailbox is full';
       $res = strpos($bodyfetch, $search);
       
       if( $res > 0 )
       {        
                $partNumber .= $counter;
                $message = imap_fetchbody( $mbox, $mailNumber, $partNumber );
                $errorCode = findErrorCode( $message );
                $address = findDestinationAddress( $message );
                $arrival = findBounceArrived( $message );

                return array( 'error_code' => $errorCode,
                              'address' => $address,
                              'bounce_arrived' => $arrival,
                              'bounce_message' => $message );
       } 

    }
    
    return $errorCode;
}

/*!
  Get returned message part
 */
function getReturnedMessageNewsletterID( $mbox, $mailNumber, $struct, $partNumber, $initial = true )
{
    $counter = 1;
    $newsletterID = false;
    if ( $partNumber != "" )
    {
        $partNumber .= ".";
    }

    if ( isset( $struct->parts ) && count( $struct->parts ) > 0 )
    {
        foreach( $struct->parts as $part )
        {
            //The delivery status is in a message/delivery-status part we only fetch this part
            if ( $part->type == 2
                 && isset( $part->ifsubtype )
                 && $part->ifsubtype == 1
                 && $part->subtype == 'RFC822'  )
            {
                //Get the bounce status
                $partNumber .= $counter;
                $message = imap_fetchbody( $mbox, $mailNumber, $partNumber );
                $newsletterID = findNewsletterID( $message );
                return $newsletterID;
            }
            else
            {
                //Find the status message
                $newsletterID = getReturnedMessageNewsletterID( $mbox, $mailNumber, $part, $partNumber, false );
            }
            $counter++;
        }
    }
    else
    {
       $bodyfetch = imap_fetchbody( $mbox, $mailNumber, $partNumber );
       $search = 'mailbox is full';
       $res = strpos($bodyfetch, $search);
       
       if( $res > 0 )
       {   
                //Get the bounce status
                $partNumber .= $counter;
                $message = imap_fetchbody( $mbox, $mailNumber, $partNumber );
                $newsletterID = findNewsletterID( $message );
                return $newsletterID;
       }
    }
    return $newsletterID;

}

/*!
  Finds the destination email address of the mailing
 */
function findDestinationAddress( $mailbody )
{
    $pattern = '/Final-Recipient\:[\s](.+)\;[\s](.+?)\\r/';
    $res = preg_match( $pattern, $mailbody, $matches );
    $patternNoRFC = '/To\:[\s](.+)[\s](.+?)\\r/';
    $resNoRFC = preg_match( $patternNoRFC, $mailbody, $matchesNoRFC );

    if ( $res )
    {
        return $matches[2];
    }
    else  
    {
        return $matchesNoRFC[1];
    }
    return false;
}
   
/*!
  Scans through the email body looking for numerical error codes in the
  form x.x.x ( RFC 1893 conform ) or XXX ( fallback ) to identify the
  cause of the bounced message.
 */
function findErrorCode( $mailbody )
{
    $pattern = '/[\s]([0-9]{1}\.[0-9]{1}\.[0-9]{1}|[0-9]{3})[\s]/';
    $res = preg_match( $pattern, $mailbody, $matches );
    $patternNoRFC = '/mailbox is full/i';
    $resNoRFC = preg_match($patternNoRFC, $mailbody, $matchesNoRFC);
   
    if ( $res  )
    {
       return $matches[1];
    }
    else
    {
       return $matchesNoRFC[0];
    }

}
   
/*!
  Find the date the message bounced
 */
function findBounceArrived( $message )
{

    $pattern = '/Arrival-Date\:(.+)/';
    $res = preg_match( $pattern, $message, $matches );
    $patternNoRFC = '/Date\:[\s](.+)[\s](.+?)\\r/';
    $resNoRFC = preg_match( $patternNoRFC, $message, $matchesNoRFC );

    if ( $res )
    {
        return strtotime( $matches[1] );
    }
    else
    {
        return strtotime( $matchesNoRFC[1] );
    }    
    return false;

}

   
/*!
  Find message id header, and extracts encoded newsletter id
 */
function findNewsletterID( $msg )
{
    $pattern = '/Message-Id\:[\s]\<([0-9]+)\..+\>[\s]/i';
    $res = preg_match( $pattern, $msg, $matches );
    if ( $res )
    {
        return $matches[1];
    }
    return false;
}

/*!
  Get message content
  */
function getMessageContent($mbox, $mid)
{      
    $struct = imap_fetchstructure($mbox, $mid);

    $parts = $struct->parts;
    $i = 0;

    if (!$parts) /* Simple message, only 1 piece */
    { 
        $attachment = array(); /* No attachments */
        $content = imap_body($mbox, $mid);
    }
    else /* Complicated message, multiple parts */
    {
        $endwhile = false;

        $stack = array(); /* Stack while parsing message */
        $content = "";    /* Content of message */
        $attachment = array(); /* Attachments */

        while( !$endwhile )
        {
            if( !$parts[$i] )
            {
                if (count($stack) > 0) {
                    $parts = $stack[count($stack)-1]["p"];
                    $i     = $stack[count($stack)-1]["i"] + 1;
                    array_pop($stack);
                } else {
                    $endwhile = true;
                }
            }
        
            if( !$endwhile )
            {
                /* Create message part first (example '1.2.3') */
                $partstring = "";
                foreach( $stack as $s )
                {
                    $partstring .= ( $s["i"] + 1 ) . ".";
                }
                $partstring .= ( $i + 1 );
          
                if( strtoupper( $parts[$i]->disposition ) == "ATTACHMENT" ) /* Attachment */
                {
                    $attachment[] = array( "filename" => $parts[$i]->parameters[0]->value,
                                           "filedata" => imap_fetchbody($mbox, $mid, $partstring));
                }
                elseif( strtoupper( $parts[$i]->subtype ) == "PLAIN" ) /* Message */
                {
                    $content .= imap_fetchbody( $mbox, $mid, $partstring );
                }
            }

            if( $parts[$i]->parts ) {
                $stack[] = array( "p" => $parts, "i" => $i );
                $parts = $parts[$i]->parts;
                $i = 0;
            } else {
                $i++;
            }
        } /* while */
    } /* complicated message */

    return array($content, $attachment);
    
}


/*!
  Update the bounce information in the case of abounce.
 */
function handleBounce( $type, $id, $bounceType )
{
    $previousBounce = eZBounce::fetchObject( eZBounce::definition(),
                                        null,
                                        array( 'newslettersenditem_id' => $id,
                                               'bounce_type' => $bounceType ),
                                        true);
    if( $previousBounce )
    {
        //Update the bounce count for this mailling
        $previousCount = $previousBounce->attribute( 'bounce_count' );
        $previousBounce->setAttribute( 'bounce_count', ++$previousCount );
        $previousBounce->setAttribute( 'bounce_message', $type['bounce_message']);
        $previousBounce->store();

        $sendItem = eZSendNewsletterItem::fetch( $id, true );
        if ( $sendItem )
        {
            if ( $bounceType == EZSOFTBOUNCE && $previousCount < $bounceCountStop )
            {
                $sendItem->setAttribute( 'send_status', eZSendNewsletterItem::SendStatusNone );
                $sendItem->store();
            }

            $subscriptionObject = eZSubscription::fetch( $sendItem->attribute( 'subscription_id' ) );
            if ( $subscriptionObject )
            {
                $bounce_count = $subscriptionObject->attribute( 'bounce_count' );
                $subscriptionObject->setAttribute( 'bounce_count', ++$bounce_count );
                $subscriptionObject->store();
            }
        }
    }
    else
    {
        //We create a new bounce entry
        $db = eZDB::instance();
        $db->begin();
        $bounceData = new eZBounce( array() );
        $bounceData->store();
        $bounceData->setAttribute( 'address', $type['address'] );
        $bounceData->setAttribute( 'bounce_type', $bounceType );
        $bounceData->setAttribute( 'bounce_count', 1 );
        $bounceData->setAttribute( 'bounce_arrived', $type['bounce_arrived'] );
        $bounceData->setAttribute( 'newslettersenditem_id', $id );
        $bounceData->setAttribute( 'bounce_message', $type['bounce_message']);
        $bounceData->store();
        $db->commit();

        //Update the sendnewsletteritem table with reference to this bounce entry
        $sendItem = eZSendNewsletterItem::fetch( $id, true );
        if ( $sendItem )
        {
            $current_bounceID = $sendItem->attribute( 'bounce_id' );
            if ( $current_bounceID == 0 )
            {
                $sendItem->setAttribute( 'bounce_id', $bounceData->attribute( 'id' ) );
                $sendItem->store();
            }
            else
            {
                eZDebug::writeNotice( "Bounce ID already in place", 'check_bounce' );
            }

            if ( $bounceType == EZSOFTBOUNCE )
            {
                $sendItem->setAttribute( 'send_status', eZSendNewsletterItem::SendStatusNone );
                $sendItem->store();
            }

            //Set the bounce count for the matching subscription_id directly in the subscription table
            $subscription_id = $sendItem->attribute( 'subscription_id' );

            $subscriptionObject = eZSubscription::fetch( $subscription_id );
            if ( $subscriptionObject )
            {
                $bounce_count = $subscriptionObject->attribute( 'bounce_count' );
                $subscriptionObject->setAttribute( 'bounce_count', ++$bounce_count );
                $subscriptionObject->store();
            }
        }
    }
}


/*!
  Read in the e-mail account settings
 */

$mailsettings = eZINI::instance( 'bounce.ini' );

$bounceCountStop = ( $mailsettings->variable( 'BounceSettings', 'BounceCount' )
                        ? $mailsettings->variable( 'BounceSettings', 'BounceCount' ) : 2 );

$mailAccountArray = $mailsettings->variable( 'MailAccountSettings', 'AccountList' );

if( $mailAccountArray === false ) {
    $cli->error( "No mailboxes set. Qutting." );
    return;
}

$cli->output( "Found ".count($mailAccountArray)." mailboxes.." );

foreach( $mailAccountArray as $account )
{
    $ServerName = $mailsettings->variable( $account, 'ServerName' );
    $ServerPort = $mailsettings->variable( $account, 'ServerPort' );
    $LoginName  = $mailsettings->variable( $account, 'LoginName' );
    $Password   = $mailsettings->variable( $account, 'Password' );
    $Protocol   = $mailsettings->hasVariable( $account, 'Protocol' )
                ? $mailsettings->variable( $account, 'Protocol' ) : 'pop3';

    $Flags = $mailsettings->variable( $account, 'Flags' );
    
    if ( is_array( $Flags ) and count( $Flags ) > 0 ) {
        $Flags = '/' . join( '/', array_unique( $Flags ) );
    } else {
        $Flags = '';
    }

    $cli->output( "Connecting to ".$ServerName."." );

    $server = "{" . $ServerName . ":" . $ServerPort . "/". $Protocol . $Flags . "}INBOX";

    $mailbox = imap_open( $server, $LoginName, $Password );
    
    if ( $mailbox == false )
    {
        $errorMsg = rawurlencode( imap_last_error() );

        $cli->error( "Unable to connect to $ServerName as $LoginName" );
        $cli->error( "Returned error: $errorMsg" );
    }
    else
    {
        // fetch numbers of all new mails
        $num = imap_num_msg( $mailbox );

        $cli->output( "Found ".$num." emails in mailbox." );
              
        // check each mail in inbox
        for ( $i = 1; $i <= $num; $i++ )
        {
            $delete = true;

            // fetch mail headers to get the structure.
            $mailstructure = imap_fetchstructure( $mailbox, $i );

            $type = getDeliveryStatusType( $mailbox, $i,
                                           $mailstructure, "" );

            // get returned message id from newsletter   
            $newsletterID = getReturnedMessageNewsletterID( $mailbox, $i, $mailstructure, "" );
    
            if ( $newsletterID != false )
            {
                $cli->output( "Assigned to newsletter: ".$newsletterID );
            
                switch( findBounceType( $type['error_code'] ) )
                {
                    case EZSOFTBOUNCE: {
                        handleBounce( $type, $newsletterID, EZSOFTBOUNCE );
                    }
                    break;
                    
                    case EZHARDBOUNCE: {
                        handleBounce( $type, $newsletterID, EZHARDBOUNCE );
                    }
                    break;
                    
                    case EZNOBOUNCE:
                    default: {
                        // TODO
                    }
                    break;
                }
            } else {
               //do not delete
               $delete = false;
            }

            $imapErrors = imap_errors();
            $imapAlerts = imap_alerts();
            
            if ( $imapErrors ) {
                $cli->output( "\nMAIL ID: $mailID" );
                print_r( $imapErrors );
            }
            
            if ( $imapAlerts ) {
                $cli->output( "\nMAIL ID: $mailID" );
                print_r( $imapAlerts );
            }

            if ( $delete ) {
                imap_delete( $mailbox, $i );
            }
        }
        
        // Close imap connection
        imap_close( $mailbox, CL_EXPUNGE );
    }
}

?>
