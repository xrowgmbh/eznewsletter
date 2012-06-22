<?php 

// Definition of Send_Newsletter class

// Created on: <19-Dec-2005 10:12:14 hovik>

// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// COPYRIGHT NOTICE: Copyright (C) 1999-2006 eZ systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
// This program is free software; you can redistribute it and/or
// modify it under the terms of version 2.0  of the GNU General
// Public License as published by the Free Software Foundation.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of version 2.0 of the GNU General
// Public License along with this program; if not, write to the Free
// Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
// MA 02110-1301, USA.


// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##

/*! \file cluster_send.php
*/
set_time_limit( 0 );

function microtime_float2()
{
    list( $usec, $sec ) = explode( " ", microtime() );
    return ( ( float )$usec + ( float )$sec );
} 

function regexEmail()
{
    // RegEx begin
    $nonascii      = "\x80-\xff"; # Non-ASCII-Chars are not allowed

    $nqtext        = "[^\\\\$nonascii\015\012\"]";
    $qchar         = "\\\\[^$nonascii]";

    $normuser      = '[a-zA-Z0-9][a-zA-Z0-9_.-]*';
    $quotedstring  = "\"(?:$nqtext|$qchar)+\"";
    $user_part     = "(?:$normuser|$quotedstring)";

    $dom_mainpart  = '[a-zA-Z0-9][a-zA-Z0-9._-]*\\.';
    $dom_subpart   = '(?:[a-zA-Z0-9][a-zA-Z0-9._-]*\\.)*';
    $dom_tldpart   = '[a-zA-Z]{2,5}';
    $domain_part   = "$dom_subpart$dom_mainpart$dom_tldpart";
        
    $regex         = "$user_part\@$domain_part";

    return $regex;
}

function nextConnection( $active_server, $serverlist, $counter )
{
    $cli = eZCLI::instance( );

    $sum = count( $serverlist );
    if ( $active_server == $sum )
    {
        $active_server = 1;
        $cli->output( "Switching to " . $serverlist[$active_server]['host'] . " after " . $counter );
    }
    else
    {
        $active_server++;
        $cli->output( "Switching to " . $serverlist[$active_server]['host'] . " after " . $counter );
    }
    return $active_server;
} 

function getConnection( $active_server, $serverlist )
{
    $cli = eZCLI::instance( );
    #$connection = eZClusterSMTP::connect( $serverlist[$active_server] );
    $connection = eZClusterSMTP::instance( $serverlist[$active_server] );

    if ( count( $connection->errors ) == 0 )
    { 
        // echo "Connected to ".$serverlist[$active_server]['host']."\n";
        return $connection;
    }
    else
    {
        $cli->output( "ERROR while connecting to " . $serverlist[$active_server]['host'] );
        foreach ( $connection->errors as $error )
        {
            $cli->output( "Server Respond: " . $error );
        }
        return false;
    }
}

function testConnection( $parameters )
{
    $cli = eZCLI::instance( );
    #$connection = eZClusterSMTP::connect( $parameters );
    $connection = eZClusterSMTP::instance( $parameters );

    if ( count( $connection->errors ) == 0 )
    {
        $cli->output( "Connected to " . $parameters['host'] );
        return true;
    }
    else
    {
        $cli->output( "ERROR while connecting to " . $parameters['host'] );
        foreach ( $connection->errors as $error )
        {
            $cli->output( "Server Respond: " . $error );
        }
        return false;
    }
    $connection->quit();
}

// check if another instance of this script is already running and prevent execution if so
$pidfilename = eZDir::cleanPath( eZSys::varDirectory().'/run/eznewsletter.pid' ); 

if( file_exists( $pidfilename ) ) {
    $pid = file_get_contents( $pidfilename );
    $cli->error( "A newsletter cronjob is already running." );
    $cli->error( "Please wait until the process (PID: $pid) is finished." );
    $cli->error( "If the script crashed during sendout, delete the PID file $pidfilename and restart the script." );
    exit();
}

if( false == file_exists( dirname( $pidfilename ) ) ) {
    eZDir::mkdir( dirname( $pidfilename ), false, true );
}

if( !is_writeable( dirname( $pidfilename ) ) )
{
    $cli->error( "PID file not writeable ( {$pidfilename} ). Please add write access for cronjob." );
    exit(1);
}


$pidfile = fopen( $pidfilename, 'w' );
fwrite( $pidfile, getmypid() );
fclose( $pidfile );

$serverlist = array();
$mailSettings = eZINI::instance( 'ezsmtpclustersettings.ini' );
$mailAccounts = $mailSettings->variable( 'SMTPAccountSettings', 'AccountList' );
$packageSize = intval( $mailSettings->variable( 'RoundRobinSettings', 'PackageSize' ) );
$maxRetry = intval( $mailSettings->variable( 'RoundRobinSettings', 'MaximumRetry' ) );

$cli->output( "Setting RoundRobin packagesize to: " . $packageSize );
$cli->output( "Found " . count( $mailAccounts ) . " Account(s)" );
// get smtp accounts
$i = 1;

