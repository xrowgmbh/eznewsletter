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

/*! \file email_subscribe.php
*/

$extension = 'eznewsletter';

$cli = eZCLI::instance();

if( !extension_loaded( 'imap' ) )
{
    $cli->error( "The PHP IMAP extension is required. Qutting." );
    return;
}

//Read in the e-mail account settings
$mailsettings = eZINI::instance( 'email_subscribe.ini' );

$mailAccountArray = $mailsettings->variable( 'MailAccountSettings', 'AccountList' );

$cli->output( "Found ".count($mailAccountArray)." mailboxes.." );

foreach( $mailAccountArray as $account )
{
    if( $mailbox_handle = get_mailbox( $mailsettings, $account ) )
    {
        $subscription_lists = get_list_ids();
        //print_r( $subscription_lists );
        $subscribe_email    = $mailsettings->variable( 'EmailSettings', 'SubscribeEmail' );
        $unsubscribe_email  = $mailsettings->variable( 'EmailSettings', 'UnsubscribeEmail' );
        
        // fetch numbers of all new mails
        $num = imap_num_msg( $mailbox_handle );
        $num = $num ? $num : 0;
        $cli->output( "Found ".$num." email(s) in mailbox." );
        
        // check each mail in inbox - $i is the mail position
        for ( $i = 1; $i <= $num; $i++ )
        {
            $handled = true;

            $header_info = get_mail_header( $mailbox_handle, $i );
            $to      = $header_info->toaddress;
            $from    = $header_info->fromaddress;
            $subject = $header_info->subject;

            if( handle_to_address( $to ) == $subscribe_email &&
                in_array( handle_subject( $subject ), $subscription_lists ) )
            {
                $subscription = subscribe($from, $subject );
                if ( $subscription )
                {
                    eZMail::extractEmail( $from, $email, $name );
                    $cli->output( "Subscription created: ".$subscription->attribute('subscriptionlist_id')."/".$email );
                    sendConfirmation($email, $subscription, true);
                }
                else
                {
                    $handled = false;
                }
            }
            elseif( handle_to_address( $to ) == $unsubscribe_email &&
                in_array( handle_subject( $subject ), $subscription_lists ) )
            {
                $subscription = unsubscribe($from, $subject );
                if ( $subscription  )
                {
                    eZMail::extractEmail( $from, $email, $name );
                    $cli->output( "Subscription removed: ".$subscription->attribute('subscriptionlist_id')."/".$email );
                    sendConfirmation($email, $subscription, false);
                }
                else
                {
                    $handled = false;
                }
            }
            else
            {
                //echo "Not a un/subscribe email: ".$to. " \"".$subject."\""."\n";
                $handled = false;
            }

            if( $handled )
            {
                $cli->output( 'Email ('.$i.') removed from mailbox' );
                imap_delete( $mailbox, $i );
            }
        }
    }
}

function get_list_ids()
{
    $ids = array();
    
    $lists = eZSubscriptionList::fetchList();
    
    for( $i = 0; $i < count( $lists); $i++ )
    {
        $ids[] = strtolower( $lists[$i]->urlAlias() );
    }

    return $ids;
}

function get_mail_header( $mailbox_handle, $pos )
{
    $header_info = null;
    $header_info = imap_header( $mailbox_handle, $pos );

    if ( !isset( $cli ) ) $cli = eZCLI::instance();

    if ( $imapErrors = imap_errors() )
    {
        $cli->error( 'Found error while trying to read the mail header. Mail pos: $pos', true );
        print_r( $imapErrors );
        $header_info = null;
    }
    if ( $imapAlerts = imap_alerts() )
    {
        $cli->error( 'Got alerts while trying to read the mail header. Mail pos: $pos', true );
        print_r( $imapAlerts );
    }

    return $header_info;
}

function handle_to_address( $address_string )
{
    // pretty simple
    return strtolower( $address_string );
}

function handle_from_address( $address_string )
{
    return handle_to_address( $address_string );
}

function handle_subject( $subject )
{
    return handle_to_address( $subject );
}

