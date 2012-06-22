<?php
//
// Created on: <22-Dec-2005 16:00:04 hovik>
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

/*! \file subscription_activate.php
*/

$Module = $Params['Module'];

$http = eZHTTPTool::instance();

$subscription = eZSubscription::fetchByHash( $Params['Hash'] );

if ( !$subscription )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

$subscriptionList = $subscription->attribute( 'subscription_list' );

$res = eZTemplateDesignResource::instance();
$res->setKeys( array( array( 'newsletter_view', 'subscription_activate' ) ) );

$subscription->setAttribute( 'status', eZSubscription::StatusConfirmed );
if ( $subscriptionList->attribute( 'auto_approve_registered' ) )
{
    $subscription->setAttribute( 'status', eZSubscription::StatusApproved );
}
$subscription->store();

$tpl = eZNewsletterTemplateWrapper::templateInit();
$tpl->setVariable( 'subscription', $subscription );

$Result = array();
$Result['content'] = $tpl->fetch( "design:eznewsletter/subscription_activate.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'eznewsletter/subscription_activate', 'Activate subscription' ) ) );


?>