foreach ( $mailAccounts as $account )
{
    $parameters = array();
    $parameters['host'] = $mailSettings->variable( $account, 'ServerName' );
    $parameters['helo'] = $mailSettings->variable( $account, 'ServerName' );
    $parameters['port'] = $mailSettings->variable( $account, 'ServerPort' );

    if ( $mailSettings->hasVariable( $account, 'LoginName' ) and $mailSettings->hasVariable( $account, 'Password' ) )
    {
        $parameters['auth'] = true;
        $parameters['user'] = $mailSettings->variable( $account, 'LoginName' );
        $parameters['pass'] = $mailSettings->variable( $account, 'Password' );
    }

    if ( testConnection( $parameters ) )
    {
        $serverlist[$i] = $parameters;
        $i++;
    }
}

if ( count( $serverlist ) == 0 )
{
    $cli->output( "No valid mailserver accounts found!" );
    exit( 1 );
} 

$cli->output( "Available accounts: " . count( $serverlist ) );
// get mail-files
$sys = eZSys::instance();

$separator = ( $sys->osType() == 'win32' ? "\\" : "/" );
$qdir = eZSys::siteDir() . eZSys::varDirectory() . $separator . 'mailq' . $separator;

$cli->output( "Fetching mail source files" );

$mailFiles = glob( $qdir . '*.mail' );
$cli->output( "Sending " . count( $mailFiles ) . " emails" );

$robinCounter = 0;
$active_server = 1;
$time_start = microtime_float2();

$smtp = getConnection( $active_server, $serverlist );
$allowPersistent = $mailSettings->variable( 'SMTPClusterSettings', 'PersistentConnection' ) == 'enabled' ? true : false; 

for( $i = 0; $i < count( $mailFiles ); $i++ )
{ 
    // sending message
    $email = file_get_contents( $mailFiles[$i] );
    $lines = file( $mailFiles[$i] );

    // get from and to
    $regex = regexEmail();
    $expression = "/($regex)/";
    preg_match_all( $expression , $lines[1], $matches );
    $from_address = array_pop( $matches[1] );
    preg_match_all( $expression , $lines[7], $matches );
    $to_address = array_pop( $matches[1] );

    eZMail::extractEmail( $from_address, $from, $name );
    eZMail::extractEmail( $to_address, $to, $name );

    if ( eZMail::validate( $from ) && eZMail::validate( $to ) )
    {
        if ( $smtp->send( $from, $to, $email ) )
        {
            $cli->output( ".", false ); 
            // rename file on hdd
            eZFile::rename( $mailFiles[$i], $mailFiles[$i] . ".send" );
            if ( !$allowPersistent )
            {
                $smtp->quit();
                $smtp = getConnection( $active_server, $serverlist );
            }
        }
        else
        {
            if ( count( $serverlist ) > 1 )
            {
                // retrying other server
                $active_server = nextConnection( $active_server, $serverlist, $i + 1 );
                $smtp = getConnection( $active_server, $serverlist );
                $try = 1;
                while ( !$smtp->send( $from, $to, $email ) && ( $try <= $maxRetry ) )
                {
                    $active_server = nextConnection( $active_server, $serverlist, $i + 1 );
                    $smtp = getConnection( $active_server, $serverlist );
                    $try++;
                }

                if ( $try == $maxRetry )
                {
                    eZFile::rename( $mailFiles[$i-1], $mailFiles[$i] . ".notsend" );
                }
            }
            else
            {
                eZFile::rename( $mailFiles[$i-1], $mailFiles[$i] . ".notsend" );
            }
        }
    }

    if ( ( $robinCounter >= $packageSize ) && ( count( $serverlist ) != 1 ) )
    {
        $time_end = microtime_float2();
        $time = $time_end - $time_start;
        $cli->output( "Sent " . $robinCounter . " emails in " . number_format( $time, 3 ) . " seconds with " . $serverlist[$active_server]['host'] );
        $cli->output( "Average speed: " . number_format( ( ( float )60.0 * ( float )$robinCounter ) / ( ( float )$time ), 3 ) . " emails/minute" );

        $robinCounter = 0;
        $active_server = nextConnection( $active_server, $serverlist, $i + 1 );
        $time_start = microtime_float2();
    } 

    if ( eZMail::validate( $from ) && eZMail::validate( $to ) )
    {
        $robinCounter++;
    }
}

if ( count( $serverlist ) == 1 )
{
    $time_end = microtime_float2();
    $time = $time_end - $time_start;
    $cli->output( "Sent " . $robinCounter . " emails in " . number_format( $time, 3 ) . " seconds with " . $serverlist[$active_server]['host'] );
    $cli->output( "Average speed: " . number_format( ( ( float )60.0 * ( float )$robinCounter ) / ( ( float )$time ), 3 ) . " emails/minute" );
}

// remove pid file to unlock cronjob
if( file_exists( $pidfilename ) ) {
    unlink( $pidfilename );
}
?>
