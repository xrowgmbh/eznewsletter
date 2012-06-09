<?php
//
// Definition of eZRecurrence class
//
// Created on: <17-Sep-2007 14:00:00 tos>
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

/*! \file ezrecurrence.php
 */

/*!
  \class eZRecurrence ezrecurrence.php
  \brief The class eZRecurrence is checking recurring conditions
*/

class eZRecurrence
{
    var $conditionExtensions;

    /*
    */
    function __construct()
    {
        $ini = eZINI::instance( 'eznewsletter.ini' );
        $this->conditionExtensions = $ini->variable( 'RecurrenceSettings', 'conditionExtensions' );
    }

    /*
     Asks the condition extension given in the recurrence if it is fullfilled
    */
    function checkRecurrenceCondition( $newsletter )
    {
        if( !$newsletter->attribute( 'recurrence_condition' ) )
        {
            return true;
        }

        if ( 0 < count( $this->conditionExtensions ) )
        {
            foreach ( $this->conditionExtensions as $conditionExtension )
            {
                // TODO: Extend to ask multiple condition extensions to allow more complex checks
                $siteINI = eZINI::instance();
                $siteINI->loadCache();
                $extensionDirectory = $siteINI->variable( 'ExtensionSettings', 'ExtensionDirectory' );
                $extensionDirectories = eZDir::findSubItems( $extensionDirectory );
                $directoryList = eZExtension::expandedPathList( $extensionDirectories, 'condition_handler' );

                foreach( $directoryList as $directory )
                {
                    $handlerFile = $directory . '/' . strtolower( $conditionExtension ) . 'handler.php';

                    // we only check one extension for now
                    if ( $conditionExtension === $newsletter->attribute( 'recurrence_condition') &&
                         file_exists( $handlerFile ) )
                    {
                        include_once( $handlerFile );
                        $className = $conditionExtension . 'Handler';
                        if( class_exists( $className ) )
                        {
                            $impl = new $className();
                            // Ask if condition is fullfilled
                            return $impl->checkCondition( $newsletter );
                        }
                        else
                        {
                            eZDebug::writeError( "Class $className not found. Unable to verify recurrence condition. Blocked recurrence." );
                            return false;
                        }
                    }
                }
            }
        }
        // If we have a condition but no match we prevent the sendout
        eZDebug::writeError( "Newsletter recurrence condition '".$newsletter->attribute( 'recurrence_condition' )."' extension not found " );
        return false;
    }
}

?>
