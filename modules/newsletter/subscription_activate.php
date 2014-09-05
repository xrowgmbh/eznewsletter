<?php
//
// Created on: <22-Dec-2005 16:00:04 hovik>
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

/*! \file subscription_activate.php
*/

$Module = $Params['Module'];

$http = eZHTTPTool::instance();

$subscription = eZSubscription::fetchByHash( $Params['Hash'] );

if ( !$subscription )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

$subscriptionList = $subscription->attribute( 'subscription_list' );

$res = eZTemplateDesignResource::instance();
$res->setKeys( array( array( 'newsletter_view', 'subscription_activate' ) ) );

$subscription->setAttribute( 'status', eZSubscription::StatusConfirmed );
if ( $subscriptionList->attribute( 'auto_approve_registered' ) )
{
    $subscription->setAttribute( 'status', eZSubscription::StatusApproved );
}
$subscription->store();

//Check if we need to notify anyone
$newsletterini = eZINI::instance( 'eznewsletter.ini' );
if($newsletterini->hasVariable( 'NotifySettings', 'Receivers' ))
{
    if(count($newsletterini->variable( 'NotifySettings', 'Receivers' )) > 0 )
    {
        $receivers=$newsletterini->variable( 'NotifySettings', 'Receivers' );
        $sender=$newsletterini->variable( 'NotifySettings', 'Sender' );
        $sendername=$newsletterini->variable( 'NotifySettings', 'SenderName' );
        $receivernames=$newsletterini->variable( 'NotifySettings', 'ReceiversName' );
        foreach( $receivers as $receiver )
        {
            if( eZMail::validate( $receiver ) )
            {
                $mail = new eZMail();
                $mail->setSender( $sender, $sendername );
                $mail->setSubject( "Neue Newsletteranmeldung" );
                // fetch text from mail template
                $mailtpl = eZTemplate::factory();
                $mailtext = "Ein Abonnent hat sich neu registriert. \n";
                $mailtext .= "Liste: " . $subscriptionList->Name . " \n";
                $mailtext .= "Email: " . $subscription->Email . "\n";

                if($subscription->attribute("firstname") != " ")
                {
                    $mailtext .= "Vorname: " . trim($subscription->attribute("firstname")) . "\n";
                }
                if($subscription->attribute("name") != " ")
                {
                    $mailtext .= "Nachname: " . trim($subscription->attribute("name")) . "\n";
                }
                if( array_key_exists($receiver, $receivernames) )
                {
                    $mail->setReceiver( trim($receiver), trim($receivernames[trim($receiver)]));
                }
                else
                {
                    $mail->setReceiver( trim($receiver));
                }
                $mail->setBody( $mailtext );
                // mail was sent ok
                eZMailTransport::send( $mail );
            }
        }
    }
}
$tpl = eZNewsletterTemplateWrapper::templateInit();
$tpl->setVariable( 'subscription', $subscription );

$Result = array();
$Result['content'] = $tpl->fetch( "design:eznewsletter/subscription_activate.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'eznewsletter/subscription_activate', 'Activate subscription' ) ) );


?>