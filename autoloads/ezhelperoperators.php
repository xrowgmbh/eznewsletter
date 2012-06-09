<?php
//
// Definition of eZHelperOperators class
//
// Created on: <16-Feb-2007 13:12:46 tos>
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

/*! \file ezhelperoperators.php
*/

/*!
  \class eZHelperOperators ezhelperoperators.php
  \brief The class eZHelperOperators does

*/

class eZHelperOperators
{
    /*!
     Constructor
    */
    function __construct()
    {
        $this->Operators = array( 'eZDefaultHostname' );
    }

    /*!
     Returns the operators in this class.
    */
    function operatorList()
    {
        return $this->Operators;
    }

    /*!
     \return true to tell the template engine that the parameter list
    exists per operator type, this is needed for operator classes
    that have multiple operators.
    */
    function namedParameterPerOperator()
    {
        return true;
    }

    /*!
     The operator have one input value
     See eZTemplateOperator::namedParameterList()
    */
    function namedParameterList()
    {
        return array(
            'eZDefaultHostname' => array(
                'cleaned' => array(
                    'type' => 'integer',
                    'required' => false,
                    'default' => false 
                )
            )
        );
    }

    /*!
     Executes the needed operator(s).
     Checks operator names, and calls the appropriate functions.
    */
    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace,
                     $currentNamespace, &$operatorValue, $namedParameters )
    {
        switch ( $operatorName )
        {
            case 'eZDefaultHostname':
            {
                if( true == ( bool )$namedParameters['cleaned'] )
                {
                    // Remove the index.php part. needed for storage and images
                    // $operatorValue = str_replace( 'index.php', '', $this->defaultHostname( ) );
		            // $operatorValue = preg_replace( '/index.php(\?\/)?$/', '', $this->defaultHostname( ) );
		               $operatorValue = preg_replace( '/\/?index.php\??$/', '', $this->defaultHostname( ) );
                }
                else
                {
                    $operatorValue = $this->defaultHostname( );
                }
            } break;
        }
    }

    /*!
        Returns the hostname of the default Siteaccess<
    */
    function defaultHostname( )
    {
        $siteINI = eZINI::instance( 'eznewsletter.ini' );
        return $siteINI->hasVariable( 'HostSettings', 'defaulthost' ) ? $siteINI->variable( 'HostSettings', 'defaulthost' ) : '';
    }

    /// \privatesection
    var $Operators;

}

?>
