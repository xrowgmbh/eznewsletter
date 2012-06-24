<?php
//
// Definition of eZSendNewsletterItem class
//
// Created on: <19-Dec-2005 12:05:23 hovik>
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

/*! \file ezsendnewsletteritem.php
*/

/*!
  \class eZSendNewsletterItem ezsendnewsletteritem.php
  \brief The class eZSendNewsletterItem does

*/


class eZSendNewsletterItem extends eZPersistentObject
{
    const SendStatusNone = 0;
    const SendStatusSent = 1;
    const SendStatusOnHold = 2;

    /*!
      Constructor
    */
    function __construct( $row )
    {
        $this->eZPersistentObject( $row );
    }

    static function definition()
    {
        return array( 'fields' => array( 'id' => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         'newsletter_id' => array( 'name' => 'NewsletterID',
                                                                   'datatype' => 'integer',
                                                                   'default' => 0,
                                                                   'required' => true ),
                                         'subscription_id' => array( 'name' => 'SubscriptionID',
                                                                     'datatype' => 'integer',
                                                                     'default' => 0,
                                                                     'required' => true ),
                                         'send_status' => array( 'name' => 'SendStatus',
                                                                 'datatype' => 'integer',
                                                                 'default' => 0,
                                                                 'required' => true ),
                                         'send_ts' => array( 'name' => 'SendTS',
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true ),
                                         'hash' => array( 'name' => 'Hash',
                                                          'datatype' => 'string',
                                                          'default' => '',
                                                          'required' => true ),
                                         'object_read_ids' => array( 'name' => 'ArticleReadIDs',
                                                                      'datatype' => 'string',
                                                                      'default' => '',
                                                                      'required' => true ),
                                         'object_print_ids' => array( 'name' => 'ArticlePrintIDs',
                                                                       'datatype' => 'string',
                                                                       'default' => '',
                                                                       'required' => true ),
                                         'bounce_id' => array( 'name' => 'BounceID',
                                                               'datatype' => 'integer',
                                                               'default' => 0,
                                                               'required' => true ) ),
                      'keys' => array( 'id' ),
                      'function_attributes' => array( 'user_data' => 'userData',
                                                      'newsletter' => 'newsletter',
                                                      'newsletter_related_object_list' => 'newsletterRelatedObjectList' ),
                      'increment_key' => 'id',
                      'sort' => array( 'id' => 'asc' ),
                      'class_name' => 'eZSendNewsletterItem',
                      'name' => 'ezsendnewsletteritem' );
    }

    /*!
     \static
     Get number of sent newsletters

     \param newsletter ID

     \return newsletter send count
    */
    static function sendCount( $newsletterID )
    {
        $db = eZDB::instance();

        $totalCountSQL = 'SELECT count(*) AS count
                          FROM ezsendnewsletteritem
                          WHERE newsletter_id = \'' . $db->escapeString( $newsletterID ) . '\' AND
                                send_status = \'' . eZSendNewsletterItem::SendStatusSent . '\'';
        $totalCountResult = $db->arrayQuery( $totalCountSQL );
        return $totalCountResult[0]['count'];
    }

    /*!
     \static
     Get number of newsletters which resultet in an object read on the server.

     \param newsletter ID

     \return number of read newsletters.
    */
    static function readCount( $newsletterID )
    {
        $db = eZDB::instance();

        $totalReadSQL = 'SELECT count(*) AS count
                          FROM ezsendnewsletteritem
                          WHERE newsletter_id = \'' . $db->escapeString( $newsletterID ) . '\' AND
                                send_status = \'' . eZSendNewsletterItem::SendStatusSent . '\' AND
                                object_read_ids != \'\'';
        $totalReadResult = $db->arrayQuery( $totalReadSQL );
        return $totalReadResult[0]['count'];
    }

    function attribute( $attr, $noFunction = false )
    {
        $retVal = false;
        switch( $attr )
        {
            case 'newsletter_related_object_list':
            {
                $newsletter = $this->attribute( 'newsletter' );
                if ( $newsletter )
                {
                    $retVal = $newsletter->attribute( 'object_relation_id_list' );
                }
            } break;

            case 'newsletter':
            {
                $retVal = eZNewsletter::fetch( $this->attribute( 'newsletter_id' ) );
            } break;

            default:
            {
                $retVal = eZPersistentObject::attribute( $attr );
            } break;
        }

        return $retVal;
    }

