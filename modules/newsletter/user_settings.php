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

if ( $http->hasPostVariable( 'UpdateSubscriptions' ) )
{
    if( $http->hasVariable('original_email' ) )
    {
        $userData = eZUserSubscriptionData::fetch( $http->postVariable( 'original_email' ) );

        $allowedStatusList = array( eZSubscription::StatusApproved,
                                    eZSubscription::StatusPending,
                                    eZSubscription::StatusConfirmed,
                                    eZSubscription::StatusRemovedSelf );

        $subscriptionList = eZSubscription::fetchListByEmail( $userData->attribute( 'email' ),
                                                              eZSubscription::VersionStatusPublished,
                                                              array( array( eZSubscription::StatusPending,
                                                                            eZSubscription::StatusApproved,
                                                                            eZSubscription::StatusConfirmed,
                                                                            eZSubscription::StatusRemovedSelf ) ) );
        foreach( $subscriptionList as $key => $subscription )
        {
            //unsubscribe
            if ( !$http->hasPostVariable( 'Status_' . $subscription->attribute( 'id' ) ) )
            {
                if ( $subscription->attribute('status') == eZSubscription::StatusApproved ||
                     $subscription->attribute( 'status') == eZSubscription::StatusConfirmed )
                {
                    $subscription->setAttribute( 'status', eZSubscription::StatusRemovedSelf );
                    $subscription->sync();
                    $subscriptionList[$key] = $subscription;
                }
            }
            else
            {
                if ( $subscription->attribute('status') == eZSubscription::StatusRemovedSelf )
                {
                    //subscribe
                    $subscription->setAttribute( 'status', eZSubscription::StatusConfirmed );
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

        if ( !isset($user) )
        {
            $userData->setAttribute( 'name', $http->postVariable( 'Name' ) );
            $userData->setAttribute( 'firstname', $http->postVariable( 'FirstName' ) );
            $userData->setAttribute( 'mobile', $http->postVariable( 'Mobile' ) );

            if ( $userData->attribute( 'email' ) != $http->postVariable( 'Email' ) )
            {
                $emailExists = eZUserSubscriptionData::fetch( $http->postVariable( 'Email' ) );
                if ( $emailExists )
                {
                    $warning = ezpI18n::tr( 'eznewsletter/user_settings', 'The given email address is already in use. Please use another.' );
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
                    $userData->setAttribute( 'password', md5( $http->postVariable( 'Password1' ) ) );
                }
                else
                {
                    $warning = ezpI18n::tr( 'eznewsletter/user_settings', 'Password did not match' );
                }
            }

            $userData->sync();
            //get new Data inmemory
            $userData = eZUserSubscriptionData::fetch( $http->postVariable( 'original_email' ) );
        }  
    }
}

if( $Params['Hash'] )
{
    $userData = eZUserSubscriptionData::fetchByHash( $Params['Hash'] );
        
    if ( !$userData )
        {
            $tpl = eZNewsletterTemplateWrapper::templateInit();

            $Result = array();
            $Result['content'] = $tpl->fetch( "design:eznewsletter/no_subscription.tpl" );
            $Result['path'] = array( array( 'url' => false,
                                            'text' => ezpI18n::tr( 'eznewsletter', 'No subscription.' ) ) );
            return $Result;
        }
    else
    {
        //$warning = false;
        $passwordSet = $userData->attribute( 'password' ) != '';
        if ( $passwordSet && !$http->hasSessionVariable( 'Newsletter_UserSettings_' . $userData->attribute( 'id' ) ) )
        {
                $checkPassword = true;
            if ( $http->hasPostVariable( 'Password' ) )
            {
                if ( md5( $http->postVariable( 'Password' ) ) == $userData->attribute( 'password' ) )
                {
                        $http->setSessionVariable( 'Newsletter_UserSettings_' . $userData->attribute( 'id' ), 1 );
                        $checkPassword = false;
                }
            }

            if ( $checkPassword )
            {
                $tpl = eZNewsletterTemplateWrapper::templateInit();
                $tpl->setVariable( 'hash', $userData->attribute( 'hash' ) );

                $Result = array();
                $Result['content'] = $tpl->fetch( "design:eznewsletter/user_settings_password.tpl" );
                $Result['path'] = array( array( 'url'  => false,
                                                'text' => ezpI18n::tr( 'eznewsletter/user_settings_password.tpl', 'Activate subscription' ) ) );
                return $Result;
            }
        }
        $subscriptionList = eZSubscription::fetchListByEmail( $userData->attribute( 'email' ),
                                                              eZSubscription::VersionStatusPublished,
                                                              array( array( eZSubscription::StatusPending,
                                                                            eZSubscription::StatusApproved,
                                                                            eZSubscription::StatusConfirmed,
                                                                            eZSubscription::StatusRemovedSelf,
                                                                            eZSubscription::StatusRemovedAdmin ) ) );

        $allowedStatusList = array( eZSubscription::StatusApproved,
                                    eZSubscription::StatusConfirmed,
                                    eZSubscription::StatusRemovedSelf );

        $removedStatusList = array( eZSubscription::StatusRemovedAdmin,
                                    eZSubscription::StatusRemovedSelf );

        //check if all subscriptions are not pending
        $pending=true;
        foreach($subscriptionList as $subscription)
        {
        if ($subscription->attribute( 'status' ) != eZSubscription::StatusPending )
        {
            $pending=false;
        }
        }
        if ($pending)
        {
            $tpl = eZNewsletterTemplateWrapper::templateInit();
            $tpl->setVariable( 'hash', $userData->attribute( 'hash' ) );

            $Result = array();
            $Result['content'] = $tpl->fetch( "design:eznewsletter/pending.tpl" );
            $Result['path'] = array( array( 'url' => false,
                                            'text' => ezpI18n::tr( 'eznewsletter', 'Activate Subscription' ) ) );
            return $Result;

        }
    }
}
else
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

//get new data

$tpl = eZNewsletterTemplateWrapper::templateInit();
if ( isset($warning) )
{
    $tpl->setVariable( 'warning', $warning );
}
$tpl->setVariable( 'hash', $Params['Hash'] );
$tpl->setVariable( 'userData', $userData );
$tpl->setVariable( 'allowedStatusList', $allowedStatusList );
$tpl->setVariable( 'removedStatusList', $removedStatusList );
$tpl->setVariable( 'statusNameMap', eZSubscription::statusNameMap() );
$tpl->setVariable( 'subscriptionList', $subscriptionList );
//get profile data

$Result = array();
$Result['content'] = $tpl->fetch( "design:eznewsletter/user_settings_user.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'eznewsletter', 'Activate Subscription' ) ) );

?>
