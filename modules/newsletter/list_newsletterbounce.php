<?php
//
// Created on: <06-Dec-2005 10:18:58 oms>
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

/*! \file list_newsletterbounce.php
 */

$extension = 'eznewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";

$http = eZHTTPTool::instance();
$Module = $Params['Module'];
$db = eZDB::instance();

$userParameters = $Params['UserParameters'];
$offset = isset( $userParameters['offset'] ) ? $userParameters['offset'] : 0;
$limitList = array ( '1' => 10,
                     '2' => 25,
                     '3' => 50 );
$limit = $limitList[(string)(isset( $userParameters['limitkey'] ) ? $userParameters['limitkey'] : '1' )];
$viewParameters = array( 'offset' => $offset,
                         'limitkey' => ( isset( $userParameters['limitkey'] ) ? $userParameters['limitkey'] : 1 ) );

$tpl = eZNewsletterTemplateWrapper::templateInit();
$tpl->setVariable( 'module', $Module );

$mode = $Params['Mode'];
$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'limit', $limit );

switch( $mode )
{
    case 'onhold':
    {
        $bounceID = $Params['BounceID'];
        if ( isset( $bounceID) && is_numeric( $bounceID ) )
        {
            //Update eZSendNewsletteritem entry
            $onHoldItem = eZSendNewsletterItem::fetch( $bounceID );

            if( $onHoldItem )
            {
                $tpl->setVariable( 'onhold_item', $onHoldItem );
                $tpl->setVariable( 'send_status', eZSendNewsletterItem::sendStatusNameMap() );

                if( $http->hasPostVariable( 'EditOnHold' )  && $http->hasPostVariable( 'NewSendStatus' ) )
                {
                    $onHoldItem->setAttribute( 'send_status', $http->postVariable( 'NewSendStatus' ) );
                    $onHoldItem->store();
                    $Module->redirectToView( 'list_bounce', array( 'onhold', $bounceID ) );
                }


                $Result = array();
                $Result['newsletter_menu'] = 'design:parts/content/bounce_menu.tpl';
                $Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
                $Result['content'] = $tpl->fetch( "design:$extension/edit_newsletter_onhold.tpl" );
                $Result['path'] = array( array( 'url' => false,
                                                'text' => ezpI18n::tr( 'eznewsletter/list_newsletterbounce', 'Messages on hold' ) ) );
                return;
            }
        }
        else if ( $http->hasPostVariable( 'RemoveOnHoldEntryButton' ) )
        {
            $onHoldEntryIDArray = $http->postVariable( 'OnHoldIDArray' );
            $http->setSessionVariable( 'OnHoldIDArray', $onHoldEntryIDArray );
            $itemsOnHold = array();

            if( count( $onHoldEntryIDArray ) > 0 )  	
            {
                foreach( $onHoldEntryIDArray as $onHoldID )
                {
                    $onHold = eZSendNewsletterItem::fetch( $onHoldID );
                    $itemsOnhold[] = $onHold;
                }
            }
            $tpl->setVariable( 'delete_result', $itemsOnHold );
            $Result = array();
            $Result['newsletter_menu'] = 'design:parts/content/bounce_menu.tpl';
            $Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
            $Result['content'] = $tpl->fetch( "design:$extension/confirmremove_onhold.tpl" );
            $Result['path'] = array( array( 'url' => false,
                                            'text' => ezpI18n::tr( 'eznewsletter/list_newsletterbounce', 'Newsletter items on hold' ) ) );
            return;
        }
        else if ( $http->hasPostVariable( 'ConfirmRemoveOnHoldEntryButton' ) )
        {
            $onHoldEntryIDArray = $http->sessionVariable( 'OnHoldIDArray' );

            $db->begin();
            if( count( $onHoldEntryIDArray ) > 0 )
            {
                foreach ( $onHoldEntryIDArray as $onHoldID )
                {
                    eZSendNewsletterItem::removeEntry( $onHoldID );
                }
            }
            $db->commit();
        }
        $sendItemsOnHold = eZSendNewsletterItem::fetchObjectList( eZSendNewsletterItem::definition(),
                                                                  null,
                                                                  array( 'send_status' => eZSendNewsletterItem::SendStatusOnHold ),
                                                                  array( 'id' => 'asc' ),
								                                  array( 'offset' => $offset, 'length' => $limit ));

        $tpl->setVariable( 'onhold_items', $sendItemsOnHold );

        $Result = array();
        $Result['newsletter_menu'] = 'design:parts/content/bounce_menu.tpl';
        $Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
        $Result['content'] = $tpl->fetch( "design:$extension/list_newsletter_onhold.tpl" );
        $Result['path'] = array( array( 'url' => false,
                                            'text' => ezpI18n::tr( 'eznewsletter/list_newsletterbounce', 'Messages on hold' ) ) );
        return;
    }break;

    case 'all':
    default:
    {
        $bounceID = $Params['BounceID'];
        $sendItemBounced = eZSendNewsletterItem::fetch( $bounceID );

        if ( $sendItemBounced)
        {
        	
            $bounceObject = eZBounce::fetchBySendItemID($bounceID);
            
            $tpl->setVariable( 'bounce_object', $bounceObject );
            $tpl->setVariable( 'sendnewsletteritem_bounced', $sendItemBounced );
            $subscriptionObject = eZSubscription::fetch( $sendItemBounced->attribute( 'subscription_id' ) );
            if ( $subscriptionObject )
            {
                $tpl->setVariable( 'subscription_object', $subscriptionObject );
            }
            $tpl->setVariable( 'statusNames', eZSubscription::statusNameMap() );

            $Result = array();
            $Result['newsletter_menu'] = 'design:parts/content/bounce_menu.tpl';
            $Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
            $Result['content'] = $tpl->fetch( "design:$extension/view_newsletter_bounce.tpl" );
            $Result['path'] = array( array( 'url' => false,
                                            'text' => ezpI18n::tr( 'eznewsletter/list_newsletterbounce', 'View newsletter bounce entry' ) ) );
            return;
        }
        else if( $http->hasPostVariable( 'EditButton' ) && $http->hasPostVariable( 'NewSubscriptionStatus' ) &&  $http->hasPostVariable( 'SubscriptionID' ))
        {
        		$subscriptionObject = eZSubscription::fetch( $http->postVariable( 'SubscriptionID' ) );
                $subscriptionObject->setAttribute( 'status', $http->postVariable( 'NewSubscriptionStatus' ) );
                $subscriptionObject->store();
        }
        else if ( $http->hasPostVariable( 'RemoveBounceEntryButton' ) )
        {
        	if ($http->hasPostVariable( 'MailArray' ))
        	{
        		$bounceEntryIDArray = array();
        		foreach ($http->postVariable( 'MailArray' ) as $mail)
        		{
        			foreach (eZBounce::fetchListByAddress($mail) as $bounce)
        			{
        				array_push($bounceEntryIDArray, $bounce->ID);
        			}
        		}
        	}
        	else
        	{
        		$bounceEntryIDArray = $http->postVariable( 'BounceIDArray' );
        	}
            
            $http->setSessionVariable( 'BounceIDArray', $bounceEntryIDArray );
            $bounces = array();

            if( count( $bounceEntryIDArray ) > 0 )
            {
                foreach( $bounceEntryIDArray as $bounceID )
                {
                    $bounce = eZBounce::fetch( $bounceID );
                    $bounces[] = $bounce;
                }
            }
            $tpl->setVariable( 'delete_result', $bounces );
            $Result = array();
            $Result['newsletter_menu'] = 'design:parts/content/bounce_menu.tpl';
            $Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
            $Result['content'] = $tpl->fetch( "design:$extension/confirmremove_bounce.tpl" );
            $Result['path'] = array( array( 'url' => false,
                                            'text' => ezpI18n::tr( 'eznewsletter/list_newsletterbounce', 'Newsletter types' ) ) );
            return;
        }
        else if ( $http->hasPostVariable( 'RemoveAndUnSubscribeButton' ) )
        {  
        	if ($http->hasPostVariable( 'MailArray' ))
        	{
        		foreach ($http->postVariable( 'MailArray' ) as $mail)
        		{
        			foreach (eZBounce::fetchListByAddress($mail) as $bounce)
        			{
                    	$bounces[] = eZBounce::fetch( $bounce->ID );
        			}
        		}
        	}
            
        	$http->setSessionVariable( 'BounceIDArray', $bounces );
        	
        	$tpl->setVariable( 'delete_result', $bounces );
        	
        	$Result = array();
            $Result['newsletter_menu'] = 'design:parts/content/bounce_menu.tpl';
            $Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
            $Result['content'] = $tpl->fetch( "design:$extension/confirmremoveandunsubscribe_bounce.tpl" );
            $Result['path'] = array( array( 'url' => false,
                                            'text' => ezpI18n::tr( 'eznewsletter/list_newsletterbounce', 'Remove and unsubscribe' ) ) );
            return;
        	
        }
    	else if ( $http->hasPostVariable( 'ConfirmRemoveAndUnsubscribeButton' ) )
        {
            $bounceArray = $http->sessionVariable( 'BounceIDArray' );
            $db->begin();
            if( count( $bounceArray ) > 0 )
            {
                foreach ( $bounceArray as $bounce )
                {
                    eZBounce::removeAllBounceInformation( $bounce->ID );
                    $sendItemBounced = eZSendNewsletterItem::fetch($bounce->ID);
	        		$subscription = eZSubscription::fetch( $sendItemBounced->attribute( 'subscription_id' ) );
	        		if ( $subscriptionObject )
	        		{
	        			$subscription->setAttribute( 'status', eZSubscription::StatusRemovedAdmin );
	        			$subscription->sync();
	        		}
                }
            }
            $db->commit();
        }
        else if ( $http->hasPostVariable( 'RemoveAllBounceEntryButton' ) )
        {         
            $Result = array();
            $Result['newsletter_menu'] = 'design:parts/content/bounce_menu.tpl';
            $Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
            $Result['content'] = $tpl->fetch( "design:$extension/confirmremoveall_bounce.tpl" );
            $Result['path'] = array( array( 'url' => false,
                                            'text' => ezpI18n::tr( 'eznewsletter/list_newsletterbounce', 'Newsletter types' ) ) );
            return;
        }
        else if ( $http->hasPostVariable( 'ConfirmRemoveAllBounceEntryButton' ) )
        {
            $removeAllBounce = 'TRUNCATE Table ez_bouncedata';      
            $db->query( $removeAllBounce );     
        }
        else if ( $http->hasPostVariable( 'CancelBounceSearchButton' ) )
        {
            $Module->redirectToView( 'bounce_search' );   
        }
        else if ( $http->hasPostVariable( 'ConfirmRemoveBounceSearchButton' ) )
        {
            $bounceEntryIDArray = $http->sessionVariable( 'BounceIDArray' );

            $db->begin();
            if( count( $bounceEntryIDArray ) > 0 )
            {
                foreach ( $bounceEntryIDArray as $bounceID )
                {
                    eZBounce::removeAllBounceInformation( $bounceID );
                }
            }
            $db->commit();
            /*
            $bounceDataArray = eZBounce::fetchByOffset( $offset, $limit, true, "address" );

            $tpl->setVariable( 'bounce_data_array', $bounceDataArray );

            $Result = array();
            $Result['newsletter_menu'] = 'design:parts/content/bounce_menu.tpl';
            $Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
            $Result['content'] = $tpl->fetch( "design:$extension/bounce_search.tpl" );
            $Result['path'] = array( array( 'url' => false,
                                        'text' => ezpI18n::tr( 'eznewsletter/bounce_search', 'Bounce search' ) ) );      
			*/
            $Module->redirectToView( 'bounce_search' );
        }
        else if ( $http->hasPostVariable( 'ConfirmRemoveBounceEntryButton' ) )
        {
            $bounceEntryIDArray = $http->sessionVariable( 'BounceIDArray' );

            $db->begin();
            if( count( $bounceEntryIDArray ) > 0 )
            {
                foreach ( $bounceEntryIDArray as $bounceID )
                {
                    eZBounce::removeAllBounceInformation( $bounceID );
                }
            }
            $db->commit();
        }

        $MailArray = eZBounce::fetchByOffset( $offset, $limit, false, "address" );
        
        if( count($MailArray) >= 1)
        {
        	$bounceDataArray = array();
        	foreach ($MailArray as $key => $MailBounceData)
        	{
        		$mail = $MailBounceData["address"];
        		$sb_c = $db->ArrayQuery( "SELECT count(*) as 'softbounces' FROM ez_bouncedata WHERE address='$mail' and bounce_type = 0" );
        		$hb_c = $db->ArrayQuery( "SELECT count(*) as 'hardbounces' FROM ez_bouncedata WHERE address='$mail' and bounce_type = 1" );
        		$bounceDataArray[$key]["mail"] = $mail;
        		$bounceDataArray[$key]["sb_count"] = $sb_c[0]["softbounces"];
        		$bounceDataArray[$key]["hb_count"] = $hb_c[0]["hardbounces"];
        		$bounceDataArray[$key]["bounces"] = eZBounce::fetchListByAddress($mail);
        		$bounceDataArray[$key]["bounceIDarray"] = array();
        		foreach ( $bounceDataArray[$key]["bounces"] as $key2 => $bounce)
        		{
        			$sendItemBounced = eZSendNewsletterItem::fetch($bounce->ID);
        			array_push($bounceDataArray[$key]["bounceIDarray"], $bounce->ID);
	        		$subscriptionObject = eZSubscription::fetch( $sendItemBounced->attribute( 'subscription_id' ) );
		            if ( $subscriptionObject )
		            {
		                $bounceDataArray[$key]["subscriptions"][$key2] = $subscriptionObject;
		            }
        		}
        	}
        	$tpl->setVariable( 'bounce_data_array', $bounceDataArray );
        }

        $tpl->setVariable( 'statusNames', eZSubscription::statusNameMap() );
        
        $Result = array();
        $Result['newsletter_menu'] = 'design:parts/content/bounce_menu.tpl';
        $Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
        $Result['content'] = $tpl->fetch( "design:$extension/list_newsletter_bounce.tpl" );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => ezpI18n::tr( 'eznewsletter/list_newsletterbounce', 'View newsletter bounces' ) ) );

    }break;
}

?>
