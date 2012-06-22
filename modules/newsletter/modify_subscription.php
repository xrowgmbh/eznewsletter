<?php
//
// Created on: <24-Jan-2006 16:54:51 hovik>
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

/*! \file user_settings.php
*/

$Module = $Params['Module'];
$http = eZHTTPTool::instance();
$tpl = eZNewsletterTemplateWrapper::templateInit();

if ( $http->hasPostVariable( 'CreateSubscriptionButton' ) )
{
    $tpl->setVariable( 'newUser', true );
}

if ( $http->hasPostVariable( 'AddSubscription' ) )
{
    if( $http->hasVariable('original_email' ) )
    {
    $userData = eZUserSubscriptionData::fetch( $http->postVariable( 'original_email' ) );

        if ($userData)
        {
            if( $http->hasVariable('AddSubscriptionID' ) )
            {
                foreach ( $http->postVariable('AddSubscriptionID' ) as $subscriptionID )
                {

                    $subscriptionList = eZSubscriptionList::fetch( $subscriptionID, eZSubscriptionList::StatusPublished, true, true );
                    if ($subscriptionList)
                    {
                    $subscription = $subscriptionList->registerSubscription( 
                                            $userData->attribute( 'firstname' ), 
                                            $userData->attribute( 'name' ), 
                                            $userData->attribute( 'mobile' ), 
                                            $userData->attribute( 'email' ), 
                                            false );
                    $subscription->setAttribute( 'status', eZSubscription::StatusApproved );
                    $subscription->store();
                    }
                }
            }
        }
    }
}

if ( $http->hasPostVariable( 'UpdateSubscriptions' ) )
{
    if( ( $http->hasVariable('original_email' ) ) && ( $http->postVariable('original_email' ) != "" ) )
    {
        $userData = eZUserSubscriptionData::fetch( $http->postVariable( 'original_email' ) );

        $allowedStatusList = array( eZSubscription::StatusApproved,
                                    eZSubscription::StatusPending,
                                    eZSubscription::StatusConfirmed,
                                    eZSubscription::StatusRemovedSelf,
                                    eZSubscription::StatusRemovedAdmin );

        $subscriptionList = eZSubscription::fetchListByEmail( $userData->attribute( 'email' ),
                                                              eZSubscription::VersionStatusPublished,
                                                          array( array( eZSubscription::StatusPending,
                                                                        eZSubscription::StatusApproved,
                                                                        eZSubscription::StatusConfirmed,
                                                                        eZSubscription::StatusRemovedSelf,
                                                                        eZSubscription::StatusRemovedAdmin ) ) );

    foreach( $subscriptionList as $key => $subscription )
    {
        if ( $http->hasPostVariable( 'Status_' . $subscription->attribute( 'id' ) ) )
        {
            $newStatus = $http->postVariable( 'Status_' . $subscription->attribute( 'id' ) );
            if ( in_array( $subscription->attribute( 'status' ), $allowedStatusList ) &&
                 in_array( $newStatus, $allowedStatusList ) )
            {
                $subscription->setAttribute( 'status', $newStatus );
                $subscription->sync();
                $subscriptionList[$key] = $subscription;
            }
        }
    
   
    $subscription->store();
    }
    
    //get new data in memory
    $subscriptionList = eZSubscription::fetchListByEmail( $userData->attribute( 'email' ),
                                                      eZSubscription::VersionStatusPublished,
                                                          array( array( eZSubscription::StatusPending,
                                                                        eZSubscription::StatusApproved,
                                                                        eZSubscription::StatusConfirmed,
                                                                        eZSubscription::StatusRemovedSelf,
                                        eZSubscription::StatusRemovedAdmin ) ) );    

        $userData->setAttribute( 'name', $http->postVariable( 'Name' ) );
        $userData->setAttribute( 'firstname', $http->postVariable( 'FirstName' ) );
        $userData->setAttribute( 'mobile', $http->postVariable( 'Mobile' ) );

        if ( $userData->attribute( 'email' ) != $http->postVariable( 'Email' ) )
        {
            $emailExists = eZUserSubscriptionData::fetch( $http->postVariable( 'Email' ) );
            if ( $emailExists )
            {
                $warning = ezpI18n::tr( 'eznewsletter/modify_subscription', 'The given email address is already in use. Please use another.' );
            }
            else
            {
                $userData->setAttribute( 'email', $http->postVariable( 'Email' ) );
            }
        }

        if ( $http->hasPostVariable( 'Password1' ) &&
             $http->postVariable( 'Password1' ) != 'password' )
        {
            if ( $http->postVariable( 'Password1' ) === $http->postVariable( 'Password2' ) &&
                 strlen( trim( $http->postVariable( 'Password1' ) ) ) > 0 )
            {
                $userData->setVariable( 'password', md5( $http->postVariable( 'Password1' ) ) );
            }
            else
            {
                $warning = ezpI18n::tr( 'eznewsletter/modify_subscription', 'Password did not match' );
            }
        }

        $userData->sync();
    //get new Data inmemory
    $userData = eZUserSubscriptionData::fetch( $http->postVariable( 'original_email' ) );

    
    
    }
    else
    {
    //create new
    $userData = eZUserSubscriptionData::create(
                $http->postVariable( 'FirstName' ),
            $http->postVariable( 'Name' ),
            $http->postVariable( 'Mobile' ),
            $http->postVariable( 'Email' ) );
    //redirect to self
    if ($userData)
    {
        return $Module->redirectTo( '/newsletter/modify_subscription/'.$userData->attribute( 'hash'  ) );
    }
    }
}

