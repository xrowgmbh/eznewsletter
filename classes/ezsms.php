<?php
//
// Created on: <31-May-2006 14:38:20 aw>
//
// Copyright (C) 1999-2006 eZ systems as. All rights reserved.
//
// This source file is part of the eZ publish (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE included in
// the packaging of this file.
//
// Licencees holding a valid "eZ publish professional licence" version 2
// may use this file in accordance with the "eZ publish professional licence"
// version 2 Agreement provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "eZ publish professional licence" version 2 is available at
// http://ez.no/ez_publish/licences/professional/ and in the file
// PROFESSIONAL_LICENCE included in the packaging of this file.
// For pricing of this licence please contact us via e-mail to licence@ez.no.
// Further contact information is available at http://ez.no/company/contact/.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact licence@ez.no if any conditions of this licencing isn't clear to
// you.
//


class eZSMS
{
    var $provider = 'provider';
    var $username;
    var $password;
    var $content = false;
    var $numbers = array();
    var $send_numbers = array();
    var $connection;
    var $fileTransport = false;

    static function instance($fileTransport = false) 
    {
        $class = 'eZSMS';
    
        if ( !isset( $GLOBALS['eZSMSGlobalInstance'] ) ) {
            $instance = new eZSMS();
            $instance->fileTransport=$fileTransport;
            $GLOBALS['eZSMSGlobalInstance'] =& $instance;
        } else {
            $instance =& $GLOBALS['eZSMSGlobalInstance'];
        }
        return $instance;
    }
    
    function destroyInstance()
    {
        if ( isset( $GLOBALS['eZSMSGlobalInstance'] ) )
        {
            unset( $GLOBALS['eZSMSGlobalInstance'] );
        }
    }
    
    function __construct()
    {
      $ini = eZINI::instance('ezsmssettings.ini');
    	$this->messages = array();
    	$this->username = $ini->variable( $this->provider, 'Username' );
    	$this->password = $ini->variable( $this->provider, 'Password' );
    	$this->originator = $ini->variable( $this->provider, 'Originator' );
    }   

    function numberClean ($toClean)
    {
        $search = array("(0)", "+", "-", "(", ")", " ", "");
        $replace_with  = array("", "", "", "", "", "", "");
        $newphrase = str_replace($search, $replace_with, $toClean);
    
        return $newtext = preg_replace("/(^0*)/", "", $newphrase);
    }               						  

    function getConnection()
    {
	   // Create connection here	
	   $this->connection = true;
	
	   return $this->connection;
    }

    function closeConnection()
    {
        //close connection
        return true;
    }

    function addNumber($number)
    {
        if ( !eZRobinsonListEntry::inList( $number, eZRobinsonListEntry::MOBILE ) )
        {
        	$this->numbers[] = $this->numberClean($number);
        }
    }

    function setContent($content)
    {
        $this->content = trim($content);
    }

    function getMessage()
    {
        //create your message here
        return "Create your XML here.";
    }

    function sendMessages()
    {
        if ($this->fileTransport)
        {
            $message = $this->getMessage();
            
            if ( $message ) {
    	       $this->createFile($message);
            } else {
                echo "Getting message XML content failed!";
            }
        }
        else
        {
            if ( $this->getConnection() )
            {
        	    $message = $this->getMessage();
                if ( $message ) {
		          //send messages
                } else {
                    echo "Getting message XML content failed!";
                }
            }
            else {
    	       echo "No connection";
            }
            $this->closeConnection();
        }
    }

    function sendMessageFile($message)
    {
        if ( $this->getConnection() )
        {   
            if ( $message ) {
	           //send your message here
            }else {
                echo "Getting message XML content failed!"."\n";
                return false;
            }
        } else {
            echo "No connection"."\n";
            return false;
        }
        $this->closeConnection();
    }

    function getReply()
    {
        //get reply from provider
        return true;
    }

    function countNumbers()
    {
        return count($this->numbers);
    }

    function getNumbers() 
    {
        return $this->numbers;
    }

    function createFile($message)
    {
        $sys = eZSys::instance();
        $lineBreak = ($sys->osType() == 'win32' ? "\r\n" : "\n" );
        $separator = ($sys->osType() == 'win32' ? "\\" : "/" );
                                        
        $fname = time().'-'.rand().'.sms';
        $qdir = eZSys::siteDir().eZSys::varDirectory().$separator.'mailq';
                                                        
        $data = $message;                                                      
        $data = preg_replace('/(\r\n|\r|\n)/', "\r\n", $data);
                                                                                    
        // echo 'eZFile::create('.$fname.', '.$qdir.', '.$data.');';
        eZFile::create($fname, $qdir, $data);
    }
}
?>
