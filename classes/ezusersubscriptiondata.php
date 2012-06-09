<?php
//
// Definition of eZUserSubscriptionData class
//
// Created on: <10-Jan-2006 10:21:19 hovik>
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

/*! \file ezusersubscriptiondata.php
*/

/*!
  \class eZUserSubscriptionData ezusersubscriptiondata.php
  \brief The class eZUserSubscriptionData does

*/

class eZUserSubscriptionData extends eZPersistentObject
{
    /*!
     Constructor
    */
    function __construct( $row )
    {
        parent::__construct( $row );
    }

    static function definition()
    {
        return array( 'fields' => array( 'id' => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         'email' => array( 'name' => 'Email',
                                                           'datatype' => 'string',
                                                           'default' => '',
                                                           'required' => true ),
                                         'firstname' => array( 'name' => 'Firstname',
                                                          'datatype' => 'string',
                                                          'default' => '',
                                                          'required' => false ),
                                         'name' => array( 'name' => 'Name',
                                                          'datatype' => 'string',
                                                          'default' => '',
                                                          'required' => false ),
                                         'mobile' => array( 'name' => 'Mobile',
                                                          'datatype' => 'string',
                                                          'default' => '',
                                                          'required' => false ),
                                         'password' => array( 'name' => 'Password',
                                                          'datatype' => 'string',
                                                          'default' => '',
                                                          'required' => false ),
                                         'hash' => array( 'name' => 'Hash',
                                                          'datatype' => 'string',
                                                          'default' => '',
                                                          'required' => true ) ),
                      'function_attributes' => array( 'subscription_list' => 'subscriptionList' ),
                      'keys'                => array( 'id' ),
                      'increment_key'       => 'id',
                      'sort'                => array( 'id' => 'asc' ),
                      'class_name'          => 'eZUserSubscriptionData',
                      'name'                => 'ezsubscriptionuserdata' );
    }

    /*!
     \reimp
    */
    function setAttribute( $attr, $value )
    {
        switch( $attr )
        {
            case 'email':
            {
                $emailExists = eZUserSubscriptionData::fetch( $value );
                if ( !$emailExists )
                {
                    // Fetch the list of subscriptions the user identified by email
                    $subscriptionList = eZSubscription::fetchListByEmail( $this->attribute( 'email' ),
                                                                          false,
                                                                          false );
                    foreach( $subscriptionList as $subscription )
                    {
                        $subscription->setAttribute( 'email', $value );
                        $subscription->sync();
                    }

                    eZPersistentObject::setAttribute( $attr, $value );
                }
            } break;

            default:
            {
                eZPersistentObject::setAttribute( $attr, $value );
            } break;
        }
    }

    /*!
     Update the attribute but bypass setAttribute
    */
    function updateAttribute( $attr, $value )
    {
        eZPersistentObject::setAttribute( $attr, $value );
    }

    /*!
     Get subscription list for this user
    */
    function subscriptionList()
    {
        return eZSubscription::fetchListByEmail( $this->attribute( 'email' ) );
    }

    /*!
     Fetch by hash
    */
    static function fetchByHash( $hash, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZUserSubscriptionData::definition(),
                                                null,
                                                array( 'hash' => $hash ),
                                                $asObject );
    }

    /*!
     Fetch by id
    */
    static function fetchById( $id, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZUserSubscriptionData::definition(),
                                                null,
                                                array( 'id' => $id ),
                                                $asObject );
    }


    /*!
     Fetch object by email address ( unique )
    */
    static function fetch( $email, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZUserSubscriptionData::definition(),
                                                null,
                                                array( 'email' => $email ),
                                                $asObject );
    }

    /*!
     Create new object
    */
    static function create( $firstname, $name, $mobile, $email )
    {
        if ( !$email )
        {
            return false;
        }
        $rows = array( 'firstname' => $firstname,
                       'name'      => $name,
                       'mobile'    => $mobile,
                       'email'     => $email,
                       'hash'      => md5( time() . '-' . mt_rand() ) );
        $userData = new eZUserSubscriptionData( $rows );
        $userData->store();

        return $userData;
    }

    /*
     \param ID
    */
    static function removeAll( $id )
    {
        eZPersistentObject::removeObject(  eZUserSubscriptionData::definition(),
                                           array( 'id' => $id ) );
    }
}

?>
