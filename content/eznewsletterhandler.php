<?php
//
// Definition of eZNewsletterHandler class
//
// Created on: <20-Dec-2005 15:02:11 hovik>
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

/*! \file eznewsletterhandler.php
*/

/*!
  \class eZNewsletterHandler eznewsletterhandler.php
  \brief The class eZNewsletterHandler does

*/

class eZNewsletterHandler extends eZContentObjectEditHandler
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
    static function storeActionList()
    {
        return array( 'NewsletterSendPreview', 'NewsletterPreview' );
    }

    /*!
     \reimp
     // #TODO# Check
    */
    function fetchInput( $http, &$module, &$class, $object, &$version, $contentObjectAttributes, $editVersion, $editLanguage, $fromLanguage )
    {
        if ( !$http->hasPostVariable( 'NewsletterID' ) )
        {
            return;
        }

        $newsletter = eZNewsletter::fetchDraft( $http->postVariable( 'NewsletterID' ) );
        if ( !$newsletter )
        {
            eZDebug::writeError( 'Could not fetch newsletter : ' . $http->postVariable( 'NewsletterID' ) );
            return;
        }

        $newsletter->setAttribute( 'name', $http->postVariable( 'NewsletterName' ) );
        $newsletter->setAttribute( 'send_date', $this->getTimestamp( $http, $object->ID ) );
        $newsletter->setAttribute( 'category', $http->postVariable( 'NewsletterCategory' ) );
        
        // #CHECK# validate
        $newsletter->setAttribute( 'preview_email', $http->postVariable( 'NewsletterPreviewEmail' ) );
        $newsletter->setAttribute( 'preview_mobile', $http->postVariable( 'NewsletterPreviewMobile' ) );

        $preInput = "<?xml version=\"1.0\" encoding=\"utf-8\" ?><paragraph>";
        $postInput = "</paragraph>";

        $pretext  = $http->hasPostVariable( 'pretext' )  ? $preInput. html_entity_decode( $http->postVariable( 'pretext' ) ) .$postInput  : '';
        $posttext = $http->hasPostVariable( 'posttext' ) ? $preInput. html_entity_decode( $http->postVariable( 'posttext' ) ) .$postInput : '';

        $newsletter->setAttribute( 'pretext', $pretext );
        $newsletter->setAttribute( 'posttext', $posttext );
        
        $designtouse  = $http->hasPostVariable( 'DesignToUse' )  ? $http->postVariable( 'DesignToUse' )  : 'eznewsletter';
        $newsletter->setAttribute( 'design_to_use', $designtouse );
        $newsletter->setAttribute( 'recurrence_type', $http->postVariable( 'RecurrenceType' ) );

        $recurrencecondition = $http->hasPostVariable( 'RecurrenceCondition' ) ? $http->postVariable( 'RecurrenceCondition' ) : '';
        $newsletter->setAttribute( 'recurrence_condition', $recurrencecondition  );

        if ( $http->postVariable( 'RecurrenceType' ) === 'd' )
        {
            $newsletter->setAttribute( 'recurrence_value', implode( ',', $http->postVariable( 'RecurrenceValue_d' ) ) );
        }
        elseif ( $http->postVariable( 'RecurrenceType' ) === 'w' )
        {
            $newsletter->setAttribute( 'recurrence_value', implode( ',', $http->postVariable( 'RecurrenceValue_w' ) ) );
        }
        elseif ( $http->postVariable( 'RecurrenceType' ) === 'm' )
        {
            $newsletter->setAttribute( 'recurrence_value', implode( ',', $http->postVariable( 'RecurrenceValue_m' ) ) );
        }
        else
        {
            $newsletter->setAttribute( 'recurrence_value', implode( ',', $http->postVariable( 'RecurrenceValue' ) ) );
        }

        $newsletter->store();

        if ( $http->hasPostVariable( 'NewsletterPreview' ) )
        {
            return $module->redirect( 'newsletter',  'preview', array( $object->attribute( 'id' ), $editVersion ) );
        }


        if ( $http->hasPostVariable( 'NewsletterSendPreview' ) )
        {
            eZNewsletter::sendNewsletterMail( $newsletter, true );

        }
    }

    /*!
     \reimp
    */
    function publish( $contentObjectID, $contentObjectVersion )
    {
        $newsletter = eZNewsletter::fetchByContentObject( $contentObjectID,
                                                          $contentObjectVersion,
                                                          eZNewsletter::StatusDraft );
        if ( $newsletter )
        {
            $newsletter->publish();
        }
    }

    /*!
     Get timestamp from HTTP input.

     \param http object.

     \return timestamp
    */
    function getTimestamp( $http, $id )
    {
        $day   = $http->postVariable( 'newsletter_datetime_day_' . $id );
        $month = $http->postVariable( 'newsletter_datetime_month_' . $id );
        $year  = $http->postVariable( 'newsletter_datetime_year_' . $id );

        $hour   = $http->postVariable( 'newsletter_datetime_hour_' . $id );
        $minute = $http->postVariable( 'newsletter_datetime_minute_' . $id );

        $dateTime = new eZDateTime();

        if ( ( $year == '' and $month == ''and $day == '' and
               $hour == '' and $minute == '' ) or
             !checkdate( $month, $day, $year ) or $year < 1970 )
        {
            $dateTime->setTimeStamp( 0 );
        }
        else
        {
            $dateTime->setMDYHMS( $month, $day, $year, $hour, $minute, 0 );
        }

        return $dateTime->timeStamp();
    }
}

?>