    /*!
     Fetch by ID
    */
    static function fetch( $id, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZSendNewsletterItem::definition(),
                                                null,
                                                array( 'id' => $id ),
                                                $asObject );
    }

    /*!
     Add object read

     \param object id
    */
    function addObjectRead( $objectID )
    {
        $objectIDArray = explode( '/', $this->attribute( 'object_read_ids' ) );
        array_pop( $objectIDArray );
        array_shift( $objectIDArray );
        if ( !in_array( $objectID, $objectIDArray ) )
        {
            $objectIDArray[] = $objectID;
            $this->setAttribute( 'object_read_ids', '/' . implode( '/', $objectIDArray ) . '/' );
            $this->sync();
        }
    }

    /*!
     Add object print

     \param object id
    */
    function addObjectPrint( $objectID )
    {
        $objectIDArray = explode( '/', $this->attribute( 'object_print_ids' ) );
        array_pop( $objectIDArray );
        array_shift( $objectIDArray );
        if ( !in_array( $objectID, $objectIDArray ) )
        {
            $objectIDArray[] = $objectID;
            $this->setAttribute( 'object_print_ids', '/' . implode( '/', $objectIDArray ) . '/' );
            $this->sync();
        }
    }

    /*!
     \static
     Fetch by hash
    */
    static function fetchByHash( $hash, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZSendNewsletterItem::definition(),
                                                null,
                                                array( 'hash' => $hash ),
                                                $asObject );
    }

    function &userData()
    {
        $retVal = false;
        $subscription = eZSubscription::fetch( $this->attribute( 'subscription_id' ) );

        if ( $subscription )
        {
            $userSubscription = eZUserSubscriptionData::fetch( $subscription->attribute( 'email' ) );

            if (!$userSubscription)
            {
                eZDebug::writeError( "User subscription not found for: ".$subscription->attribute( 'email' ),
                                     'eZSendNewsletterItem::userData' );
                return false;
            }
            else
            {
                $generic_hash = $userSubscription->attribute('hash');

                $retVal = array( 
                    'firstname'         => $subscription->attribute( 'firstname' ),
                    'name'              => $subscription->attribute( 'name' ),
                    'email'             => $subscription->attribute( 'email' ),
                    'mobile'            => $subscription->attribute( 'mobile' ),
                    'userhash'          => $this->attribute( 'hash' ),
                    'generic_userhash'  => $generic_hash
                );
            }
        }
        return $retVal;
    }

    /*!
     Fetch array by newsletter ID

     \param newsletterID
     \param offset
     \param limit
     \param sendStatus, defaul : eZSendNewsletterItem::SendStatusNone
     \param asObject
    */
    function fetchByNewsletterID( $newsletterID,
                                  $offset = 0,
                                  $limit = 50,
                                  $sendStatus = eZSendNewsletterItem::SendStatusNone,
                                  $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( eZSendNewsletterItem::definition(),
                                                    null,
                                                    array( 'newsletter_id' => $newsletterID,
                                                           'send_status' => $sendStatus ),
                                                    null,
                                                    array( 'offset' => $offset, 'length' => $limit ),
                                                    $asObject );
    }

    /*!
     Create and store new entry
    */
    function create( $newsletterID, $subscriptionID, $status = eZSendNewsletterItem::SendStatusNone )
    {
        if ( eZSendNewsletterItem::itemExists( $newsletterID, $subscriptionID ) )
        {
            return false;
        }

        $sendItem = new eZSendNewsletterItem( array( 'newsletter_id' => $newsletterID,
                                                     'subscription_id' => $subscriptionID,
                                                     'hash' => md5( time() . '-' . $newsletterID . '-' . mt_rand() ),
                                                     'send_status' => $status ) );
        $sendItem->store();

        return $sendItem;
    }

    /*!
     Check if senditem already exists
    */
    function itemExists( $newsletterID, $subscriptionID )
    {
        $result = eZPersistentObject::fetchObject( eZSendNewsletterItem::definition(),
                                                   null,
                                                   array( 'newsletter_id' => $newsletterID,
                                                          'subscription_id' => $subscriptionID ),
                                                   false );
        if ( !$result )
        {
            return false;
        }
        return true;
    }

    /*!
     \static

     Get bounce list count

     \return eZSendNewsletterItem count
    */
    static function countAll( $status = eZSendNewsletterItem::SendStatusNone)
    {
        $rows = eZPersistentObject::fetchObject( eZSendNewsletterItem::definition(),
                                                 array(),
                                                 array( 'send_status' => $status ),
                                                 false,
                                                 false,
                                                 array( array( 'operation' => 'count( id )',
                                                               'name' => 'count' ) ) );
        return $rows['count'];
    }

    /*!
      \static
      Get the map of send statuses
     */
    static function sendStatusNameMap()
    {
        return array( eZSendNewsletterItem::SendStatusNone => ezpI18n::tr( 'eznewsletter/senditem_status', 'Send mailing' ),
                      eZSendNewsletterItem::SendStatusSent => ezpI18n::tr( 'eznewsletter/senditem_status', 'Mark as sent' ),
                      eZSendNewsletterItem::SendStatusOnHold => ezpI18n::tr( 'eznewsletter/senditem_status', 'On hold' ) );
    }

    /*!
      \static
      Remove objects with \a id
     */
    static function removeEntry( $id )
    {
        eZPersistentObject::removeObject( eZSendNewsletterItem::definition(),
                                          array( 'id' => $id ) );
    }

}

?>
