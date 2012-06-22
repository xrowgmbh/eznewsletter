<?php
//
// Definition of Send_Newsletter class
//
// Created on: <19-Dec-2005 10:12:14 hovik>
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

/*! \file send_newsletter.php
*/

set_time_limit(0);

//1. get list of all finnished recurring newsletter

$condArray['send_status'] = array( array( eZNewsletter::SendStatusFinished ) );
$condArray['status'] = array( array( eZNewsletter::StatusPublished ) );
$condArray['recurrence_type'] = array( array( 'd', 'w', 'm' ) );

$cli->output( 'Fetching list of recurring newsletters...' );
$newsletterArray =  eZPersistentObject::fetchObjectList( eZNewsletter::definition(),
                                                     null,
                                                     $condArray,
                                                     null,
                                                     null,
                                                     true );

// checks if the recurrence condition is fullfilled
$recurrence = new eZRecurrence();

$cli->output( 'Found '.count($newsletterArray).' recurring newsletter.' );
foreach ($newsletterArray as $newsletter )
{
    // 1.0 check if constraint is fullfilled, skip if not
    if( false === $recurrence->checkRecurrenceCondition( $newsletter ) ||
        in_array( '--force-condition', $_SERVER['argv'] ) )
    {
        $cli->output( 'Condition "'.$newsletter->attribute( 'recurrence_condition' ).'" for newsletter "'.$newsletter->attribute( 'name' ) .'" [id:'.$newsletter->attribute( 'id' ).'] was not fulfilled. Skipping.' );
        continue;
    }

    $cli->output( '' );
    $cli->output( 'Archiving newsletter: '.$newsletter->attribute( 'id' ).' '.$newsletter->attribute( 'name' ) );

    //1.1 copy newsletter and contentobject
    $newObject = $newsletter->copy();
    $oldObject = eZContentObject::fetch( $newsletter->attribute( 'contentobject_id' ) );

    $cli->output( 'Publishing new object...' );

    // publish the newly created object
    eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $newObject->attribute( 'id' ),
                                 'version'   => $newObject->attribute( 'current_version' ) ) );

    $newNewsletter = eZNewsletter::fetchByContentObject( $newObject->attribute( 'id'), $newObject->attribute( 'current_version' ), eZNewsletter::StatusPublished, true);

    if ( $newNewsletter )
    {
        $cli->output( 'Copied newsletter: '.$newsletter->attribute( 'id' ).' to '.$newNewsletter->attribute( 'id' ).' '.$newsletter->attribute( 'name' ) );

        //1.2 remove recurrency of old one
        $newsletter->setAttribute( 'recurrence_type', '' );
        $newsletter->setAttribute( 'recurrence_value', '' );    

        //1.3 set status of new to None for sending
        $newNewsletter->setAttribute( 'send_status', eZNewsletter::SendStatusNone );

        //1.4 set send date for new recurrence
        $oldDate = $newNewsletter->attribute( 'send_date' );

        //echo date("YmdGHjs", $olddate);
        $date = mktime( $newNewsletter->attribute( 'send_hour' ),
                $newNewsletter->attribute( 'send_minute' ),
                $newNewsletter->attribute( 'send_second' ),
                $newNewsletter->attribute( 'send_month' ),
                $newNewsletter->attribute( 'send_day' ),
                $newNewsletter->attribute( 'send_year' ) );

        if ( $newNewsletter->attribute( 'recurrence_type' ) == 'd' )
        {
            echo "daily value: ".$newNewsletter->attribute( 'recurrence_value' )."\n";

            //get next senddate
            $date = getNextSendDateDaily($newNewsletter);

            echo "old: ".date("Y-m-d-H-i", $oldDate)."\n";
            echo "new: ".date("Y-m-d-H-i", $date )."\n";
            $newNewsletter->setAttribute( 'send_date', $date );

        }
        else if ( $newNewsletter->attribute( 'recurrence_type' ) == 'w' )
        {
            echo "weekly value: ".$newNewsletter->attribute( 'recurrence_value' )."\n";
            $date = mktime( $newNewsletter->attribute( 'send_hour' ),
                    $newNewsletter->attribute( 'send_minute' ),
                    $newNewsletter->attribute( 'send_second' ),
                    $newNewsletter->attribute( 'send_month' ),
                    $newNewsletter->attribute( 'send_day' )+7,
                    $newNewsletter->attribute( 'send_year' ) );

            echo "old: ".date("Y-m-d-H-i", $oldDate)."\n";
            echo "new: ".date("Y-m-d-H-i", $date )."\n";
            $newNewsletter->setAttribute( 'send_date', $date );
        }
        else if ( $newNewsletter->attribute( 'recurrence_type' ) == 'm' )
        {
            echo "monthly value: ".$newNewsletter->attribute( 'recurrence_value' )."\n";
            $date = mktime( $newNewsletter->attribute( 'send_hour' ),
                    $newNewsletter->attribute( 'send_minute' ),
                    $newNewsletter->attribute( 'send_second' ),
                    $newNewsletter->attribute( 'send_month' )+1,
                    $newNewsletter->attribute( 'send_day' ),
                    $newNewsletter->attribute( 'send_year' ) );

            echo "old: ".date("Y-m-d-H-i", $oldDate)."\n";
            echo "new: ".date("Y-m-d-H-i", $date )."\n";
            $newNewsletter->setAttribute( 'send_date', $date );
        }
    }
    else
    {
        $cli->output( 'ERROR occured while trying to copy newsletter with ID: '.$newsletter->attribute( 'id' ) );
    }
    $newsletter->store();
    $newNewsletter->store();
    $newNewsletter->removeDraft();
}

function getNextSendDateDaily($newNewsletter)
{
    $cli = eZCLI::instance( );

    //get next senddate
    $counter=1;
    while(true)
    {
    if ( $counter > 7 ) 
    {
        $next_date=false;
        break;
    }

    $next_date = mktime( $newNewsletter->attribute( 'send_hour' ),
                 $newNewsletter->attribute( 'send_minute' ),
                 $newNewsletter->attribute( 'send_second' ),
                 $newNewsletter->attribute( 'send_month' ),
                 $newNewsletter->attribute( 'send_day' )+$counter,
                 $newNewsletter->attribute( 'send_year' ) );

    if ( is_numeric( strpos( $newNewsletter->attribute( 'recurrence_value' ), date("w", $next_date) ) ) )
    {
        break;
    }
    $counter=$counter+1;
    }

    //set next senddate
    $date = mktime( $newNewsletter->attribute( 'send_hour' ),
            $newNewsletter->attribute( 'send_minute' ),
            $newNewsletter->attribute( 'send_second' ),
            date("m", $next_date),
            date("d", $next_date),
            date("Y", $next_date) );

    return $date;
}

?>
