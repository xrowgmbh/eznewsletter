<?php
//
// Created on: <07-Dec-2005 14:26:45 hovik>
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

/*! \file subscription_list.php
*/

$Module = $Params['Module'];
$http = eZHTTPTool::instance();

if ( $http->hasPostVariable( 'SubmitFilter' ) )
{
    $value = implode( ',', $http->postVariable( 'statusFilter' ));
    eZPreferences::setValue( 'robinsonlist_filter', $value );
}

$statusFilter = eZPreferences::value( 'robinsonlist_filter' );
$limitKey = eZPreferences::value( 'admin_list_limit' );

if ( $statusFilter=="" )
{
    eZPreferences::setValue( 'robinsonlist_filter', '-1' );
    $statusFilter = eZPreferences::value( 'robinsonlist_filter' );
}

if ( $limitKey=="" )
{
    eZPreferences::setValue( 'admin_list_limit', '1' );
    $limitKey = eZPreferences::value( 'admin_list_limit' );
}

if ( $http->hasPostVariable( 'RemoveRobinsonlistEntryButton' ) )
{
    if( $http->hasPostVariable( 'EmailIDArray' ) )
    {
        foreach( $http->postVariable( 'EmailIDArray' ) as $id )
        {
            eZRobinsonListEntry::removeById( $id );
        }
    }
    
    if( $http->hasPostVariable( 'MobileIDArray' ) )
    {    
        foreach( $http->postVariable( 'MobileIDArray' ) as $id )
        {
            eZRobinsonListEntry::removeById( $id );
        }
    }
}
$statusFilterArray = explode( ',', $statusFilter );

$userParameters = $Params['UserParameters'];
//$statusFilter = isset( $userParameters['statusFilter'] ) ? explode( ',', $userParameters['statusFilter'] ) : array( -1 );
//$vipFilter = isset( $userParameters['vipFilter'] ) ? explode( ',', $userParameters['vipFilter'] ) : array( -1 );
$offset = isset( $userParameters['offset'] ) ? $userParameters['offset'] : 0;
//$limitKey = isset( $userParameters['limit'] ) ? $userParameters['limit'] : '1';
$limitList = array ( '1' => 10,
                     '2' => 25,
                     '3' => 50 );

$limit = $limitList[(string)$limitKey];
$viewParameters = array( 'offset' => $offset );

if ( $http->hasPostVariable( 'CreateRobinsonlistEntryButton' ) ) 
{
    $robinsonlistEntry = eZRobinsonListEntry::create();
    return $Module->redirectToView( 'robinson_edit', array( $robinsonlistEntry->attribute( 'id' ) ) );
}

$condArray = array();
if ( !in_array( -1, $statusFilterArray ) )
{
    $condArray['global'] = array( $statusFilterArray );
}

$robinsonlist_Email = eZRobinsonListEntry::fetchByOffset( eZRobinsonListEntry::EMAIL,
                                                          $condArray,
                                                          $offset,
                                                          $limit );

$robinsonlist_Mobile = eZRobinsonListEntry::fetchByOffset( eZRobinsonListEntry::MOBILE,
                                                          $condArray,
                                                          $offset,
                                                          $limit );

$tpl = eZNewsletterTemplateWrapper::templateInit();
$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'statusFilter', $statusFilterArray );
$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'robinsonlist_Email', $robinsonlist_Email );
$tpl->setVariable( 'robinsonlist_Mobile', $robinsonlist_Mobile );
$tpl->setVariable( 'status_map', eZRobinsonListEntry::globalNameMap() );
$tpl->setVariable( 'type_map', eZRobinsonListEntry::typeNameMap() );
$tpl->setVariable( 'robinsonlistCount_Email', eZRobinsonListEntry::countAll( eZRobinsonListEntry::EMAIL, $condArray ) );
$tpl->setVariable( 'robinsonlistCount_Mobile', eZRobinsonListEntry::countAll( eZRobinsonListEntry::MOBILE, $condArray ) );
if ( isset($warning) )
{
    $tpl->setVariable( 'warning', $warning );
}

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/robinson_menu.tpl';
$Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:eznewsletter/robinson_show.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'eznewsletter/robinson_show', 'Opt-out list' ) ) );

?>
