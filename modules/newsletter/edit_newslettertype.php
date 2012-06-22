<?php
//
// Created on: <04-Dec-2005 14:35:12 oms>
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

/*! \file edit_newslettertype.php
*/

$extension = 'eznewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";

$Module = $Params['Module'];
$http = eZHTTPTool::instance();

$newsletterTypeID = $Params['NewsletterTypeID'];
$newsletterType = eZNewsletterType::fetchDraft( $newsletterTypeID );

if ( !$newsletterType )
{
    $newsletterType = eZNewsletterType::create();
    $Module->redirectToView( 'edit_type', array( $newsletterType->attribute( 'id' ) ) );
}

if ( !$newsletterType->siteaccessAllowed() )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}
$warning = array();

if ( $http->hasPostVariable( 'StoreButton' ) ||
     $http->hasPostVariable( 'BrowseArticlePool' ) || $http->hasPostVariable( 'DeleteArticlePool' ) ||
     $http->hasPostVariable( 'BrowseRelatedObject_1' ) || $http->hasPostVariable( 'DeleteRelatedObject_1' ) ||
     $http->hasPostVariable( 'BrowseRelatedObject_2' ) || $http->hasPostVariable( 'DeleteRelatedObject_2' ) ||
     $http->hasPostVariable( 'BrowseRelatedObject_3' ) || $http->hasPostVariable( 'DeleteRelatedObject_3' ) ||
     $http->hasPostVariable( 'BrowseInbox' ) || $http->hasPostVariable( 'DeleteInbox' ) )
{

    if( 0 < strlen( $http->postVariable( 'NewsletterTypeName' ) ) )
    {
        $newsletterType->setAttribute( 'name', $http->postVariable( 'NewsletterTypeName' ) );
    }
    else
    {
        $warning[] = ezpI18n::tr( 'eznewsletter/edit_newslettertype', 'You have not defined a name for this newslettertype' );
    }


    $modifierDays    = $http->hasPostVariable( 'SendModifierDays')    ? $http->postVariable( 'SendModifierDays' )    : 0;
    $modifierHours   = $http->hasPostVariable( 'SendModifierHours')   ? $http->postVariable( 'SendModifierHours' )   : 0;
    $modifierMinutes = $http->hasPostVariable( 'SendModifierMinutes') ? $http->postVariable( 'SendModifierMinutes' ) : 0;

    $SendDateModifier = ($modifierDays * 86400) + ($modifierHours * 3600 ) + ( $modifierMinutes * 60 );
    $newsletterType->setAttribute( 'send_date_modifier', $SendDateModifier);

    $preText  = $http->hasPostVariable( 'preText' )  ? $http->postVariable( 'preText' )  : '';
    $postText = $http->hasPostVariable( 'postText' ) ? $http->postVariable( 'postText' ) : '';

    $newsletterType->setAttribute( 'pretext', $preText );
    $newsletterType->setAttribute( 'posttext', $postText );

    if ( $http->postVariable( 'PersonaliseNewsletter' ) ) {
        $newsletterType->setAttribute( 'personalise', '1' );
    } else {
        $newsletterType->setAttribute( 'personalise', '0' );
    }

    $senderAddress = $http->postVariable( 'NewsletterTypeSenderAddress' );
    if ( eZMail::validate( $senderAddress ) )
    {
        $newsletterType->setAttribute( 'sender_address', $senderAddress );
    }
    else
    {
        $warning[] = ezpI18n::tr( 'eznewsletter/edit_newslettertype', 'Email address "%address" did not validate.', false, array( '%address' => $senderAddress ) );
    }

    if( $http->hasPostVariable( 'ValidContentClassIDArray' ) )
    {
        $newsletterType->setAttribute( 'contentclass_list', eZNewsletterType::serializeArray( $http->postVariable( 'ValidContentClassIDArray' ) ) );
    }

    if( $http->hasPostVariable( 'AllowedDesigns' ) &&
        0 < count( $http->postVariable( 'AllowedDesigns' ) ) )
    {
        $newsletterType->setAttribute( 'allowed_designs', eZNewsletterType::serializeArray( $http->postVariable( 'AllowedDesigns' ) ) );
    }
    else
    {
        $warning[] = ezpI18n::tr( 'eznewsletter/edit_newslettertype', 'You have to select at least one design.' );
    }

    if( $http->hasPostVariable( 'AllowedSiteaccesses' ) &&
        0 < count( $http->postVariable( 'AllowedSiteaccesses' ) ) )
    {
        $newsletterType->setAttribute( 'allowed_siteaccesses', eZNewsletterType::serializeArray( $http->postVariable( 'AllowedSiteaccesses' ) ) );
    }
    else
    {
        $warning[] = ezpI18n::tr( 'eznewsletter/edit_newslettertype', 'You have to select at least one allowed siteaccess.' );
    }

    if ( $http->hasPostVariable( 'SubscriptionListIDArray' ) &&
         0 < count( $http->postVariable( 'SubscriptionListIDArray' ) ) )
    {
        $newsletterType->removeSubscription();
        foreach( $http->postVariable( 'SubscriptionListIDArray' ) as $subscriptionListID )
        {
            $newsletterType->assignSubscription( $subscriptionListID );
            $newsletterType->store();
        }
    }
    else
    {
        $warning[] = ezpI18n::tr( 'eznewsletter/edit_newslettertype', 'You have to select at least one subscription list.' );
    }

    if ( !$newsletterType->attribute( 'article_pool_object_id' ) )
    {
        $warning[] = ezpI18n::tr( 'eznewsletter/edit_newslettertype', 'You have to select a valid newsletter placement.' );
    }

    $newsletterType->setAttribute( 'description', $http->postVariable( 'NewsletterTypeDescription' ) );
    $newsletterType->store();

}

