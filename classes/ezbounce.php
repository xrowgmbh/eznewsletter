<?php
//
// Definition of eZBounce class
//
// Created on: <13-Jan-2006 02:49:01 oms>
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

/*! \file ezbounce.php
*/

/*!
  \class eZBounce ezbounce.php
  \brief The class eZBounce does

*/


class eZBounce extends eZPersistentObject
{
    const SOFTBOUNCE = 0;
    const HARDBOUNCE = 1;

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
                                         'newslettersenditem_id' => array( 'name' => 'NewsletterID',
                                                                     'datatype' => 'integer',
                                                                     'default' => 0,
                                                                     'required' => true ),
                                         'address' => array( 'name' => 'Address',
                                                              'datatype' => 'string',
                                                              'default' => '',
                                                              'required' => true ),

                                         'bounce_count' => array( 'name' => 'BounceCount',
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true ),
                                         'bounce_type' => array( 'name' => 'BounceType',
                                                                'datatype' => 'integer',
                                                                'default' => 0,
                                                                'required' => true ),
                                         'bounce_arrived' => array( 'name' => 'BounceArrived',
                                                                    'datatype' => 'integer',
                                                                    'default' => 0,
                                                                    'required' => true ),
					                     'bounce_message' => array( 'name' => 'BounceMessage',
					                     			   'datatype' => 'string',
								                       'default' => '',
								                       'required' => false ) ),
                                          'keys' => array( 'id' ),
                                          'function_attributes' => array( 'newsletter_name' => 'getNewsletterName' ),
                                          'increment_key' => 'id',
                                          'sort' => array( 'id' => 'asc' ),
                                          'class_name' => 'eZBounce',
                                          'name' => 'ez_bouncedata' );
    }

   /*!
      \a $offset Offset from start of dataset.
      \a $limit Number of elements to return in each batch.
      \a $asObject Specifies whether to return datasat as objects or rows.
      \return Array of eZNewsletterType.
     */
    static function fetchByOffset( $offset, $limit, $asObject = true, $grouping = false )
    {
        $newsletterTypeList = eZPersistentObject::fetchObjectList( eZBounce::definition(),
                                                            null,
                                                            null,
                                                            array( 'address' => 'ASC' ),
                                                            array( 'offset' => $offset, 'length' => $limit ),
                                                            $asObject,
															array( $grouping )
                                                            );
        return $newsletterTypeList;
    }

    /*!
      Fetches ans eZbounce object by its newslettersenditem_id
     */
    static function fetchBySendItemID( $id, $asObject = true )
    {
        $bounce = eZPersistentObject::fetchObject( eZBounce::definition(),
                                                   null,
                                                   array( 'newslettersenditem_id' => $id ) );
        return $bounce;
    }

    /*!
      Fetches an eZbounce object
     */
    static function fetch( $id, $asObject = true )
    {
        $bounce = eZPersistentObject::fetchObject( eZBounce::definition(),
                                                   null,
                                                   array( 'id' => $id ) );
        return $bounce;
    }

 	/*!
      Fetches bouncelist by mail address
     */
    static function fetchListByAddress( $address, $asObject = true )
    {
        $bounceList = eZPersistentObject::fetchObjectList( eZBounce::definition(),
	                                                   null,
	                                                   array( 'address' => $address ) );
        return $bounceList;
    }
    
    static function fetchAddress( $id, $asObject = true )
    {
        $bounce = eZPersistentObject::fetchObject( eZBounce::definition(),
                                                   null,
                                                   array( 'address' => $id ) );
        return $bounce;
    }

    /*!
     \static

     Get bounce list count

     \return eZBounce count
    */
   /*!
    static function count()
    {
        $rows = eZPersistentObject::fetchObject( eZBounce::definition(),
                                                 array(),
                                                 null,
                                                 false,
                                                 false,
                                                 array( array( 'operation' => 'count( id )',
                                                               'name' => 'count' ) ) );
        return $rows['count'];
    }
    */
    /*!
      Get the eZSendNewsletterItem object belong to the current bounce entry
     */
	function getNewsletterName()
	{
	    $sendItem = eZSendNewsletterItem::fetch(
	        $this->attribute( 'newslettersenditem_id' ), true
	    );  
	
	    if ( $sendItem )  
	    {
	        $newsletter = $sendItem->attribute( 'newsletter' );
	        if ( $newsletter )
	        {
	            $newsletterName = $newsletter->attribute( 'name' );
	            return $newsletterName;
	        }
	    }
	    return false;
	}

    /*!
      \static
      Removes entry from bounce table, as well bounce count in subscription list, and reference from ezsendnewsletteritem table.
      This function should be run as a transaction.
     */
    static function removeAllBounceInformation( $bounceID )
    {
        $bounceItem = eZBounce::fetch( $bounceID );
        if ( !$bounceItem )
        {
            return;
        }

        $sendItem = eZSendNewsletterItem::fetch( $bounceItem->attribute( 'newslettersenditem_id' ), true );
        if ( $sendItem )
        {
            $subscription = eZSubscription::fetch( $sendItem->attribute( 'subscription_id' ) );
            if ( $subscription )
            {
                $subscription->setAttribute( 'bounce_count', 0 );
                $subscription->store();
            }
                $sendItem->setAttribute( 'bounce_id', 0 );
                $sendItem->store();

                $bounceItem->remove();
            
        }
        else
        {
            $bounceItem->remove();
        }

    }

}

?>
