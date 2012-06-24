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

$warnings = array();

$subscriptionID = $Params['SubscriptionID'];

$subscription = eZSubscription::fetchDraft( $subscriptionID );

if ( !$subscription )
{
    $subscription = eZSubscription::create();
}

if ( !$subscription )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

if ( $http->hasPostVariable( 'StoreButton' ) ||
     $http->hasPostVariable( 'ProfileButton' ) ||
     $http->hasPostVariable( 'BrowseUserButton' ) ||
     $http->hasPostVariable( 'UnsetUserButton' ) )
{
    if ( !eZRobinsonListEntry::inList( $http->postVariable( 'Email' ) ) && !eZRobinsonListEntry::inList( $http->postVariable( 'Mobile' ) ) )
    {
        if ( !eZMail::validate( $http->postVariable( 'Email' ) ) )
        {
            $warnings[] = ezpI18n::tr( 'eznewsletter/newsletter/edit_subscription', 'You must provide a valid email address.' );
        }
        else
        {
            $subscription->setAttribute( 'email', $http->postVariable( 'Email' ) );
        }

        $subscription->setAttribute( 'firstname', $http->postVariable( 'Firstname' ) );
        $subscription->setAttribute( 'name', $http->postVariable( 'Name' ) );
        $subscription->setAttribute( 'mobile', $http->postVariable( 'Mobile' ) );
        $subscription->setAttribute( 'status', $http->postVariable( 'Status' ) );
        // $subscription->setAttribute( 'vip', $http->postVariable( 'Vip' ) );

        if ( $http->hasPostVariable( 'Password1' ) &&
             $http->hasPostVariable( 'Password2' ) )
        {
                if ( $http->postVariable( 'Password1' ) != 'password' &&
                     $http->postVariable( 'Password1' ) === $http->postVariable( 'Password2' ) &&
                     strlen( trim( $http->postVariable( 'Password1' ) ) ) > 0 )
                {
                    $subscription->setAttribute( 'password', $http->postVariable( 'Password1' ) );
                }
        }

        $subscription->store();

    }
    else
    {
        $warnings[] = ezpI18n::tr( 'eznewsletter/edit_subscription', 'Email address or mobile phone number is in opt-out list!' );
    }
}

if ( $http->hasPostVariable( 'StoreButton' ) )
{
    if ( 0 == count( $warnings ) )
    {
        $subscription->publish();
        $subscriptionList = $subscription->attribute( 'subscription_list' );
        return $Module->redirectToView( 'subscription_list', array( $subscriptionList->attribute( 'url_alias' ) ) );
    }

}
else if ( $http->hasPostVariable( 'CancelButton' ) )
{
    $subscription->removeDraft();
    $subscriptionList = $subscription->attribute( 'subscription_list' );
    return $Module->redirectToView( 'subscription_list', array( $subscriptionList->attribute( 'url_alias' ) ) );
}
else if ( $http->hasPostVariable( 'BrowseUserButton' ) )
{
    eZContentBrowse::browse( array( 'action_name' => 'SelectSingleUser',
                                    'keys' => array(),
                                    'description_template' => "design:eznewsletter/browse_single_user_select.tpl",
                                    'from_page' => 'newsletter/edit_subscription/' . $subscription->attribute( 'id' ) ),
                             $Module );
}
else if ( $http->hasPostVariable( 'SelectedObjectIDArray' ) &&
          !$http->hasPostVariable( 'BrowseCancelButton' ) )
{
    $userIDArray = $http->postVariable( 'SelectedObjectIDArray' );
    $subscription->setAttribute( 'user_id', $userIDArray[0] );
    $subscription->sync();
}
else if ( $http->hasPostVariable( 'UnsetUserButton' ) )
{
    $subscription->setAttribute( 'user_id', 0 );
    $subscription->store();
}

$tpl = eZNewsletterTemplateWrapper::templateInit();

$tpl->setVariable( 'warning', $warnings );
// $tpl->setVariable( 'vip_map', $subscription->vipNameMap() );
$tpl->setVariable( 'status_map', $subscription->statusNameMap() );
$tpl->setVariable( 'subscription', $subscription );

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/robinson_menu.tpl';
$Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:eznewsletter/edit_subscription.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'eznewsletter/edit_subscription', 'Edit subscription' ) ) );


?>
