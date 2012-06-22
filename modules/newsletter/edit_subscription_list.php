<?php
//
// Created on: <07-Dec-2005 11:42:10 hovik>
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

/*! \file edit_subscription_list.php
*/

$Module = $Params['Module'];
$http = eZHTTPTool::instance();

$subscriptionListID = $Params['SubscriptionListID'];

$subscriptionList = eZSubscriptionList::fetchDraft( $subscriptionListID );

if ( !$subscriptionList )
{
    $subscriptionList = eZSubscriptionList::create();
}

if ( !$subscriptionList->siteaccessAllowed() )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$warning = array();

if ( $http->hasPostVariable( 'StoreButton' ) ||
     $http->hasPostVariable( 'GenerateURLHash' ) ||
     $http->hasPostVariable( 'BrowseRelatedObject_1' ) || $http->hasPostVariable( 'DeleteRelatedObject_1' ) ||
     $http->hasPostVariable( 'BrowseRelatedObject_2' ) || $http->hasPostVariable( 'DeleteRelatedObject_2' ) ||
     $http->hasPostVariable( 'BrowseRelatedObject_3' ) || $http->hasPostVariable( 'DeleteRelatedObject_3' ) )
{
    $subscriptionList->setAttribute( 'name', $http->postVariable( 'Name' ) );
    $subscriptionList->setAttribute( 'description', $http->postVariable( 'Description' ) );
    $subscriptionList->setAttribute( 'login_steps', $http->postVariable( 'LoginSteps' ) );
    $subscriptionList->setAttribute( 'require_password', $http->hasPostVariable( 'RequirePassword' ) ? 1 : 0 );
    $subscriptionList->setAttribute( 'allow_anonymous', $http->hasPostVariable( 'AllowAnonymous' ) ? 1 : 0 );
    $subscriptionList->setAttribute( 'auto_confirm_registered', $http->hasPostVariable( 'AutoConfirmRegistered' ) ? 1 : 0 );
    $subscriptionList->setAttribute( 'auto_approve_registered', $http->hasPostVariable( 'AutoApproveRegistered' ) ? 1 : 0 );
    $subscriptionList->setAttribute( 'url_type', $http->postVariable( 'RegistrationURL' ) );
    $subscriptionList->setAttribute( 'url', $http->postVariable( 'URLName' ) );


    if( $http->hasPostVariable( 'AllowedSiteaccesses' ) &&
	0 < count( $http->postVariable( 'AllowedSiteaccesses' ) ) )
    {
	$subscriptionList->setAttribute( 'allowed_siteaccesses', eZNewsletterType::serializeArray( $http->postVariable( 'AllowedSiteaccesses' ) ) );
    }
    else
    {
	$warning[] = ezpI18n::tr( 'eznewsletter/edit_subscription_list', 'You have to select at least one allowed siteaccess.' );
    }

    if ( $http->hasPostVariable( 'GenerateURLHash' ) )
    {
        $subscriptionList->setAttribute( 'url', md5( mt_rand() ) );
    }

    $subscriptionList->store();
}

if ( 0 === count( $warning ) &&
     $http->hasPostVariable( 'StoreButton' ) )
{
    $subscriptionList->publish();
    return $Module->redirectToView( 'subscription_list', array( $subscriptionList->attribute( 'url_alias' ) ) );
}
if ( $http->hasPostVariable( 'CancelButton' ) )
{
    $subscriptionList->removeDraft();

    return $Module->redirectToView( 'list_subscriptions' );
}

$relatedObjectMap = array();
$extension = 'eznewsletter';
for ( $count = 1; $count <= 3; ++$count )
{
    $postName = 'BrowseRelatedObject_' . $count;
    $attributeName = 'related_object_id_' . $count;
    $selectName = 'related' . $count;
    $deleteName = 'DeleteRelatedObject_' . $count;

    if ( $http->hasPostVariable( $postName ) )
    {
        return eZContentBrowse::browse( array( 'action_name' => 'ArticlePoolBrowse',
                                               'keys' => array(),
                                               'description_template' => "design:$extension/browse_article_pool.tpl",
                                               'from_page' => 'newsletter/edit_subscription_list/' . $subscriptionListID . '/' . $selectName ),
                                        $Module );
    }

    if ( $http->hasPostVariable( $deleteName ) )
    {
        $subscriptionList->setAttribute( $attributeName, 0 );
        $subscriptionList->store();
    }

    if ( isset( $Params['BrowseSelected'] ) &&
         $Params['BrowseSelected'] == $selectName )
    {
        if ( $http->hasPostVariable( 'SelectedObjectIDArray' ) )
        {
            $relatedObjectID = $http->postVariable( 'SelectedObjectIDArray' );
            if ( isset( $relatedObjectID ) && !$http->hasPostVariable( 'BrowseCancelButton' ) )
            {
                $subscriptionList->setAttribute( $attributeName, $relatedObjectID[0] );
                $subscriptionList->store();
            }
        }
    }
}

$tpl = eZNewsletterTemplateWrapper::templateInit();
$tpl->setVariable( 'warning', $warning );
$tpl->setVariable( 'subscriptionList', $subscriptionList );
$tpl->setVariable( 'loginSteps_map', eZSubscriptionList::loginStepsNameMap() );

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/robinson_menu.tpl';
$Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:eznewsletter/edit_subscription_list.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'eznewsletter/edit_subscription_list', 'Subscription lists' ) ) );


?>

