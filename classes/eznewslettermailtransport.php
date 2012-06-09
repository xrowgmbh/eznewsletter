<?php
//
// Definition of eZNewsletterMailTransport class
//
// Created on: <10-Dec-2002 14:31:35 amos>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ publish
// SOFTWARE RELEASE: 3.7.x
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

/*! \file eznewslettermailtransport.php
*/

/*!
  \class eZNewsletterMailTransport eznewslettermailtransport.php
  \brief Interface for mail transport handling

*/

class eZNewsletterMailTransport
{
    /*!
     Constructor
    */
    function __construct()
    {
    }

    /*!
     \virtual
     Tries to send the contents of the email object \a $mail and
     returns \c true if succesful.
    */
    function sendMail( &$mail )
    {
        if ( get_class( $mail ) != 'eznewslettermail' )
        {
            eZDebug::writeError( 'Can only handle objects of type eZMail.', 'eZNewsletterMailTransport::sendMail' );
            return false;
        }
        return false;
    }

    /*!
     \static
     Sends the contents of the email object \a $mail using the default transport.
    */
    static function send( eZNewsletterMail $mail, $preview=false )
    {
        if( strtolower( get_class( $mail ) ) != 'eznewslettermail' )
        {
            eZDebug::writeError( 'Can only handle objects of type eZNewsletterMail .', 'eZNewsletterMailTransport::send' );
            return false;
        }
	
	//transport type defined in newsletter extension
	$newsletterINI = eZINI::instance('eznewsletter.ini');

	if ($newsletterINI)
	{
	    if ($preview)
	    {
    		$customTransportType = trim( $newsletterINI->variable( 'NewsletterSendout', 'PreviewTransport' ) );
	    }
	    else
	    {
    		$customTransportType = trim( $newsletterINI->variable( 'NewsletterSendout', 'Transport' ) );	    
	    }
	}

	//transport type defined in siteaccess/override
    $ini = eZINI::instance();

    if ( $mail->contentType() === "sms" )
    {
	    if ($customTransportType=='File')
	    {
		$transportType = 'fileSMS';
	    }
	    else
	    {
		$transportType = 'SMS';
    }
	} 
        else
        {
	    if (!$customTransportType)
	    {
    		$transportType = trim( $ini->variable( 'MailSettings', 'Transport' ) );
	    }
	    else
	    {
		$transportType = $customTransportType;
	    }
	}

        $transportObject =& $GLOBALS['eZMailTransportHandler_' . strtolower( $transportType )];
        if ( !isset( $transportObject ) or
             !is_object( $transportObject ) )
        {
            $transportClassFile = 'eznewsletter' . strtolower( $transportType ) . 'transport.php';
            //Change from original ezmailtransport.php, now loads transport class directly from the newsletter extension.
            $transportClassPath = eZExtension::baseDirectory() . '/eznewsletter/classes/' . $transportClassFile;
            $transportClass = 'eZNewsletter' . $transportType . 'Transport';
            if ( !file_exists( $transportClassPath ) )
            {
                eZDebug::writeError( "Unknown mail transport type '$transportType', cannot send mail",
                                     'eZNewsletterMailTransport::send' );
                return false;
            }
            //include_once( $transportClassPath );
            
            if ( !class_exists( $transportClass ) )
            {
                eZDebug::writeError( "No class available for mail transport type '$transportType', cannot send mail",
                                     'eZNewsletterMailTransport::send' );
                return false;
            }
            $transportObject = new $transportClass();
        }
        return $transportObject->sendMail( $mail );
    }
}

?>
