<?php
//
// Definition of eZSubscriptionList class
//
// Created on: <06-Dec-2005 13:31:34 hovik>
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

/*! \file ezsubscriptionlist.php
*/

/*!
  \class eZSubscriptionList ezsubscriptionlist.php
  \brief The class eZSubscriptionList does

*/

class eZSubscriptionList extends eZPersistentObject
{
    const StatusDraft = 0;
    const StatusPublished = 1;
    
    const LoginStepsOne = 0;
    const LoginStepsTwo = 1;
    
    const URLTypeID = 0;
    const URLTypeName = 1;
    
    /*!
     Constructor
    */
    function __construct( $row )
    {
        parent::__construct( $row );
        #$this->eZPersistentObject( $row );
	
    }

    static function definition()
    {
        return array( 'fields' => array( 'id' => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         'name' => array( 'name' => 'Name',
                                                          'datatype' => 'string',
                                                          'default' => '',
                                                          'required' => true ),
                                         'url_type' => array( 'name' => 'URLType',
                                                          'datatype' => 'integer',
                                                          'default' => 0,
                                                          'required' => true ),
                                         'url' => array( 'name' => 'URL',
                                                          'datatype' => 'string',
                                                          'default' => '',
                                                          'required' => true ),
                                         'description' => array( 'name' => 'Description',
                                                                 'datatype' => 'string',
                                                                 'default' => '',
                                                                 'required' => true ),
                                         'allow_anonymous' => array( 'name' => 'allowAnonymous',
                                                                     'datatype' => 'integer',
                                                                     'default' => 1,
                                                                     'required' => true ),
                                         'require_password' => array( 'name' => 'requirePassword',
                                                                      'datatype' => 'integer',
                                                                      'default' => 1,
                                                                      'required' => true ),
                                         'login_steps' => array( 'name' => 'loginSteps',
                                                                     'datatype' => 'integer',
                                                                     'default' => 0,
                                                                     'required' => true ),
                                         'auto_confirm_registered' => array( 'name' => 'autoConfirmRegistered',
                                                                             'datatype' => 'integer',
                                                                             'default' => 1,
                                                                             'required' => true ),
                                         'auto_approve_registered' => array( 'name' => 'autoApproveRegistered',
                                                                             'datatype' => 'integer',
                                                                             'default' => 0,
                                                                             'required' => true ),
                                         'created' => array( 'name' => 'Created',
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true ),
                                         'creator_id' => array( 'name' => 'Creator',
                                                                'datatype' => 'integer',
                                                                'default' => 0,
                                                                'required' => true ),
                                         'related_object_id_1' => array( 'name' => 'RelatedObjectID1',
                                                                         'datatype' => 'integer',
                                                                         'default' => 0,
                                                                         'required' => true ),
                                         'related_object_id_2' => array( 'name' => 'RelatedObjectID2',
                                                                         'datatype' => 'integer',
                                                                         'default' => 0,
                                                                         'required' => true ),
                                         'related_object_id_3' => array( 'name' => 'RelatedObjectID3',
                                                                         'datatype' => 'integer',
                                                                         'default' => 0,
                                                                         'required' => true ),
                                         'status' => array( 'name' => 'Status',
                                                            'datatype' => 'integer',
                                                            'default' => 0,
                                                            'required' => true ),
                                         'allowed_siteaccesses' => array( 'name' => 'AllowedSiteaccesses',
                                                                          'datatype' => 'string',
                                                                          'default' => '',
                                                                          'required' => false ) ),
                      'keys' => array( 'id', 'status' ),
                      'increment_key' => 'id',
                      'function_attributes' =>
                            array( 'creator'            => 'creator',
                                   'subscription_count' => 'subscriptionCount',
                                   'related_object_1'   => 'relatedObject1',
                                   'related_object_2'   => 'relatedObject2',
                                   'related_object_3'   => 'relatedObject3',
                                   'url_alias'          => 'urlAlias',
                                   'allowed_siteaccesses_array' => 'allowedSiteaccessesArray' ),
                      'increment_key' => 'id',
                      'sort'          => array( 'id' => 'asc' ),
                      'class_name'    => 'eZSubscriptionList',
                      'name'          => 'ezsubscription_list' );
		     }

    /*!
     \reimp
    */
	

    function attribute( $attr, $noFunction = false )
    { 
        $retVal = null;
        switch( $attr )
        {
            case 'related_object_1':
            case 'related_object_2':
            case 'related_object_3':
            {
                /* #DEPRECATED# */
                $retVal = eZContentObject::fetch( $this->attribute( 'related_object_id_' . substr( $attr, -1, 1 ) ) );
            } break;

            default:
            {
                $retVal = eZPersistentObject::attribute( $attr );
            } break;
        }

        return $retVal;
    }


