<?php
//
// Created on: <05-Dec-2005 15:11:38 oms>
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

/*! \file function_definition.php
*/

$extension = 'eznewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/modules/newsletter/";


$FunctionList = array();

$FunctionList['version'] = array( 'name' => 'version',
                                  'operation_types' => array( 'read' ), 
                                  'call_method' => array( 'include_file' => $baseDir . 'eznewsletterfunctioncollection.php',
                                                          'class' => 'eZNewsletterFunctionCollection',
                                                          'method' => 'fetchFullVersionString' ), 
                                  'parameter_type' => 'standard',
                                  'parameters' => array( ) ); 

$FunctionList['newsletter_type_count'] = array( 'name' => 'newsletter_type_count',
                                                'call_method' => array( 'include_file' => $baseDir . 'eznewsletterfunctioncollection.php',
                                                                        'class' => 'eZNewsletterFunctionCollection',
                                                                        'method' => 'fetchNewsletterTypeCount' ),
                                                'parameter_type' => 'standard',
                                                'parameters' => array( array( 'name' => 'use_filter',
                                                                              'type' => 'boolean',
                                                                              'required' => false,
                                                                              'default' => false ) ) );

$FunctionList['newsletter_type_list'] = array( 'name' => 'newsletter_type_list',
                                                'call_method' => array( 'include_file' => $baseDir . 'eznewsletterfunctioncollection.php',
                                                                        'class' => 'eZNewsletterFunctionCollection',
                                                                        'method' => 'fetchNewsletterTypeList' ),
                                                'parameter_type' => 'standard',
                                                'parameters' => array( array( 'name' => 'use_filter',
                                                                              'type' => 'boolean',
                                                                              'required' => false,
                                                                              'default' => false ) ) );

$FunctionList['subscription_list_count'] = array( 'name' => 'subscription_list_count',
                                                  'call_method' => array( 'include_file' => $baseDir . 'eznewsletterfunctioncollection.php',
                                                                          'class' => 'eZNewsletterFunctionCollection',
                                                                          'method' => 'fetchSubscriptionListCount' ),
                                                  'parameter_type' => 'standard',
                                                  'parameters' => array( array( 'name' => 'use_filter',
                                                                                'type' => 'boolean',
                                                                                'required' => false,
                                                                                'default' => false ) ) );

$FunctionList['list_subscriptions'] = array( 'name' => 'list_subscriptions',
                                             'call_method' => array( 'include_file' => $baseDir . 'eznewsletterfunctioncollection.php',
                                                                     'class' => 'eZNewsletterFunctionCollection',
                                                                     'method' => 'listSubscriptions' ),
                                             'parameter_type' => 'standard',
                                             'parameters' => array( array( 'name' => 'offset',
                                                                           'type' => 'integer',
                                                                           'required' => false,
                                                                           'default' => 0 ),
                                                                    array( 'name' => 'count',
                                                                           'type' => 'integer',
                                                                           'required' => false,
                                                                           'default' => 100 ),
                                                                    array( 'name' => 'use_filter',
                                                                           'type' => 'boolean',
                                                                           'required' => false,
                                                                           'default' => false ) ) );

$FunctionList['subscription_array_by_user_id'] = array( 'name' => 'subscription_array_by_user_id',
                                                        'call_method' => array( 'include_file' => $baseDir . 'eznewsletterfunctioncollection.php',
                                                                                'class' => 'eZNewsletterFunctionCollection',
                                                                                'method' => 'subscriptionArrayByUserID' ),
                                                        'parameter_type' => 'standard',
                                                        'parameters' => array( array( 'name' => 'user_id',
                                                                                      'type' => 'string',
                                                                                      'required' => true ) ) );

$FunctionList['subscription_array_by_email'] = array( 'name' => 'subscription_array_by_email',
                                                      'call_method' => array( 'include_file' => $baseDir . 'eznewsletterfunctioncollection.php',
                                                                              'class' => 'eZNewsletterFunctionCollection',
                                                                              'method' => 'subscriptionArrayByEmail' ),
                                                      'parameter_type' => 'standard',
                                                      'parameters' => array( array( 'name' => 'email',
                                                                                    'type' => 'string',
                                                                                    'required' => true ) ) );

