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
$db = eZDB::instance();

if ( $http->hasPostVariable( 'SubmitFilter' ) )
{
    $value = implode( ',', $http->postVariable( 'statusFilter' ));
    eZPreferences::setValue( 'subscriptionlist_filter', $value );
}

$statusFilter = eZPreferences::value( 'subscriptionlist_filter' );
$limitKey = eZPreferences::value( 'admin_list_limit' );

if ( $statusFilter=="" )
{
    eZPreferences::setValue( 'subscriptionlist_filter', '-1' );
    $statusFilter = eZPreferences::value( 'subscriptionlist_filter' );
}

if ( $limitKey=="" )
{
    eZPreferences::setValue( 'admin_list_limit', '1' );
    $limitKey = eZPreferences::value( 'admin_list_limit' );
}



$subscriptions = "";
$subscriptionUserIDs = "";

$subscriptions = eZSubscription::fetchListBySubscriptionListID( $Params['SubscriptionListID'] );


if  ($http->hasPostVariable( 'RemoveSubscriptionButton' ) ) 
{

	foreach($subscriptions as $subscription)
	{
		$subscriptionUserIDs = $subscription->Email;
	}
	$emailCount = 1;


	if (is_array ($http->postVariable( 'SubscriptionIDArray' )))
	{
		foreach( $http->postVariable( 'SubscriptionIDArray' ) as $subscriptionID )
		{

			$userDeleteID  = $db->arrayQuery( "SELECT email 
						      FROM ezsubscription
						      WHERE id= '$subscriptionID'" );
			
			if (count ($userDeleteID > 0) )
			{
			$userEmail = $userDeleteID[0]['email'];
			}

			$userDeleteEmail  = $db->arrayQuery( "SELECT count(email) as emailcount
					       	      FROM ezsubscription
						      WHERE email= '$userEmail'
						      GROUP BY email");

			if (count ($userDeleteEmail > 0))
			{
			$count = $userDeleteEmail[0];
			}
			
			if( (int)$count > $emailCount )
			{ 	
				
			}
			else 
			{	
				

				$userDeleteSubscription = $db->query( "DELETE FROM ezsubscriptionuserdata
								WHERE email = '$userEmail'");

				$userDeleteUserData     = $db->query( "DELETE FROM ezsubscription
								WHERE email = '$userEmail'");

			}

		}
	
	}	 
}


if ($http->hasPostVariable( 'RemoveSingleSubscriptionButton' ) )
{
    foreach( $http->postVariable( 'SubscriptionIDArray' ) as $subscriptionID )
    {
        eZSubscription::removeAll( $subscriptionID );
    }
}

/*
$userParameters = $Params['UserParameters'];
$statusFilter = isset( $userParameters['statusFilter'] ) ? explode( ',', $userParameters['statusFilter'] ) : array( -1 );
//$vipFilter = isset( $userParameters['vipFilter'] ) ? explode( ',', $userParameters['vipFilter'] ) : array( -1 );
$offset = isset( $userParameters['offset'] ) ? $userParameters['offset'] : 0;
$limitKey = isset( $userParameters['limit'] ) ? $userParameters['limit'] : '1';
$limitList = array ( '1' => 10,
                     '2' => 25,
                     '3' => 50 );

$limit = $limitList[(string)$limitKey];

$viewParameters = array( 'offset' => $offset,
                         'limitkey' => $limitKey,
                         'statusFilter' => $statusFilter );
//                         'vipFilter' => $vipFilter );
*/
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

$subscriptionList = eZSubscriptionList::fetch( $Params['SubscriptionListID'] );

if ( !$subscriptionList )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}


if ( $http->hasPostVariable( 'CreateSubscriptionButton' ) )
{
    $subscription = eZSubscription::create( $subscriptionList->attribute( 'id' ) );
    return $Module->redirectToView( 'edit_subscription', array( $subscription->attribute( 'id' ) ) );
}

/*
$condArray = array();
if ( !in_array( -1, $statusFilter ) )
{
    $condArray['status'] = array( $statusFilter );
}
//if ( !in_array( -1, $vipFilter ) )
//{
//    $condArray['vip'] = array( $vipFilter );
//}
*/

$condArray = array();
if ( !in_array( -1, $statusFilterArray ) )
{
    $condArray['status'] = array( $statusFilterArray );
}

$subscriberList = eZSubscription::fetchListBySubscriptionListID( $subscriptionList->attribute( 'id' ),
                                                                 $condArray,
                                                                 $offset,
                                                                 $limit );

//$viewParameters['statusFilter'] = $userParameters['statusFilter'];

$tpl = eZNewsletterTemplateWrapper::templateInit();
$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'statusFilter', $statusFilterArray );
$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'subscriptionList', $subscriptionList );
$tpl->setVariable( 'subscriberList', $subscriberList );
//$tpl->setVariable( 'vip_map', eZSubscription::vipNameMap() );
$tpl->setVariable( 'status_map', eZSubscription::statusNameMap() );
$tpl->setVariable( 'loginSteps_map', eZSubscriptionList::loginStepsNameMap() );
$tpl->setVariable( 'subscriptionCount', eZSubscription::count( $subscriptionList->attribute( 'id' ), eZSubscription::VersionStatusPublished, $condArray ) );

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/robinson_menu.tpl';
$Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:eznewsletter/subscription_list.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'eznewsletter/subscription_list', 'Subscription list' ) ) );

?>
