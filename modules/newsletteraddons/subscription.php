<?php
//
// Created on: <09-Dec-2005 13:08:26 hovik>
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

/*! \file register_subscription.php
*/

include_once( 'kernel/common/eztemplatedesignresource.php' );
include_once( 'kernel/common/template.php' );

include_once( eZExtension::baseDirectory() . '/eznewsletter/classes/ezsubscriptionlist.php' );
include_once( eZExtension::baseDirectory() . '/eznewsletter/classes/ezsubscription.php' );
include_once( eZExtension::baseDirectory() . '/eznewsletter/classes/eznewsletteraddons.php' );
$Module =& $Params['Module'];

$http =& eZHTTPTool::instance();
if ( $http->hasSessionVariable( 'LastPostVars' ) )
{    
    $_POST =  $http->sessionVariable( 'LastPostVars' );
    $http->removeSessionVariable( 'LastPostVars' );
}
$http->setSessionVariable( "register_subscription", array() );
$http->setSessionVariable( "unregister_subscription", array() );

if ( $Module->hasActionParameter( 'ChoosenSubscriptions' ) )
{

    foreach ( $Module->actionParameter( 'ChoosenSubscriptions' ) as $sub )
    {
        $subscriptionList = eZSubscriptionList::fetch( $sub );
        if ( !$subscriptionList )
        {
            eZDebug::writeError('ID "'.$sub.'" not found.', "" );
            return $Module->handleError( 3, 'kernel' );
        }
        $subscriptions[] = $subscriptionList->attribute( 'id' );
        if ( $Module->isCurrentAction( 'Add' ) )
        {
            $http->removeSessionVariable( "unregister_subscription" );
            $http->setSessionVariable( "register_subscription", $subscriptions );
        }
        if ( $Module->isCurrentAction( 'Remove' ) )
        {
            $http->removeSessionVariable( "register_subscription" );
            $http->setSessionVariable( "unregister_subscription", $subscriptions );
        }
    }
}

if ( $Module->isCurrentAction( 'Add' ) or $http->hasSessionVariable( "register_subscription" ) )
{
    $res =& eZTemplateDesignResource::instance();
    $res->setKeys( array( array( 'newsletter_view', 'register_subscription' ) ) );
    if ( $http->hasSessionVariable( "register_subscription" ) )
        $subscriptions = $http->sessionVariable( "register_subscription" );
    
    $user = false;
    $currentUser = eZUser::currentUser();
    if ( $currentUser->isLoggedIn() )
    {
        $user = $currentUser;
    }
    
    if ( !$user )
    {
        eZDebug::writeError( 'Login needed.', "" );
        if ( $Module->hasActionParameter( 'RedirectURI' ) )
        {
            $http->setSessionVariable( "LastAccessesURI", "newsletteraddons/subscription" );
            $http->setSessionVariable( "SubscriptionRedirectAfterUserRegister", $Module->actionParameter( 'RedirectURI' ) );
        }
        $http->setSessionVariable( '$_POST_BeforeLogin', $_POST );
        eZSessionWrite( eZHTTPTool::sessionID(), session_encode() );

        return $Module->handleError( 1, 'kernel' );
    }
    $co = $currentUser->attribute( 'contentobject' );
    $version = $co->attribute( 'current' );
    $langs = $version->attribute( 'language_list' );
    if ( $Module->hasActionParameter( 'RedirectURI' ) )
    {
        $http->setSessionVariable( "LastAccessesURI", $Module->actionParameter( 'RedirectURI' ) );
    }
    eZNewsletterAddons::removeDrafts( $user );
    return $Module->redirectTo( "/content/edit/" . eZUser::currentUserID() . "/a/" . $langs[0]->attribute( 'language_code' ) );
}
elseif ( $Module->isCurrentAction( 'Remove' )  or $http->hasSessionVariable( "unregister_subscription" ) )
{
    $res =& eZTemplateDesignResource::instance();
    $res->setKeys( array( array( 'newsletter_view', 'register_subscription' ) ) );
    if ( $http->hasSessionVariable( "unregister_subscription" ) )
        $subscriptions = $http->sessionVariable( "unregister_subscription" );
    $http->setSessionVariable( "unregister_subscription", $subscriptions );

    $user = false;
    $currentUser = eZUser::currentUser();
    if ( $currentUser->isLoggedIn() )
    {
        $user = $currentUser;
    }
    if ( !$user )
    {
        eZDebug::writeError( 'Login needed.', "" );
        $http->setSessionVariable( '$_POST_BeforeLogin', $_POST );
        if ( $Module->hasActionParameter( 'RedirectURI' ) )
        {
            $http->setSessionVariable( "LastAccessesURI", "newsletteraddons/subscription" );
            $http->setSessionVariable( "SubscriptionRedirectAfterUserRegister", $Module->actionParameter( 'RedirectURI' ) );
        }
        eZSessionWrite( eZHTTPTool::sessionID(), session_encode() );
        return $Module->handleError( 1, 'kernel' );
    }
    $co = $currentUser->attribute( 'contentobject' );
    $version = $co->attribute( 'current' );
    $langs = $version->attribute( 'language_list' );
    $warning = false;
    if ( $Module->hasActionParameter( 'RedirectURI' ) )
    {
        $http->setSessionVariable( "LastAccessesURI", $Module->actionParameter( 'RedirectURI' ) );
    }
    eZNewsletterAddons::removeDrafts( $user );
    return $Module->redirectTo( "/content/edit/" . eZUser::currentUserID() . "/a/" . $langs[0]->attribute( 'language_code' ) );
    /* old code for direct formular
    if ( $user )
    {
        foreach ( $subscriptions as $subscriptionListID )
        {
            $subscriptionList = eZSubscriptionList::fetch( $subscriptionListID, eZSubscriptionList_StatusPublished, true, true );
            $subscription = eZSubscription::fetchByUserSubscriptionListID( $user->attribute( 'contentobject_id' ), $subscriptionList->attribute( 'id' ) );
            $subscription->unsubscribe();
            $subscriptionsout[] = $subscriptionList;
        }
    }
    
    $tpl =& templateInit();
    if ( $user )
    {
        $tpl->setVariable( 'user', $user );
    }
    $tpl->setVariable( 'subscriptionLists', $subscriptionsout );

    if ( $http->hasSessionVariable( "LastAccessesURI" ) )
    {
       $tpl->setVariable( 'RedirectURI', $http->sessionVariable( "LastAccessesURI" ) );
    }
    $tpl->setVariable( 'warning', $warning );

    $Result = array();
    $Result['content'] =& $tpl->fetch( "design:eznewsletter/unregister_subscription.tpl" );
    $Result['path'] = array( array( 'url' => false,
    'text' => ezi18n( 'ezxnewsletter', 'Register subscription' ) ) );
    
    $http->removeSessionVariable( "unregister_subscription", $subscriptions );
    */
        
}
if ( $Module->isCurrentAction( 'Cancel' ) )
{
        $ini = eZINI::instance();
        return $Module->redirectTo( $ini->variable( 'SiteSettings', 'DefaultPage' ) );
}
else
{
    eZDebug::writeError('Missing action.' );
    return $Module->handleError( 1, 'kernel' );
}
?>