$FunctionList['subscription_by_id'] = array( 'name' => 'subscription_by_id',
                                                      'call_method' => array( 'include_file' => $baseDir . 'eznewsletterfunctioncollection.php',
                                                                              'class' => 'eZNewsletterFunctionCollection',
                                                                              'method' => 'subscriptionByID' ),
                                                      'parameter_type' => 'standard',
                                                      'parameters' => array( array( 'name' => 'id',
                                                                                    'type' => 'integer',
                                                                                    'required' => true ) ) );

$FunctionList['active_subscriptions_by_user_id'] = array( 'name' => 'active_subscriptions_by_user_id',
                                                        'call_method' => array( 'include_file' => $baseDir . 'eznewsletterfunctioncollection.php',
                                                                                'class' => 'eZNewsletterFunctionCollection',
                                                                                'method' => 'activeSubscriptionsByUserID' ),
                                                        'parameter_type' => 'standard',
                                                        'parameters' => array( array( 'name' => 'user_id',
                                                                                      'type' => 'string',
                                                                                      'required' => true ) ) );

$FunctionList['active_subscriptions_by_email'] = array( 'name' => 'active_subscriptions_by_email',
                                                      'call_method' => array( 'include_file' => $baseDir . 'eznewsletterfunctioncollection.php',
                                                                              'class' => 'eZNewsletterFunctionCollection',
                                                                              'method' => 'activeSubscriptionsByEmail' ),
                                                      'parameter_type' => 'standard',
                                                      'parameters' => array( array( 'name' => 'email',
                                                                                    'type' => 'string',
                                                                                    'required' => true ) ) );

$FunctionList['user_data'] = array( 'name' => 'user_data',
                                    'call_method' => array( 'include_file' => $baseDir . 'eznewsletterfunctioncollection.php',
                                                            'class' => 'eZNewsletterFunctionCollection',
                                                            'method' => 'userDataByHash' ),
                                    'parameter_type' => 'standard',
                                    'parameters' => array( array( 'name' => 'hash',
                                                                  'type' => 'string',
                                                                  'required' => true ) ) );

$FunctionList['newsletter_count'] = array( 'name' => 'newsletter_count',
                                           'call_method' => array( 'include_file' => $baseDir . 'eznewsletterfunctioncollection.php',
                                                                   'class' => 'eZNewsletterFunctionCollection',
                                                                   'method' => 'fetchNewsletterCount' ),
                                           'parameter_type' => 'standard',
                                           'parameters' => array( array( 'name' => 'type_id',
                                                                         'type' => 'integer',
                                                                         'required' => false,
                                                                         'default' => false ) ) );

$FunctionList['newsletter'] = array( 'name' => 'newsletter',
                                     'call_method' => array( 'include_file' => $baseDir . 'eznewsletterfunctioncollection.php',
                                                             'class' => 'eZNewsletterFunctionCollection',
                                                             'method' => 'fetchNewsletter' ),
                                     'parameter_type' => 'standard',
                                     'parameters' => array( array( 'name' => 'id',
                                                                   'type' => 'integer',
                                                                   'required' => true ) ) );

$FunctionList['newsletter_by_object'] = array( 'name' => 'newsletter_by_object',
                                               'call_method' => array( 'include_file' => $baseDir . 'eznewsletterfunctioncollection.php',
                                                             'class' => 'eZNewsletterFunctionCollection',
                                                             'method' => 'fetchNewsletterByObject' ),
                                               'parameter_type' => 'standard',
                                               'parameters' => array( array( 'name' => 'contentobject_id',
                                                                             'type' => 'integer',
                                                                             'required' => true ),
                                                                      array( 'name' => 'contentobject_version',
                                                                             'type' => 'integer',
                                                                             'required' => true ),
                                                                         array( 'name' => 'published',
                                                                                'type' => 'boolean',
                                                                                'required' => false,
                                                                                'default' => null ) ) );

