<?php
//
// Definition of Module class
//
// Created on: <29-Nov-2005 09:27:17 oms>
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

/*! \file module.php
*/

$Module = array( 'name' => 'eZNewsletter' );

$ViewList = array();
$ViewList['list_type'] = array(
    'script' => 'list_newslettertype.php',
    'default_navigation_part' => 'eznewsletter',
    'functions' => array( 'view_admin' ),
    'unordered_params' => array( "offset" => "Offset",
                                 "offset2" => "Offset2",
                                 "limit" => "Limit",
                                 "limit2" => "Limit2" ),
    'params' => array() );

$ViewList['view_type'] = array(
    'script' => 'view_newslettertype.php',
    'functions' => array( 'view_admin' ),
    'default_navigation_part' => 'eznewsletter',
    'params' => array( 'NewsletterTypeID' ) );

$ViewList['edit_type'] = array(
    'script' => 'edit_newslettertype.php',
    'default_navigation_part' => 'eznewsletter',
    'functions' => array( 'create_newsletter_type' ),
    'params' => array( 'NewsletterTypeID', 'BrowseSelected' ) );

$ViewList['list_inprogress'] = array(
    'script' => 'list_inprogress.php',
    'functions' => array( 'view_admin' ),
    'default_navigation_part' => 'eznewsletter',
    'params' => array( 'NewsletterTypeID' ) );

$ViewList['list_archive'] = array(
    'script' => 'list_archive.php',
    'functions' => array( 'view_admin' ),
    'default_navigation_part' => 'eznewsletter',
    'params' => array( 'NewsletterTypeID' ) );

$ViewList['list_draft'] = array(
    'script' => 'list_draft.php',
    'functions' => array( 'view_admin' ),
    'default_navigation_part' => 'eznewsletter',
    'params' => array( 'NewsletterTypeID' ) );

$ViewList['list_recurring'] = array(
    'script' => 'list_recurring.php',
    'functions' => array( 'view_admin' ),
    'default_navigation_part' => 'eznewsletter',
    'params' => array( 'NewsletterTypeID' ) );

$ViewList['list_subscriptions'] = array(
    'script' => 'list_subscriptions.php',
    'functions' => array( 'view_admin' ),
    'default_navigation_part' => 'eznewsletter',
    'params' => array() );

$ViewList['edit_subscription_list'] = array(
    'script' => 'edit_subscription_list.php',
    'default_navigation_part' => 'eznewsletterList',
    'functions' => array( 'admin_subscription' ),
    'params' => array( 'SubscriptionListID', 'BrowseSelected' ) );

$ViewList['subscription_list'] = array(
    'script' => 'subscription_list.php',
    'default_navigation_part' => 'eznewsletter',
    'functions' => array( 'view_admin' ),
    'params' => array( 'SubscriptionListID' ) );

$ViewList['subscription_import'] = array(
    'script' => 'subscription_import.php',
    'default_navigation_part' => 'eznewsletter',
    'functions' => array( 'admin_subscription' ),
    'params' => array( 'SubscriptionListID' ) );

$ViewList['subscription_activate'] = array(
    'script' => 'subscription_activate.php',
    'default_navigation_part' => 'eznewsletter',
    'functions' => array( 'subscribe' ),
    'params' => array( 'Hash' ) );

$ViewList['subscription_search'] = array(
    'script' => 'subscription_search.php',
    'default_navigation_part' => 'eznewsletter',
    'functions' => array( 'view_admin' ),
    'params' => array() );

$ViewList['bounce_search'] = array(
    'script' => 'bounce_search.php',
    'default_navigation_part' => 'eznewsletter',
    'functions' => array( 'view_admin' ),
    'params' => array() );

$ViewList['user_settings'] = array(
    'script' => 'user_settings.php',
    'default_navigation_part' => 'eznewsletter',
    'functions' => array( 'subscribe' ),
    'params' => array( 'Hash' ) );

$ViewList['edit_subscription'] = array(
    'script' => 'edit_subscription.php',
    'default_navigation_part' => 'eznewsletter',
    'functions' => array( 'admin_subscription' ),
    'params' => array( 'SubscriptionID' ) );

$ViewList['modify_subscription'] = array(
    'script' => 'modify_subscription.php',
    'default_navigation_part' => 'eznewsletter',
    'functions' => array( 'admin_subscription' ),
    'params' => array( 'hash' ) );

$ViewList['register_subscription'] = array(
    'script' => 'register_subscription.php',
    'default_navigation_part' => 'eznewsletter',
    'functions' => array( 'subscribe' ),
    'params' => array( 'SubscriptionListID' ) );

$ViewList['list_bounce'] = array(
    'script' => 'list_newsletterbounce.php',
    'default_navigation_part' => 'eznewsletter',
    'functions' => array( 'bounce' ),
    'params' => array( 'Mode', 'BounceID' ) );

$ViewList['preview'] = array(
    'script' => 'preview.php',
    'default_navigation_part' => 'eznewsletter',
    'functions' => array( 'preview' ),
    'params' => array( 'ObjectID', 'ObjectVersion' ) );

$ViewList['previewfull'] = array(
    'script' => 'previewfull.php',
    'default_navigation_part' => 'eznewsletter',
    'functions' => array( 'preview' ),
    'params' => array( 'ObjectID', 'ObjectVersion' ) );

$ViewList['copy'] = array(
    'script' => 'copy.php',
    'default_navigation_part' => 'ezcontentnavigationpart',
    'functions' => array( 'create_newsletter' ),
    'params' => array( 'ObjectID' ) );

$ViewList['recurrence_status'] = array(
    'script' => 'recurrence_status.php',
    'functions' => array( 'view_admin' ),
    'default_navigation_part' => 'eznewsletter',
    'params' => array( 'NewsletterID', 'Action' ) );

$ViewList['robinson_show'] = array(
    'script' => 'robinson_show.php',
    'functions' => array( 'view_admin' ),
    'default_navigation_part' => 'eznewsletter',
    'params' => array() );

$ViewList['robinson_import'] = array(
    'script' => 'robinson_import.php',
    'functions' => array( 'admin_subscription' ),
    'default_navigation_part' => 'eznewsletter',
    'params' => array() );

$ViewList['robinson_edit'] = array(
    'script' => 'robinson_edit.php',
    'functions' => array( 'admin_subscription' ),
    'default_navigation_part' => 'eznewsletter',
    'params' => array( 'RobinsonEntryID' ) );

$ViewList['robinson_user'] = array(
    'script' => 'robinson_user.php',
    'functions' => array( 'robinson' ),
    'default_navigation_part' => 'eznewsletter',
    'params' => array() );

$ViewList['read'] = array(
    'script' => 'read.php',
    'functions' => array( 'read' ),
    'default_navigation_part' => 'eznewsletter',
    'params' => array(
        'sendItemHash',
        'objectID',
        'newsletterNodeID'
    )
);

$FunctionList = array();
$FunctionList['create_newsletter_type'] = array();
$FunctionList['create_newsletter'] = array();
$FunctionList['admin_subscription'] = array();
$FunctionList['read'] = array();
$FunctionList['view_admin'] = array();
$FunctionList['subscribe'] = array();
$FunctionList['bounce'] = array();
$FunctionList['preview'] = array();
$FunctionList['robinson'] = array();

?>
