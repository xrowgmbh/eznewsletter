<?php
//
// Definition of eZNewsletterType class
//
// Created on: <31-Dez-2007 10:41:07>
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

/*! \file eznewslettertypesubscription.php
*/

/*!
  \class eZNewsletterTypeSubscription eznewslettertypesubscription.php
  \brief The class eZNewsletterTypeSubscription does

*/


class eZNewsletterTypeSubscription extends eZPersistentObject
{
    /*!
     Constructor
    */
    function __construct( $row )
    {
        $this->eZPersistentObject( $row );
    }

    static function definition()
    {
        return array( "fields" => array( "newsletter_id" => array( 'name' => 'NewsletterID',
                                                                   'datatype' => 'integer',
                                                                   'default' => 0,
                                                                   'required' => true ),
                                         "status" => array( 'name' => 'Status',
                                                            'datatype' => 'integer',
                                                            'default' => 0,
                                                            'required' => true ),
                                         "subscription_id" => array( 'name' => 'SubscriptionID',
                                                                     'datatype' => 'integer',
                                                                     'default' => 0,
                                                                     'required' => true ) ),
                      'function_attributes' => array( 'subscription_object' => 'subscriptionObject' ),
                      "keys" => array( 'newsletter_id', 'status', 'subscription_id' ),
                      'sort' => array( 'subscription_id' => 'asc' ),
                      "class_name" => "eZNewsletterTypeSubscription",
                      "name" => "ez_newsletter_subscription" );
    }

    function subscriptionObject()
    {
        $retVal = eZSubscriptionList::fetch( $this->attribute( 'subscription_id' ), $this->attribute( 'status' ), true, true );
        return $retVal;
    }

    /*!
     fetch
    */
    static function fetch( $newsletterTypeID, $subscriptionListID, $status = eZNewsletterType::StatusPublished, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZNewsletterTypeSubscription::definition(),
                                                null,
                                                array( 'newsletter_id' => $newsletterTypeID,
                                                       'subscription_id' => $subscriptionListID,
                                                       'status' => $status ),
                                                $asObject );
    }

    /*!
     Fetch list of newsletter type subscriptions.

     fetch
    */
    static function fetchList( $newsletterTypeID, $subscriptionListID = false, $status = eZNewsletterType::StatusPublished, $asObject = true )
    {
        $condArray = array( 'newsletter_id' => $newsletterTypeID,
                            'status' => $status );
        if ( $subscriptionListID !== false )
        {
            $condArray['subscription_id'] = $subscriptionListID;
        }

        return eZPersistentObject::fetchObjectList( eZNewsletterTypeSubscription::definition(),
                                                    null,
                                                    $condArray,
                                                    null,
                                                    null,
                                                    $asObject );
    }

    /*!
     Add
    */
    static function add( $newsletterTypeID, $subscriptionListID, $status = eZNewsletterType::StatusDraft )
    {
        $existing = eZNewsletterTypeSubscription::fetch( $newsletterTypeID, $subscriptionListID, $status );
        if ( !$existing )
        {
            $newAssignment = new eZNewsletterTypeSubscription( array( 'newsletter_id' => $newsletterTypeID,
                                                                      'subscription_id' => $subscriptionListID,
                                                                      'status' => $status ) );
            $newAssignment->store();
        }
    }

    static function removeByCondition( $conditions = null, $extraConditions = null )
    {
        /*$def = $this->definition();
        $keys = $def['keys'];
        if ( !is_array( $conditions ) )
        {
            $conditions = array();
            foreach ( $keys as $key )
            {
                $value = $this->attribute( $key );
                $conditions[$key] = $value;
            }
        }*/

        eZPersistentObject::removeObject( eZNewsletterTypeSubscription::definition(),
                                          $conditions, $extraConditions );
    }

    /*!
     Publish
    */
    static function publish( $newsletterTypeID )
    {
        eZNewsletterTypeSubscription::removeByCondition( array( 'newsletter_id' => $newsletterTypeID, 'status' => eZNewsletterType::StatusPublished ) );
        foreach( eZNewsletterTypeSubscription::fetchList( $newsletterTypeID, false, eZNewsletterType::StatusDraft ) as $assignment )
        {
            $assignment->setAttribute( 'status', eZNewsletterType::StatusPublished ); // TODO, copy, not alter.
            $assignment->store();
        }
    }
}

?>
