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

class eZNewsletterSendmailTransport extends eZNewsletterMailTransport
{
    function sendMail( ezcMail $mail )
    {
        $sendMailSettings = eZINI::instance( 'ezsendmailsettings.ini' );
        $sendMailParameter = $sendMailSettings->variable( 'SendNewsletter', 'MTAEnvelopeReturnPathParameter' );
        $transport = new ezcMailMtaTransport();
        $transport->send( $mail );
    }
}
