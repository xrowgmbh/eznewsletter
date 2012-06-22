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

$Module = $Params['Module'];

$http = eZHTTPTool::instance();

$subscriptionList = eZSubscriptionList::fetch( $Params['SubscriptionListID'] );

$res = eZTemplateDesignResource::instance();
$res->setKeys( array( array( 'newsletter_view', 'register_subscription' ) ) );

if ( !$subscriptionList )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

if ( !$subscriptionList->siteaccessAllowed() )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$user = false;
$currentUser = eZUser::currentUser();
if ( $currentUser->isLoggedIn() )
{
    $user = $currentUser;
}

$warning = false;
$firstname = '';
$name = '';
$email = '';
if ( $http->hasPostVariable( 'StoreButton' ) )
{
    if ( $user )
    {

        $subscription = eZSubscription::fetchByUserSubscriptionListID( $user->attribute( 'contentobject_id' ), $subscriptionList->attribute( 'id' ) );
        if ( is_object( $subscription ) and $subscription->attribute( 'status' ) != eZSubscription::StatusRemovedSelf )
        {
            $warning = ezpI18n::tr( 'eznewsletter/register_subscription', 'You are already a registered subscriber' );
        }
        elseif ( is_object( $subscription ) and $subscription->attribute( 'status' ) == eZSubscription::StatusRemovedSelf )
        {
            $subscription->setAttribute( 'status', eZSubscription::StatusApproved );
            $subscription->store();
            $warning = ezpI18n::tr( 'eznewsletter/register_subscription', 'You have renewed your subscription.' );
        }
        else
        {
            $subscription = $subscriptionList->registerUser( $user->attribute( 'contentobject_id' ) );
        }
    }
    else
    {
        $password = false;
        if ( $subscriptionList->attribute( 'require_password' ) )
        {
            if ( $http->postVariable( 'Password1' ) != 'password' &&
                 $http->postVariable( 'Password1' ) === $http->postVariable( 'Password2' ) &&
                 strlen( trim( $http->postVariable( 'Password1' ) ) ) > 0 )
            {
                $password = $http->postVariable( 'Password1' );
            }
            else
            {
                $warning = ezpI18n::tr( 'eznewsletter/register_subscription', 'Passwords did not match.' );
            }
        }

        $firstname = $http->postVariable( 'Firstname' );
        $name = $http->postVariable( 'Name' );
        $email = $http->postVariable( 'Email' );
        $mobile = $http->postVariable( 'Mobile' );

        if ( !eZRobinsonListEntry::inList( $http->postVariable( 'Email' ) ) && !eZRobinsonListEntry::inList( $http->postVariable( 'Mobile' ) ) )
        {

            if ( !$firstname )
            {
                $warning = ezpI18n::tr( 'eznewsletter/register_subscription', 'You must enter a first name.' );
            }

            if ( !$name )
            {
                $warning = ezpI18n::tr( 'eznewsletter/register_subscription', 'You must enter a last name.' );
            }

            if ( !eZMail::validate( $email ) )
            {
                $warning = ezpI18n::tr( 'eznewsletter/register_subscription', 'You must provide a valid email address.' );
            }

            if ( !$warning )
            {
                $subscription = $subscriptionList->registerSubscription( $firstname, $name, $mobile, $email, $password );

                if ( !$subscription )
                {
                    $warning = ezpI18n::tr( 'eznewsletter/register_subscription', 'You\'re already a registered subscriber' );
                }
                else if ( $http->hasPostVariable( 'OutputFormat' ) )
                {
                    $subscription->setAttribute( 'output_format', implode( ',', $http->postVariable( 'OutputFormat' ) ) );
                    $subscription->sync();
                }
            }

        }
        else
        {
            $warning = ezpI18n::tr( 'eznewsletter/register_subscription', 'Email address or mobile phone number is in opt-out list. Subscribing is not possible.' );
        }

    }

    if ( !$warning )
    {
        $tpl = eZNewsletterTemplateWrapper::templateInit();
        $tpl->setVariable( 'subscriptionList', $subscriptionList );

        $subscription->sendConfirmation();

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:eznewsletter/register_subscription_info.tpl" );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => ezpI18n::tr( 'eznewsletter/register_subscription', 'Register subscription' ) ) );
        return;
    }
}
else if ( $http->hasPostVariable( 'CancelButton' ) )
{
    $ini = eZINI::instance();
    return $Module->redirectTo( $ini->variable( 'SiteSettings', 'DefaultPage' ) );
}

$tpl = eZNewsletterTemplateWrapper::templateInit();
if ( $user )
{
    $tpl->setVariable( 'user', $user );
}
$tpl->setVariable( 'firstname', $firstname );
$tpl->setVariable( 'name', $name );
$tpl->setVariable( 'email', $email );
$tpl->setVariable( 'subscriptionList', $subscriptionList );
$tpl->setVariable( 'output_map', eZSubscription::outputFormatNameMap() );

if (isset($warning))
{
    $tpl->setVariable( 'warning', $warning );
}

$Result = array();
$Result['content'] = $tpl->fetch( "design:eznewsletter/register_subscription.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'eznewsletter/register_subscription', 'Register subscription' ) ) );

?>
