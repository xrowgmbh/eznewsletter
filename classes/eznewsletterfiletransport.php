<?php
//
// Definition of eZewsletterSendmailTransport class
//
// Created on: <10-Dec-2002 14:41:22 amos>
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

/*! \file ezsendmailtransport.php
*/

/*!
  \class eZSendmailTransport ezsendmailtransport.php
  \brief Sends the email message to sendmail which takes care of sending the actual message.

  Uses the mail() function in PHP to pass the email to the sendmail system.

*/

class eZNewsletterFileTransport extends eZNewsletterMailTransport
{
    function sendMail( ezcMail $mail )
    {
		$separator = "/";
        $mail->appendExcludeHeaders( array( 'to', 'subject' ) );
        $headers = rtrim( $mail->generateHeaders() ); // rtrim removes the linebreak at the end, mail doesn't want it.

        if ( ( count( $mail->to ) + count( $mail->cc ) + count( $mail->bcc ) ) < 1 )
        {
            throw new ezcMailTransportException( 'No recipient addresses found in message header.' );
        }
        $additionalParameters = "";
        if ( isset( $mail->returnPath ) )
        {
            $additionalParameters = "-f{$mail->returnPath->email}";
        }
        $sys = eZSys::instance();

        $fname = time().'-'.rand().'.mail';
        $qdir = eZSys::siteDir() . eZSys::varDirectory() . $separator . 'mailq';

        $data = $headers.ezcMailTools::lineBreak();

        $data .= ezcMailTools::lineBreak();
        $data .= $mail->generateBody();

        $data = preg_replace('/(\r\n|\r|\n)/', "\r\n", $data);

        $success = eZFile::create($fname, $qdir, $data);
        if ( $success === false )
        {
            throw new ezcMailTransportException( 'The email could not be sent by sendmail' );
        }
    }
}