if( $Params['hash'] != "" ) {

    $userData = eZUserSubscriptionData::fetchByHash( $Params['hash'] );

    if ( !$userData )
        {
            $tpl = eZNewsletterTemplateWrapper::templateInit();

            $Result = array();
        $Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
            $Result['content'] = $tpl->fetch( "design:ezsubscribe/no_subscription.tpl" );
            $Result['path'] = array( array( 'url' => false,
                                            'text' => ezpI18n::tr( 'eznewsletter/modify_subscription', 'No subscription' ) ) );
            return $Result;
        }
    else
    {

        $subscriptionList = eZSubscription::fetchListByEmail( $userData->attribute( 'email' ),
                                                              eZSubscription::VersionStatusPublished,
                                                          array( array( eZSubscription::StatusPending,
                                                                        eZSubscription::StatusApproved,
                                                                        eZSubscription::StatusConfirmed,
                                                                        eZSubscription::StatusRemovedSelf,
                                        eZSubscription::StatusRemovedAdmin ) ) );

        $allowedStatusList = array( eZSubscription::StatusApproved,
                        eZSubscription::StatusPending,
                    eZSubscription::StatusConfirmed,
                                eZSubscription::StatusRemovedSelf,
                    eZSubscription::StatusRemovedAdmin );
    }
}

//get new data

//all subscriptions
$found=false;
$additionalLists = array();
$allSubscriptionList = eZSubscriptionList::fetchList( 0, 100);

foreach($allSubscriptionList as $addList)
{
    foreach($subscriptionList as $userList)
    {
    if($addList->attribute('id') === $userList->attribute('subscriptionlist_id') )
    {
        $found=true;
    }
    }
    
    if (!$found)
    {
    $additionalLists[] = $addList;
    }
    $found=false;
}

//$tpl = templateInit();
//$tpl->setVariable( 'user', $user );
if ( isset($warning) ) {
    $tpl->setVariable( 'warning', $warning );
}
$tpl->setVariable( 'hash', $Params['hash'] );
$tpl->setVariable( 'userData', $userData );
$tpl->setVariable( 'allowedStatusList', $allowedStatusList );
$tpl->setVariable( 'statusNameMap', eZSubscription::statusNameMap() );
$tpl->setVariable( 'subscriptionList', $subscriptionList );
$tpl->setVariable( 'additionalLists', $additionalLists );

$Result = array();
$Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:eznewsletter/user_modify.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'eznewsletter/modify_subscription', 'Activate subscription' ) ) );

?>
