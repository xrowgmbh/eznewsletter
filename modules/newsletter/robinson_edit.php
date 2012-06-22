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

$robinsonlistEntryId = $Params['RobinsonEntryID'];

$robinsonlistEntry = eZRobinsonListEntry::fetchById( $robinsonlistEntryId );

if ( !$robinsonlistEntryId )
{
    $robinsonlistEntry = eZRobinsonListEntry::create();
}

if ( $http->hasPostVariable( 'StoreButton' ) )
{

    if ( ( $http->postVariable( 'RobinsonlistEntry_Value' )!="" ) && 
     ( $http->postVariable( 'RobinsonlistEntry_Type' )!="" ) && 
     ( $http->postVariable( 'RobinsonlistEntry_Global' )!="" ) )
    {
    
    if ( $http->postVariable( 'RobinsonlistEntry_Type' ) == eZRobinsonListEntry::EMAIL ) 
    {
        if ( eZMail::validate ( $http->postVariable( 'RobinsonlistEntry_Value' ) ) )
        {
        $robinsonlistEntry->setAttribute( 'value', $http->postVariable( 'RobinsonlistEntry_Value' ) );
        $robinsonlistEntry->setAttribute( 'type', $http->postVariable( 'RobinsonlistEntry_Type' ) );
        $robinsonlistEntry->setAttribute( 'global', $http->postVariable( 'RobinsonlistEntry_Global' ) );
        $robinsonlistEntry->store();
    
        return $Module->redirectToView( 'robinson_show', array() );
        }
        else
        {
        $warning = ezpI18n::tr( 'eznewsletter/robinson_edit', 'Please enter a valid email.' );
        }
    }
    else
    {
    
        $robinsonlistEntry->setAttribute( 'value', $http->postVariable( 'RobinsonlistEntry_Value' ) );
        $robinsonlistEntry->setAttribute( 'type', $http->postVariable( 'RobinsonlistEntry_Type' ) );
        $robinsonlistEntry->setAttribute( 'global', $http->postVariable( 'RobinsonlistEntry_Global' ) );
        $robinsonlistEntry->store();
    
        return $Module->redirectToView( 'robinson_show', array() );
    }
    }
    else
    {
    $warning = ezpI18n::tr( 'eznewsletter/robinson_edit', 'Please fill in all required fields.' );
    }
}

if ( $http->hasPostVariable( 'CancelButton' ) )
{
    if ( $robinsonlistEntry->attribute( 'value' )=="" )
    {
    $robinsonlistEntry->remove();
    return $Module->redirectToView( 'robinson_show', array() );
    }
    else
    {
    return $Module->redirectToView( 'robinson_show', array() );
    }
}

$tpl = eZNewsletterTemplateWrapper::templateInit();
if ( isset($warning) ) {
    $tpl->setVariable( 'warning', $warning );
}

$tpl->setVariable( 'global_map', eZRobinsonListEntry::globalNameMap() );
$tpl->setVariable( 'type_map', eZRobinsonListEntry::typeNameMap() );
$tpl->setVariable( 'import_map', eZRobinsonListEntry::importNameMap() );
$tpl->setVariable( 'robinsonlistEntry', $robinsonlistEntry );

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/robinson_menu.tpl';
$Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:eznewsletter/robinson_edit.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'eznewsletter/robinson_edit', 'Opt-out list edit' ) ) );

?>