if ( 0 === count( $warning ) &&
     $http->hasPostVariable( 'StoreButton' ) )
{
    $newsletterType->publish();
    return $Module->redirectTo( $Module->functionURI( 'view_type' ) . '/' . $newsletterTypeID );
}

if ( $http->hasPostVariable( 'CancelButton' )  )
{
    $newsletterType->removeDraft();
    $Module->redirectTo( $Module->functionURI( 'list_type' ) );
}

$relatedObjectMap = array();
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
                                               'from_page' => 'newsletter/edit_type/' . $newsletterTypeID . '/' . $selectName ),
                                        $Module );
    }

    if ( $http->hasPostVariable( $deleteName ) )
    {
        $newsletterType->setAttribute( $attributeName, 0 );
        $newsletterType->store();
    }

    if ( isset( $Params['BrowseSelected'] ) &&
         $Params['BrowseSelected'] == $selectName )
    {
        if ( $http->hasPostVariable( 'SelectedObjectIDArray' ) )
        {
            $relatedObjectID = $http->postVariable( 'SelectedObjectIDArray' );
            if ( isset( $relatedObjectID ) && !$http->hasPostVariable( 'BrowseCancelButton' ) )
            {
                $newsletterType->setAttribute( $attributeName, $relatedObjectID[0] );
                $newsletterType->store();
            }
        }
    }

}

if ( $http->hasPostVariable( 'BrowseArticlePool' ) )
{
    eZContentBrowse::browse( array( 'action_name' => 'ArticlePoolBrowse',
                                    'keys' => array(),
                                    'description_template' => "design:$extension/browse_article_pool.tpl",
                                    'from_page' => 'newsletter/edit_type/' . $newsletterTypeID . '/article' ),
                             $Module );
}
else if ( $http->hasPostVariable( 'BrowseInbox' ) )
{
    eZContentBrowse::browse( array( 'action_name' => 'BrowseInbox',
                                    'keys' => array(),
                                    'description_template' => "design:$extension/browse_article_pool.tpl",
                                    'from_page' => 'newsletter/edit_type/' . $newsletterTypeID . '/inbox' ),
                             $Module );
}

if ( $http->hasPostVariable( 'DeleteArticlePool' ) )
{
    $newsletterType->setAttribute( 'article_pool_object_id', 0 );
    $newsletterType->store( 'article_pool_object_id' );
}
else if ( $http->hasPostVariable( 'DeleteInbox' ) )
{
    $newsletterType->setAttribute( 'inbox_id', 0 );
    $newsletterType->store( 'inbox_id' );
}

if ( isset( $Params['BrowseSelected'] ) &&
     $Params['BrowseSelected'] == 'article' )
{
    if ( $http->hasPostVariable( 'SelectedObjectIDArray' ) )
    {
        $articlePoolNodeID = $http->postVariable( 'SelectedObjectIDArray' );
        if ( isset( $articlePoolNodeID ) && !$http->hasPostVariable( 'BrowseCancelButton' ) )
        {
            $newsletterType->setAttribute( 'article_pool_object_id', $articlePoolNodeID[0] );
            $newsletterType->store();
        }
    }
}
else if ( isset( $Params['BrowseSelected'] ) &&
          $Params['BrowseSelected'] == 'inbox' )
{
    if ( $http->hasPostVariable( 'SelectedObjectIDArray' ) )
    {
        $inboxNodeID = $http->postVariable( 'SelectedObjectIDArray' );
        if ( isset( $inboxNodeID ) && !$http->hasPostVariable( 'BrowseCancelButton' ) )
        {
            $newsletterType->setAttribute( 'inbox_id', $inboxNodeID[0] );
            $newsletterType->store();
        }
    }
}



$tpl = eZNewsletterTemplateWrapper::templateInit();
$tpl->setVariable( 'warning', $warning );
$tpl->setVariable( 'newsletter_type', $newsletterType );

$tpl->setVariable( 'contentclass_list', eZNewsletterType::unserializeArray( $newsletterType->attribute( 'contentclass_list' ) ) );
$tpl->setVariable( 'selected_designs', eZNewsletterType::unserializeArray( $newsletterType->attribute( 'allowed_designs' ) ) );
// countAll(eZSendNewsletterItem::definition()) ) ) erzeugt einen sql fehler
$tpl->setVariable( 'subscription_list_array', eZSubscriptionList::fetchList( 0, eZSubscriptionList::countAll( eZSubscriptionList::StatusPublished ) ) );


$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:$extension/edit_newslettertype.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'eznewsletter/edit_newslettertype', 'Edit newsletter type' ) ) );

?>
