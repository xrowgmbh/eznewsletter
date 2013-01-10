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

$Module =& $Params['Module'];

$http =& eZHTTPTool::instance();

$subscriptionList = eZSubscriptionList::fetch( $Params['SubscriptionListID'] );

$res =& eZTemplateDesignResource::instance();
$res->setKeys( array( array( 'newsletter_view', 'register_subscription' ) ) );

if ( !$subscriptionList )
{
    return $Module->handleError( 3, 'kernel' );
}
$http->setSessionVariable( "register_subscription", $subscriptionList->attribute( 'id' ) );
$user = false;
$currentUser = eZUser::currentUser();
if ( $currentUser->isLoggedIn() )
{
    $user = $currentUser;
}
if ( !$user )
{
    eZDebug::writeError('Missing parameter Object ID.', "Translation management" );
    return $Module->handleError( 1, 'kernel' );
}

$co = $currentUser->attribute( 'contentobject' );
$version = $co->attribute( 'current' );
$langs = $version->attribute( 'language_list' );

if ( $http->hasPostVariable( 'RedirectURIAfterPublish' ) )
{
    $http->setSessionVariable( 'RedirectURIAfterPublish', $http->postVariable( 'RedirectURIAfterPublish' ) );
}

return $Module->redirectTo( "/content/edit/" . eZUser::currentUserID() . "/a/" . $langs[0]->attribute( 'language_code' ) );


$Result = array();
$Result['content'] =& $tpl->fetch( "design:eznewsletter/unregister_subscription.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'ezxnewsletter', 'Register subscription' ) ) );

?>