    /*!
     Get URL alias
    */
    function urlAlias()
    {
        switch( $this->attribute( 'url_type' ) )
        {
            case eZSubscriptionList::URLTypeName:
            {
                return $this->attribute( 'url' );
            } break;

            default:
            case eZSubscriptionList::URLTypeID:
            {
                return $this->attribute( 'id' );
            } break;
        }
    }

    /*!
     Add regular subscription to subscription list

     \param name
     \param email
     \param password
    */
    function registerSubscription( $firstname, $name, $mobile, $email, $password = false )
    {
        if ( $this->emailSubscriptionExists( $email ) )
        {
            return false;
        }

        $subscription = eZSubscription::create( $this->attribute( 'id' ),
                                                $firstname,
                                                $name,
                                                $mobile,
                                                $email );

        $user = eZUser::instance();

        if ( $this->attribute( 'auto_confirm_registered' ) &&  $user->isLoggedIn( ) )
        {
            $subscription->setAttribute( 'status', eZSubscription::StatusConfirmed );

            if ( $this->attribute( 'auto_approve_registered' ) )
            {
                $subscription->setAttribute( 'status', eZSubscription::StatusApproved );
            }
        }

        if ( $password !== false )
        {
            $subscription->setAttribute( 'password', $password );
        }

        if ( !is_object( $subscription ) )
        {
            eZDebug::writeError( 'Newsletter Anmeldefehler', __METHOD__ );
            eZDebug::writeError( $this->attribute( 'id' ), 'subscription id' );
            eZDebug::writeError( $name, 'name' );
            eZDebug::writeError( $firstname, 'firstname' );
            eZDebug::writeError( $email, 'email' );
        }

        $subscription->publish();

        return $subscription;
    }

    /*!
     Add eZ user to subscription list

     \param user ID ( automaticly confirmed )
    */
    function registerUser( $userID )
    {
        if ( $this->userExists( $userID ) )
        {
            return false;
	
        }

        $subscription = eZSubscription::create( $this->attribute( 'id' ), '', '', '', '', $userID );

		if ( !$subscription )
		{
			eZDebug::writeError( 'could not create eZSubscription' , __METHOD__.' line:'.__LINE__);
			return false;
		}

        $subscription->setAttribute( 'status', eZSubscription::StatusConfirmed );

        if ( $this->attribute( 'auto_approve_registered' ) )
        {
            $subscription->setAttribute( 'status', eZSubscription::StatusApproved );
        }

        $subscription->publish();

        return $subscription;
    }

    /*!
     Unsubscribe regular subscription to subscription list
    */
    function unsubscribeSubscription( $email, $password = false )
    {
        $subscription = eZSubscription::fetchByEmailSubscriptionListID( $email,
                                                                        $this->attribute( 'id' ) );
        if ( $subscription )
        {
            if ( $password != false ||
                 $this->attribute( 'require_password' ) )
            {
                if ( $subscription->attribute( 'password' ) != md5( $password ) )
                {
                    $subscription = false;
                }
            }
        }

        if ( $subscription )
        {
            $subscription->unsubscribe();
            return true;
        }

        return false;
    }

    /*!
     Fetch subscription list a user is subscribed to, base on email
    */
    function fetchSubscribeListByUserID( $userID )
    {
        $subscriptionListArray = array();
        foreach( eZSubscription::fetchListByUserID( $userID ) as $subscription )
        {
            $subscriptionListArray[] = $subscription->attribute( 'subscription_list' );
        }

        return $subscriptionListArray;
    }

    /*!
     Fetch subscription list a user is subscribed to, base on email
    */
    function fetchSubscribeListByEmail( $email )
    {
        $subscriptionListArray = array();
        foreach( eZSubscription::fetchListByEmail( $email ) as $subscription )
        {
            $subscriptionListArray[] = $subscription->attribute( 'subscription_list' );
        }

        return $subscriptionListArray;
    }

    /*!
     Unsubscribe eZ user to subscription list

     \param user ID ( automaticly confirmed )
    */
    function unsubscribeUser( $userID )
    {
        $subscription = eZSubscription::fetchByUserSubscriptionListID( $userID,
                                                                       $this->attribute( 'id' ) );
        if ( $subscription )
        {
            $subscription->unsubscribe();
            return true;
        }

        return false;
    }

