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
set_time_limit( 0 );

// check if another instance of this script is already running and prevent execution if so
$pidfilename = eZDir::cleanPath( eZSys::varDirectory() . '/run/eznewsletter.pid' );

if ( file_exists( $pidfilename ) )
{
    $pid = file_get_contents( $pidfilename );
    $cli->error( "A newsletter cronjob is already running." );
    $cli->error( "Please wait until the process (PID: $pid) is finished." );
    $cli->error( "If the script crashed during sendout, delete the PID file $pidfilename and restart the script." );
    exit();
}

if ( false == file_exists( dirname( $pidfilename ) ) )
{
    eZDir::mkdir( dirname( $pidfilename ), false, true );
}

if ( ! is_writeable( dirname( $pidfilename ) ) )
{
    $cli->error( "PID file not writeable ( {$pidfilename} ). Please add write access for cronjob." );
    exit( 1 );
}

$pidfile = fopen( $pidfilename, 'w' );
fwrite( $pidfile, getmypid() );
fclose( $pidfile );

// send newsletters


$sendMailSettings = eZINI::instance( 'ezsendmailsettings.ini' );
$newsletterSettings = eZINI::instance( 'eznewsletter.ini' );
$newsletterContentClasses = $newsletterSettings->variable( 'NewsletterContentClasses', 'newsletterContentClassIdent' );
$replaceMsgIDHost = $sendMailSettings->variable( 'SendNewsletter', 'ReplaceMessageIDHost' );
$newSendHost = $sendMailSettings->variable( 'SendNewsletter', 'Host' );
$hostSettings['replace'] = $replaceMsgIDHost;
$hostSettings['host'] = $newSendHost;

$conditions = array();

//pregeneration parameter
if ( in_array( '-pregeneration', $_SERVER['argv'] ) )
{
    //fetch only newsletter with send_date <= now + 1 hour
    $timestamp = time() + 3600;
    $cli->output( 'Pregeneration enabled.' );
}
else
{
    $timestamp = time();
}

$conditions = array_merge( $conditions, array( 
    'send_date' => array( 
        '<=' , 
        $timestamp 
    ) 
) );

//newslettertype parameter
if ( in_array( '-newslettertype', $_SERVER['argv'] ) )
{
    $key = array_search( '-newslettertype', $_SERVER['argv'] );
    $newslettertype_id = $_SERVER['argv'][$key + 1];
    
    if ( is_numeric( $newslettertype_id ) && eZNewsletterType::fetch( $newslettertype_id ) )
    {
        $conditions = array_merge( $conditions, array( 
            'newslettertype_id' => array( 
                '=' , 
                $newslettertype_id 
            ) 
        ) );
        $cli->output( 'Filter for newslettertype <' . $newslettertype_id . '> enabled.' );
    }
    else
    {
        $cli->output( 'Invalid id of newslettertype <' . $newslettertype_id . '>!' );
        eZExecution::cleanup();
        eZExecution::setCleanExit();
    }
}

// 2. Fetch all newsletters with status : eZNewsletter::SendStatusSending, and send date less than current TS.
$cli->output( 'Fetching prepared newsletter...' );
$newsletterArray = eZNewsletter::fetchListBySendStatus( eZNewsletter::SendStatusSending, eZNewsletter::StatusPublished, true, $conditions );

$cli->output( 'Found ' . count( $newsletterArray ) . ' newsletter.' );

if ( 0 === count( $newsletterArray ) )
{
    $cli->output( 'Nothing to send.' );
}
else
{
    $cli->output( 'Sending newsletter...' );
}

foreach ( $newsletterArray as $newsletter )
{
    $cli->output( 'Sending messages for ' . $newsletter->attribute( 'name' ) );
    $statistics = eZNewsletter::sendNewsletterMail( $newsletter );
    $newsletter->setAttribute( 'send_status', eZNewsletter::SendStatusFinished );
    $newsletter->sync();
    $cli->output( 'Sent ' . $statistics['sendCount'] . ' ( skipped:' . $statistics['skipCount'] . ' )' . ' messages for newsletter : ' . $newsletter->attribute( 'name' ) );
}

// remove pid file to unlock cronjob
if ( file_exists( $pidfilename ) )
{
    unlink( $pidfilename );
}
?>
