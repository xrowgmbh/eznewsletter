<?php
//
// Created on: <18-Jan-2006 18:25:13 hovik>
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

/*! \file subscription_import.php
*/

$Module = $Params['Module'];

$http = eZHTTPTool::instance();

$empty_input=true;
$done=false;
$warning="";

if ( $http->hasPostVariable( 'AddButton' ) || $http->hasPostVariable( 'RemoveButton' )  )
{
    if ( $http->hasPostVariable( 'RobinsonlistEntry_Email' ) )
    {
    if ( $http->postVariable( 'RobinsonlistEntry_Email' ) != "" )
    {    
        $empty_input=false;
        if ( eZMail::validate ( $http->postVariable( 'RobinsonlistEntry_Email' ) ) )
        {
        if ( $http->hasPostVariable( 'AddButton' ) )
        {
            if ( !eZRobinsonListEntry::inList( $http->postVariable( 'RobinsonlistEntry_Email' ), eZRobinsonListEntry::EMAIL ) )
            {
            eZRobinsonListEntry::create( $http->postVariable( 'RobinsonlistEntry_Email' ), eZRobinsonListEntry::EMAIL, eZRobinsonListEntry::IMPORT_LOCAL );
            $done=true;
            }
            else
            {
            $warning = ezpI18n::tr( 'eznewsletter/robinson_user', 'Entered email address is already in the list.' );
            $done=true;
            }
            
        }
        else if ( $http->hasPostVariable( 'RemoveButton' ) )
        {
            if ( eZRobinsonListEntry::inList( $http->postVariable( 'RobinsonlistEntry_Email' ), eZRobinsonListEntry::EMAIL, eZRobinsonListEntry::IMPORT_LOCAL ) )
            {
            eZRobinsonListEntry::removeByValue( $http->postVariable( 'RobinsonlistEntry_Email' ), eZRobinsonListEntry::EMAIL, eZRobinsonListEntry::IMPORT_LOCAL );
            $done=true;
            }
            else
            {
            $warning = ezpI18n::tr( 'eznewsletter/robinson_user', 'Entered email address is not in the list.' );
            $done=true;
            }
        }
        }
        else
        {
        $warning = ezpI18n::tr( 'eznewsletter/robinson_user', 'Please enter a valid email.' );
        $done=true;
        }
    }
    }
    
    if ( $http->hasPostVariable( 'RobinsonlistEntry_Mobile' ) )
    {
    if ( $http->postVariable( 'RobinsonlistEntry_Mobile' ) != "" )
    {    
        $empty_input=false;
        if ( $http->hasPostVariable( 'AddButton' ) )
        {
            if ( !eZRobinsonListEntry::inList( $http->postVariable( 'RobinsonlistEntry_Mobile' ), eZRobinsonListEntry::MOBILE ) )
            {
            eZRobinsonListEntry::create( $http->postVariable( 'RobinsonlistEntry_Mobile' ), eZRobinsonListEntry::MOBILE, eZRobinsonListEntry::IMPORT_LOCAL );
            $done=true;
            }
            else
            {
            $warning = ezpI18n::tr( 'eznewsletter/robinson_user', 'Entered mobile phone number is already in the list.' );
            $done=true;
            }
        }
        else if ( $http->hasPostVariable( 'RemoveButton' ) )
        {
            if ( eZRobinsonListEntry::inList( $http->postVariable( 'RobinsonlistEntry_Mobile' ), eZRobinsonListEntry::MOBILE, eZRobinsonListEntry::IMPORT_LOCAL ) )
            {
            eZRobinsonListEntry::removeByValue( $http->postVariable( 'RobinsonlistEntry_Mobile' ), eZRobinsonListEntry::MOBILE, eZRobinsonListEntry::IMPORT_LOCAL );
            $done=true;
            }
            else
            {
            $warning = ezpI18n::tr( 'eznewsletter/robinson_user', 'Entered mobile phone number is not in the list.' );
            $done=true;
            }
        }
    }
    }

    if ( ( !$done ) && ( $warning == "" ) )
    {
    $warning = ezpI18n::tr( 'eznewsletter/robinson_user', 'An error occured, no updates were made.' );
    }
    
    if ( ( $done ) && ( $warning == "" ) )
    {
    $warning = ezpI18n::tr( 'eznewsletter/robinson_user', 'Updates complete.' );
    }
    
    if ( ( $empty_input ) && ( $warning == ""  ) )
    {
    $warning = ezpI18n::tr( 'eznewsletter/robinson_user', 'You must fill in at least one field.' );
    }

}

$tpl = eZNewsletterTemplateWrapper::templateInit();
if ( $warning != "" ) {
    $tpl->setVariable( 'warning', $warning );
}

$Result = array();
$Result['content'] = $tpl->fetch( "design:eznewsletter/robinson_user.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'eznewsletter/robinson_user', 'Robinsonlist settings' ) ) );

?>