$FunctionList['newsletter_by_hash'] = array( 'name' => 'newsletter_by_object',
                                             'call_method' => array( 'include_file' => $baseDir . 'eznewsletterfunctioncollection.php',
                                                                     'class' => 'eZNewsletterFunctionCollection',
                                                                     'method' => 'fetchNewsletterByHash' ),
                                             'parameter_type' => 'standard',
                                             'parameters' => array( array( 'name' => 'hash',
                                                                           'type' => 'string',
                                                                           'required' => true ) ) );

$FunctionList['newsletter_list_by_type'] = array( 'name' => 'newsletter_list_by_type',
                                                  'call_method' => array( 'include_file' => $baseDir . 'eznewsletterfunctioncollection.php',
                                                                          'class' => 'eZNewsletterFunctionCollection',
                                                                          'method' => 'fetchNewsletterListByType' ),
                                                  'parameter_type' => 'standard',
                                                  'parameters' => array( array( 'name' => 'type_id',
                                                                                'type' => 'integer',
                                                                                'required' => true ),
                                                                         array( 'name' => 'offset',
                                                                                'type' => 'integer',
                                                                                'required' => false,
                                                                                'default' => 0 ),
                                                                         array( 'name' => 'limit',
                                                                                'type' => 'integer',
                                                                                'required' => false,
                                                                                'default' => 10 ),
                                                                         array( 'name' => 'is_sent',
                                                                                'type' => 'boolean',
                                                                                'required' => false,
                                                                                'default' => null ),
                                                                         array( 'name' => 'is_draft',
                                                                                'type' => 'boolean',
                                                                                'required' => false,
                                                                                'default' => null ),
                                                                         array( 'name' => 'grouping',
                                                                                'type' => 'boolean',
                                                                                'required' => false,
                                                                                'default' => null ),
                                                                         array( 'name' => 'recurring',
                                                                                'type' => 'boolean',
                                                                                'required' => false,
                                                                                'default' => null ) ) );

$FunctionList['newsletter_read_stat'] = array( 'name' => 'newsletter_read_stat',
                                               'call_method' => array( 'include_file' => $baseDir . 'eznewsletterfunctioncollection.php',
                                                                       'class' => 'eZNewsletterFunctionCollection',
                                                                       'method' => 'fetchNewsletterReadStat' ),
                                               'parameter_type' => 'standard',
                                               'parameters' => array( array( 'name' => 'newsletter_id',
                                                                             'type' => 'integer',
                                                                             'required' => true ) ) );

$FunctionList['bounce_count'] = array( 'name' => 'bounce_count',
                                       'call_method' => array( 'include_file' => $baseDir . 'eznewsletterfunctioncollection.php',
                                                               'class' => 'eZNewsletterFunctionCollection',
                                                               'method' => 'fetchNewsletterBounceCount' ),
                                       'parameter_type' => 'standard',
                                       'parameters' => array() );

$FunctionList['grouped_bounce_count'] = array( 'name' => 'grouped_bounce_count',
                                       'call_method' => array( 'include_file' => $baseDir . 'eznewsletterfunctioncollection.php',
                                                               'class' => 'eZNewsletterFunctionCollection',
                                                               'method' => 'fetchNewsletterBounceCountGroupedByAddress' ),
                                       'parameter_type' => 'standard',
                                       'parameters' => array() );

$FunctionList['object_stat'] = array( 'name' => 'newsletter_read_stat',
                                           'call_method' => array( 'include_file' => $baseDir . 'eznewsletterfunctioncollection.php',
                                                                   'class' => 'eZNewsletterFunctionCollection',
                                                                   'method' => 'fetchObjectStat' ),
                                           'parameter_type' => 'standard',
                                           'parameters' => array( array( 'name' => 'contentobject_id',
                                                                         'type' => 'integer',
                                                                         'required' => true ) ) );

$FunctionList['onhold_count'] = array( 'name' => 'onhold_count',
                                       'call_method' => array( 'include_file' => $baseDir . 'eznewsletterfunctioncollection.php',
                                                               'class' => 'eZNewsletterFunctionCollection',
                                                               'method' => 'fetchNewsletterOnHoldCount' ),
                                       'parameter_type' => 'standard',
                                       'parameters' => array( array( 'name' => 'status',
                                                                     'type' => 'integer',
                                                                     'required' => true ) ) );

?>
