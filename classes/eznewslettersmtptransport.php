<?php
//
// Definition of eZSMTPTransport class
//
// Created on: <10-Dec-2002 15:20:20 amos>
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

/*! \file ezsmtptransport.php
*/

/*!
  \class eZSMTPTransport ezsmtptransport.php
  \brief The class eZSMTPTransport does

*/

class eZNewsletterSMTPTransport extends eZNewsletterMailTransport
{
    /*!
     Constructor
    */
    function __construct()
    {
    }

    /*!
     \reimp
    */
    function sendMail( &$mail )
    {
        $ini = eZINI::instance();
        $parameters = array();
        $parameters['host'] = $ini->variable( 'MailSettings', 'TransportServer' );
        $parameters['helo'] = $ini->variable( 'MailSettings', 'TransportServer' );
        $parameters['port'] = $ini->variable( 'MailSettings', 'TransportPort' );
        $user = $ini->variable( 'MailSettings', 'TransportUser' );
        $password = $ini->variable( 'MailSettings', 'TransportPassword' );
        if ( $user and
             $password )
        {
            $parameters['auth'] = true;
            $parameters['user'] = $user;
            $parameters['pass'] = $password;
        }

        /* If email sender hasn't been specified or is empty
         * we substitute it with either MailSettings.EmailSender or AdminEmail.
         */
        if ( !$mail->senderText() )
        {
            $emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
            if ( !$emailSender )
                $emailSender = $ini->variable( 'MailSettings', 'AdminEmail' );

            eZMail::extractEmail( $emailSender, $emailSenderAddress, $emailSenderName );

            if ( !eZMail::validate( $emailSenderAddress ) )
                $emailSender = false;

            if ( $emailSender )
                $mail->setSenderText( $emailSender );
        }

        $sendData = array();
        $from = $mail->sender();
        $sendData['from'] = $from['email'];
        $sendData["recipients"] = $mail->receiverTextList();
        $sendData['CcRecipients'] = $mail->ccReceiverTextList();
        $sendData['BccRecipients'] = $mail->bccReceiverTextList();
        $sendData['headers'] = $mail->headerTextList();
        $sendData['body'] = $mail->body();

//      $smtp = smtp::connect( $parameters );
	$smtp = new smtp( $parameters );
	$smtp->connect( );

        if ( $smtp )
        {
            $result = $smtp->send( $sendData );

            $mailSent = true;
            if ( isset( $smtp->errors[0] ) )
            {
                eZDebug::writeError( "Error sending SMTP mail: " . $smtp->errors[0], "eZNewsletterSMTPTransport::sendMail()" );
                echo "SMTP ERROR: ".$smtp->errors[0];
                $mailSent = false;
            }
            $smtp->quit();
        }
        else
        {
            $mailSent = false;
        }
        return $mailSent;
    }
}

?>