    /*!
     Fetch subscription list a user is subscribed to, base on userId
    */
    function fetchSubscriptionByUserID( $userID )
    {
        $subscriptionListArray = array();
        foreach( eZSubscription::fetchListByUserID( $userID ) as $subscription )
        {
            $subscriptionListArray[] = $subscription;
        }
        return $subscriptionListArray;
    }

    /*!
     Fetch subscription list a user is subscribed to, base on email
    */
    function fetchSubscriptionByEmail( $email )
    {
        $subscriptionListArray = array();
        foreach( eZSubscription::fetchListByEmail( $email ) as $subscription )
        {
            $subscriptionListArray[] = $subscription;
        }
        return $subscriptionListArray;
    }

    /*!
     Get number of subscribers
    */
    function subscription()
    {
        return eZSubscription::count( $this->attribute( 'id' ) );
    }

    /*!
     \static

     Fetch draft of the eZSubscriptionList. If none exist, create one base on the published object

     \param id
    */
    static function fetchDraft( $id, $asObject = true )
    {
        $subscriptionList = eZSubscriptionList::fetch( $id, eZSubscriptionList::StatusDraft, $asObject );
        if ( !$subscriptionList )
        {
            $subscriptionList = eZSubscriptionList::fetch( $id, eZSubscriptionList::StatusPublished, $asObject );
            if ( $subscriptionList )
            {
                $subscriptionList->setAttribute( 'status', eZSubscriptionList::StatusDraft );
                $subscriptionList->store();
            }
        }

        if ( !$subscriptionList )
        {
            return false;
        }

        return $subscriptionList;
    }

    /*!
     \static

     Fetch eZSubscriptionList object

     \param id
     \param status
    */
    static function fetch( $id, $status = eZSubscriptionList::StatusPublished, $asObject = true, $forceID = false )
    {
        $condArray = array( 'id' => $id,
                            'status' => $status );
        if ( !$forceID )
        {
            $condArray['url_type'] = eZSubscriptionList::URLTypeID;
        }

        $subscriptionList = eZPersistentObject::fetchObject( eZSubscriptionList::definition(),
                                                             null,
                                                             $condArray,
                                                             $asObject );
        if ( !$subscriptionList )
        {
            $subscriptionList = eZPersistentObject::fetchObject( eZSubscriptionList::definition(),
                                                                 null,
                                                                 array( 'url' => $id,
                                                                        'status' => $status,
                                                                        'url_type' => eZSubscriptionList::URLTypeName ),
                                                                 $asObject );
        }

        return $subscriptionList;
    }

    /*!
     Publish eZSubscriptionList.
     1. Sets status to publish
     2. Stores object
     3. Removes draft
    */
    function publish()
    {
        $this->setAttribute( 'status', eZSubscriptionList::StatusPublished );
        $this->store();
        $this->removeDraft();
    }

    /*!
     Remove draft of current object
    */
    function removeDraft()
    {
        $subscriptionListDraft = eZSubscriptionList::fetchDraft( $this->attribute( 'url_alias' ) );
        $subscriptionListDraft->remove();
    }

    /*!
     \static
     Remove all entries of specified ID.

     \param ID
    */
    static function removeAll( $id )
    {
        eZPersistentObject::removeObject( eZSubscriptionList::definition(),
                                          array( 'id' => $id ) );
    }

    /*!
     Check if an email address is already registered in the subscription list

     \param email address
    */
    function emailSubscriptionExists( $email )
    {
        $subscription = eZSubscription::fetchByEmailSubscriptionListID( $email, $this->attribute( 'id' ) );
        if ( $subscription )
        {
            return true;
        }
        return false;
    }

    /*!
     Check if user exists in subscription list

     \param user id
    */
    function userExists( $userID )
    {
        $subscription = eZSubscription::fetchByUserSubscriptionListID( $userID, $this->attribute( 'id' ) );
        if ( $subscription )
        {
            return true;
        }
        return false;
    }

    /*!
     \static

     Create new eZSubscriptionList object
    */
    static function create( $name = '', $userID = false )
    {
        if ( $userID === false )
        {
            $userID = eZUser::currentUserID();
        }

        $subscriptionList = new eZSubscriptionList( array( 'created' => time(),
                                                           'creator_id' => $userID,
                                                           'name' => $name,
                                                           'status' => eZSubscriptionList::StatusDraft ) );
        $subscriptionList->store();

        return $subscriptionList;
    }

    /*!
     Get Creator user object
    */
    function creator()
    {
        return eZUser::fetch( $this->attribute( 'creator_id' ) );
    }