function get_mailbox( $mailsettings, $account )
{
    if ( !isset( $cli ) ) $cli = eZCLI::instance();

    $mailboxhandle = null;

    $ServerName = $mailsettings->variable( $account, 'ServerName' );
    $ServerPort = $mailsettings->variable( $account, 'ServerPort' );
    $LoginName  = $mailsettings->variable( $account, 'LoginName' );
    $Password   = $mailsettings->variable( $account, 'Password' );
    $Flags      = $mailsettings->variable( $account, 'Flags' );
    $Protocol   = $mailsettings->hasVariable( $account, 'Protocol' )
                    ? $mailsettings->variable( $account, 'Protocol' ) : 'pop3';

    if ( is_array( $Flags ) and count( $Flags ) > 0 )
    {
        $Flags = '/' . join( '/', array_unique( $Flags ) );
    }
    else
    {
        $Flags = '';
    }

    $server = "{" . $ServerName . ":" . $ServerPort . "/". $Protocol . $Flags . "}INBOX";

    $cli->output( "Connecting to ".$ServerName );

    $mailboxhandle = imap_open( $server, $LoginName, $Password );

    if ( $mailboxhandle == false )
    {
        $errorMsg = rawurlencode( imap_last_error() );
        $cli->error( "Can not open mailbox. Error: ".$errorMsg, true );
        imap_close( $mailbox, CL_EXPUNGE );
    }
    
    return $mailboxhandle;
}

function subscribe( $address_string, $list_name )
{
    $subscription_list = eZSubscriptionList::fetch( $list_name );
    eZMail::extractEmail( $address_string, $email, $name );

    if( $email )
    {
        $subscription = eZSubscription::fetchByEmailSubscriptionListID( $email, $subscription_list->attribute( 'id' ) );
        if ( !$subscription )
        {
            $firstname = '';
            $name = '';
            $mobile = '';
            
            $subscription = $subscription_list->registerSubscription( $firstname, $name, $mobile, $email );
            $subscription->setAttribute( 'status', eZSubscription::StatusConfirmed );
            $subscription->sync();
            return $subscription;
        }
        else
        {
            if ( $subscription->attribute( 'status' ) == eZSubscription::StatusRemovedSelf )
            {
            $subscription->setAttribute( 'status', eZSubscription::StatusApproved );
            //$subscription->setAttribute( 'status', eZSubscription::StatusConfirmed );
            $subscription->sync();
            return $subscription;
            }
        }
    }
    
    return false;
}

function unsubscribe( $address_string, $list_name )
{
    $subscription_list = eZSubscriptionList::fetch( $list_name );
    eZMail::extractEmail( $address_string, $email, $name );

    if( $email )
    {
        $subscription = eZSubscription::fetchByEmailSubscriptionListID( $email, $subscription_list->attribute( 'id' ) );

        if ( $subscription ) 
        {
            if ( $subscription->attribute('status') == eZSubscription::StatusApproved )
            {
            $subscription->setAttribute( 'status', eZSubscription::StatusRemovedSelf );
            $subscription->sync();
            return $subscription;
            }
        }
    }
    return false;
}

function sendConfirmation($email, $subscription, $subscribe)
{
    //send mail
    $res = eZTemplateDesignResource::instance();
    $ini = eZINI::instance();
    $hostname = eZSys::hostname();
    
    if ($subscribe)
    {
        $template = 'design:eznewsletter/sendout/subscription.tpl';
    }
    else
    {
        $template = 'design:eznewsletter/sendout/unsubscription.tpl';
    }

    $tpl = eZNewsletterTemplateWrapper::templateInit(); 
    $tpl->setVariable( 'userData', eZUserSubscriptionData::fetch(  $email ) );
    $tpl->setVariable( 'hostname', $hostname );
    $tpl->setVariable( 'subscription', $subscription );
    
    $subscriptionList = eZSubscriptionList::fetch( $subscription->attribute( 'subscriptionlist_id' ), eZSubscriptionList::StatusPublished, true, true );
    $tpl->setVariable( 'subscriptionList', $subscriptionList );
    $templateResult = $tpl->fetch( $template );

    if ( $tpl->hasVariable( 'subject' ) )
    {
        $subject = $tpl->variable( 'subject' );
    }
    $mail = new eZMail();
    $mail->setSender( $ini->variable( 'MailSettings', 'EmailSender' ) );
    $mail->setReceiver( $email );
    $mail->setBody( $templateResult );
    $mail->setSubject( $subject );
    
    eZMailTransport::send( $mail );
}

?>
