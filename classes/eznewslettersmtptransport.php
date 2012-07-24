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
    function sendMail( ezcMail $mail )
    {
        $ini = eZINI::instance();
        $parameters = array();
        $parameters['host'] = $ini->variable( 'MailSettings', 'TransportServer' );
        $parameters['helo'] = $ini->variable( 'MailSettings', 'TransportServer' );
        $parameters['port'] = $ini->variable( 'MailSettings', 'TransportPort' );
        $parameters['connectionType'] = $ini->variable( 'MailSettings', 'TransportConnectionType' );
        $user = $ini->variable( 'MailSettings', 'TransportUser' );
        $password = $ini->variable( 'MailSettings', 'TransportPassword' );
        if ( $user and
             $password )
        {
            $parameters['auth'] = true;
            $parameters['user'] = $user;
            $parameters['pass'] = $password;
        }

        $options = new ezcMailSmtpTransportOptions();
        if( $parameters['connectionType'] )
        {
            $options->connectionType = $parameters['connectionType'];
        }

        $smtp = new ezcMailSmtpTransport( $parameters['host'], $user, $password, $parameters['port'], $options );

        try
        {
            $smtp->send( $mail );
            return true;
        }
        catch ( ezcMailException $e )
        {
            eZDebug::writeError( "Error sending SMTP mail: " . $e->getMessage(), 'eZSMTPTransport::sendMail' );
            echo "SMTP ERROR: " . $e->getMessage();
            return false;
        }
        
        return false;
    }
}

?>
