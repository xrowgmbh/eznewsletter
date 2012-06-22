<?php
//
// Created on: <08-Dec-2005 10:40:16 hovik>
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
// This source file is part of the eZ publish (tm) Open Source Content
// Management System.
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

/*! \file edit_subscription.php
*/

$Module = $Params['Module'];

$http = eZHTTPTool::instance();

if ( $http->hasPostVariable( 'RemoveSubscriptionButton' ) )
{

if (is_array ( $http->postVariable( 'SubscriptionIDArray' )))
{
    foreach( $http->postVariable( 'SubscriptionIDArray' ) as $subscriptionID )
    {
        $userData = eZUserSubscriptionData::fetchById( $subscriptionID );
        $subscriptionList = eZSubscription::fetchListByEmail( $userData->attribute( 'email' ),
                                                              eZSubscription::VersionStatusPublished,
                                                              array( array( eZSubscription::StatusPending,
                                                                            eZSubscription::StatusApproved,
                                                                            eZSubscription::StatusConfirmed,
                                                                            eZSubscription::StatusRemovedSelf,
                                                              eZSubscription::StatusRemovedAdmin ) ) );
        foreach( $subscriptionList as $subscription)
        {
            eZSubscription::removeAll( $subscription->Attribute( 'id' ) );
        }
        eZUserSubscriptionData::removeAll( $subscriptionID );
    }
}
}
if ( $http->hasPostVariable( 'searchString' ) && trim( $http->postVariable( 'searchString' ) ) != ""   )
{

    $search = trim( strtolower( $http->postVariable( 'searchString' ) ) );

    $db = eZDB::instance();
    $searchSQL = "SELECT email FROM ezsubscriptionuserdata WHERE LOWER(firstname) LIKE '%"
               . $db->escapeString( $search ) . "%' or LOWER(name) LIKE '%"
               . $db->escapeString( $search ) . "%' or LOWER(email) LIKE '%"
               . $db->escapeString( $search ) . "%' group by email LIMIT 50";
               
    $searchResult = $db->arrayQuery( $searchSQL );

    $subscriberList = array();
    foreach( $searchResult as $email )
    {
        $subscriberList[] = eZUserSubscriptionData::fetch( $email['email'] );
    }

    $countSQL = "SELECT id FROM ezsubscriptionuserdata WHERE LOWER(firstname) LIKE '%"
              . $db->escapeString( $search ) . "%' or LOWER(name) LIKE '%"
              . $db->escapeString( $search ) . "%' or LOWER(email) LIKE '%"
              . $db->escapeString( $search ) . "%' group by email";

    $countResult = $db->arrayQuery( $countSQL );
    $subscriberCount=count($countResult);
}

$tpl = eZNewsletterTemplateWrapper::templateInit();

if ( $http->hasPostVariable( 'searchString' ) )
{
    $tpl->setVariable( 'searchString', $http->postVariable( 'searchString' ) );
}
else
{
    $tpl->setVariable( 'searchString', '' );
}

if ( isset($subscriberList ) )
{
    $tpl->setVariable( 'subscriberList', $subscriberList );
    $tpl->setVariable( 'subscriberCount', $subscriberCount );
}
else
{
    $tpl->setVariable( 'subscriberList', array() );
    $tpl->setVariable( 'subscriberCount', 0 );
}

$Result = array();
$Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:eznewsletter/subscription_search.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'eznewsletter/subscription_search', 'Edit Subscription' ) ) );

?>
