<?php
//
// Definition of eZClusterSMTP class
//
// Created on: <11-Sep-2007 10:21:19 dieding>
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

/*! \file ezclustersmtp.php
*/

/*!
  \class eZClusterSMTP ezclustersmtp.php
  \brief The class eZClusterSMTP does

*/

class eZClusterSMTP extends smtp 
{  
    private static $instance = NULL;
    
    static function instance( $params =  array())
    {
        if( !is_object( self::$instance ) )
        {
            $obj = new eZClusterSMTP( $params );
            self::$instance = $obj;
        
            if( $obj->connect() )
            {
                $obj->status = smtp::STATUS_CONNECTED;
            }
            
            return $obj;
        }
        else return self::$instance;
    }
    
    protected function __construct( $params = array() )
    {
        parent::__construct( $params );
    }
    
    private final function __clone() {}
    
    function connect($params = array())
    {
        #if ( !isset( $this->status ) )
        #{
        #    $obj = new eZClusterSMTP( $params );
        #    if( $obj->connect() )
        #    {
        #        $obj->status = SMTP_STATUS_CONNECTED;
        #    }
        #    return $obj;
        #}
        #else
        #{
        # ...        
        #}        
        
        $this->connection = fsockopen( $this->host, $this->port, $errno, $errstr, $this->timeout );
        if ( function_exists( 'socket_set_timeout' ) )
        {
            @socket_set_timeout( $this->connection, 5, 0 );
        }

        $greeting = $this->get_data();
        if ( is_resource( $this->connection ) )
        {
           return $this->auth ? $this->ehlo() : $this->helo();
        }
        else
        {
            $this->errors[] = 'Failed to connect to server: ' . $errstr;
            return false;
        }
    }
    
    function send( $from, $to, $email )
    {
        $cli = eZCLI::instance();
        if ( $this->is_connected() )
        {
            $mailSent = true;

            if ( $this->auth AND !$this->authenticated )
            {
                if ( !$this->auth() )
                {
                    $mailSent = false;
                }
            }
            if ( $mailSent )
            {
                if ( !$this->mail( $from ) )
                    $this->errors[] = 'Bad from address!';

                if ( !$this->rcpt( $to ) )
                    $this->errors[] = 'Bad to address!';

                if ( !$this->data() )
                {
                    $cli->output( "Cannot send data!" ); 
                    return false;
                }
                // Transparency
                $email = str_replace( CLRF . '.', CRLF . '..', $email );
                $email = preg_replace( '/(\r\n|\r|\n)/', "\r\n", $email );

                $this->send_data( $email );
                $this->send_cmd( '.', '250' );

                $mailSent = true;
            }
        }
        else
        {
            $this->errors[] = 'Not connected!';
        } 

        if ( isset( $this->errors[0] ) )
        {
            eZDebug::writeError( "Error sending SMTP mail: " . $smtp->errors[0], "eZNewsletterSMTPTransport::sendMail()" );
            $cli->output( "SMTP ERROR: " . $this->errors[0] );
            $this->quit();
            $mailSent = false;
        }
        return $mailSent;
    }
}
?>
