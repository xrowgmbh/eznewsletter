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

/*! \file cluster_send.php
*/
set_time_limit(0);

//get mail-files
$sys = eZSys::instance();
$lineBreak =  ($sys->osType() == 'win32' ? "\r\n" : "\n" );
$separator =  ($sys->osType() == 'win32' ? "\\" : "/" );
$qdir = eZSys::siteDir().eZSys::varDirectory().$separator.'mailq'.$separator;

echo "\n";
echo "Fetching mail source files"."\n";

$mailFiles=glob($qdir.'*.sms');
echo "Sending ".count($mailFiles)." sms messages"."\n\n";

$robinCounter=0;
$active_server=1;
$time_start=microtime_float1();

$instance = eZSMS::instance();

if ( !$instance )
{
    echo "Could not initialize SMS class!"."\n";
    eZExecution::cleanup();
    eZExecution::setCleanExit();
    exit(1);		    
}

$counter=1;
for($i=0; $i<count($mailFiles); $i++)
{    
    //sending message
    echo "Sending ".$counter.": ".basename($mailFiles[$i])."\n";
    echo "Reply:"."\n";
    $sms = file_get_contents($mailFiles[$i]);
    $lines = file($mailFiles[$i]);

    $reply = $instance->sendMessageFile($sms);
    $reply = html_entity_decode($reply);
    echo $reply."\n";
    
    if ( $reply )
    {
    //rename file on hdd
    rename($mailFiles[$i],$mailFiles[$i].".send");
    
    } else {
    rename($mailFiles[$i-1],$mailFiles[$i].".notsend");
    }
    $counter++;
}
$instance->destroyInstance();

$time_end=microtime_float1();
$time = $time_end - $time_start;
echo "Send ".count($mailFiles)." messages in ".number_format($time,3)." seconds."."\n";
echo "Average speed: ".number_format(((float)60.0*(float)count($mailFiles))/((float)$time),3)." sms/minute"."\n";

function microtime_float1()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

echo "\n";
?>
