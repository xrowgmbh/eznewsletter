<?php
//
// Created on: <06-Dec-2005 13:19:07 hovik>
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

/*! \file list_subscriptions.php
*/

$Module = $Params['Module'];
$http = eZHTTPTool::instance();

if ( $http->hasPostVariable( 'CreateSubscriptionListButton' ) )
{
    $subscriptionList = eZSubscriptionList::create();
    return $Module->redirectToView( 'edit_subscription_list', array( $subscriptionList->attribute( 'id' ) ) );
}
else if ( $http->hasPostVariable( 'RemoveSubscriptionListButton' ) )

{
     if (is_array ($http->postVariable( 'SubscriptionListIDArray' )))
      {
          foreach( $http->postVariable( 'SubscriptionListIDArray' ) as $subscriptionID )
       {
             eZSubscriptionList::removeAll( $subscriptionID );
       }
    }
}

$userParameters = $Params['UserParameters'];
$offset = isset( $userParameters['offset'] ) ? $userParameters['offset'] : 0;
$limitKey = isset( $userParameters['limit'] ) ? $userParameters['limit'] : '1';
$limitList = array ( '1' => 10,
                     '2' => 25,
                     '3' => 50 );

$limit = isset( $limitList[$limitKey] ) ? $limitList[$limitKey] : 1;

$viewParameters = array( 'offset' => $offset,
                         'limit' => $limitKey );

$subscriptionListArray = eZSubscriptionList::fetchList( $offset, $limit, true, eZSubscriptionList::StatusPublished, true );

$tpl = eZNewsletterTemplateWrapper::templateInit();
$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'subscriptionlist_array', $subscriptionListArray );

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/robinson_menu.tpl';
$Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:eznewsletter/list_subscriptions.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'eznewsletter/list_subscriptions', 'Subscription lists' ) ) );

?>