    /*!
     Verify if current siteaccess is allowed to use this list
    */
    function siteaccessAllowed()
    {
	$currentAccessArray = $GLOBALS['eZCurrentAccess'];
	$allowedSiteaccessesList = $this->attribute( 'allowed_siteaccesses_array' );
	if( in_array( $currentAccessArray['name'], $allowedSiteaccessesList ) || $this->attribute( 'status' ) == eZSubscriptionList::StatusDraft )
	{
    	    return true;
    	}
    	return false;
    }

    /*!
     \static

      Fetch list of subscriptions


     \param offset
     \param limit
     \param as object

     \return list of subscription list items
    */
    static function fetchList( $offset = 0, $limit = 10, $asObject = true, $status = eZSubscriptionList::StatusPublished, $useFilter = false )
    {
		$custom_conds = null;
		if( $useFilter )
		{
		    $currentAccessArray = $GLOBALS['eZCurrentAccess'];
		    $custom_conds = 'AND allowed_siteaccesses LIKE \'%'.$currentAccessArray['name'].'%\'';
		}

        return eZPersistentObject::fetchObjectList( eZSubscriptionList::definition(),
                                                    null,
                                                    array( 'status' => $status ),
                                                    null,
                                                    array( 'limit' => $limit,
                                                           'offset' => $offset ),
                                                    $asObject,
                                                    false,
                                                    null,
                                                    null,
                                                    $custom_conds );
    }

    /*!
     Fetch subscription list

     \param offset
     \param limit
     \param $asObject
     \param version status
     \param status
    */
    function fetchSubscriptionArray( $offset = 0,
                                     $limit = 100,
                                     $asObject = true,
                                     $versionStatus = eZSubscription::VersionStatusPublished,
                                     $status = eZSubscription::StatusApproved )
    {
        return eZPersistentObject::fetchObjectList( eZSubscription::definition(),
                                                    null,
                                                    array( 'version_status' => $versionStatus,
                                                           'status' => $status,
                                                           'subscriptionlist_id' => $this->attribute( 'id' ) ),
                                                    null,
                                                    array( 'limit' => $limit,
                                                           'offset' => $offset ),
                                                    $asObject );
    }

    /*!
     Fetch subscription list count

     \param version status
     \param status
    */
    function fetchSubscriptionArrayCount( $versionStatus = eZSubscription::VersionStatusPublished,
                                         $status = eZSubscription::StatusApproved )
    {
        $rows = eZPersistentObject::fetchObject( eZSubscription::definition(),
                                                 array(),
                                                 array( 'version_status' => $versionStatus,
                                                        'status' => $status,
                                                        'subscriptionlist_id' => $this->attribute( 'id' ) ),
                                                 false,
                                                 false,
                                                 array( array( 'operation' => 'count( id )',
                                                               'name' => 'count' ) ) );
        return $rows['count'];
    }

    /*!
     \static
     Fetches the subscription list amount
     \return eZSubscriptionList count
    */
    static function countAll( $status = eZSubscriptionList::StatusPublished, $useFilter = false )
    {
        $custom_conds = null;
        if( $useFilter )
        {
            $currentAccessArray = $GLOBALS['eZCurrentAccess'];
            $custom_conds = 'AND allowed_siteaccesses LIKE \'%'.$currentAccessArray['name'].'%\'';
        }
        $rows = eZPersistentObject::fetchObjectList( eZSubscriptionList::definition(),
                                                     array(),
                                                     array( 'status' => $status ),
                                                     null,
                                                     null,
                                                     false,
                                                     null,
                                                     array( array( 'operation' => 'count( id )',
                                                                   'name' => 'count' ) ),
                                                     null,
                                                     $custom_conds );
        return $rows[0]['count'];
    }
    
	/*!
     Get number of subscribers
    */
    function subscriptionCount()
    {
        $count = eZSubscription::count( $this->attribute( 'id' ) );
        return $count;
    }

    /*!
     \static
     Get login step name map
    */
    static function loginStepsNameMap()
    {
        return array( eZSubscriptionList::LoginStepsOne => ezpI18n::tr( 'eznewsletter/login_steps', 'Confirm user' ),
                      eZSubscriptionList::LoginStepsTwo => ezpI18n::tr( 'eznewsletter/login_steps', 'Confirm and approve user' ) );
    }

    /*!
     Return allowed siteaccesses
    */
    function allowedSiteaccessesArray()
    {
	$allowedSiteaccesses = $this->attribute( 'allowed_siteaccesses', true );
        $allowedSiteaccessesArray = explode( eZNewsletterType::FieldSeparationCharacter,
                                             $allowedSiteaccesses );
    return $allowedSiteaccessesArray;
    }
}

?>
