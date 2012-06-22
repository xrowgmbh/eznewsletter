<?php
//
// Definition of eZSubscription class
//
// Created on: <30-Nov-2005 10:51:25 oms>
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

/*! \file ezsubscription.php
*/

/*!
  \class eZSubscription ezsubscription.php
  \brief The class eZSubscription does

*/

class eZSubscription extends eZPersistentObject
{
    const VersionStatusDraft = 0;
    const VersionStatusPublished = 1;
    
    #const VIPNone = 0;
    #const VIPSilver = 1;
    #const VIPGold = 2;
    #const VIPPlatinum = 3;
    
    const StatusPending = 0;
    const StatusConfirmed = 1;
    const StatusApproved = 2;
    const StatusRemovedSelf = 3;
    const StatusRemovedAdmin = 4;
    
    const FieldSeparationCharacter = ',';
    
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
                                         'version_status' => array( 'name' => 'VersionStatus',
                                                                    'datatype' => 'integer',
                                                                    'default' => 0,
                                                                    'required' => true ),
                                         'subscriptionlist_id' => array( 'name' => 'SubscriptionListID',
                                                                         'datatype' => 'integer',
                                                                         'default' => 0,
                                                                         'required' => true ),
                                         'email' => array( 'name' => 'Email',
                                                           'datatype' => 'string',
                                                           'default' => '',
                                                           'required' => true ),
                                         'hash' => array( 'name' => 'Hash',
                                                          'datatype' => 'string',
                                                          'default' => '',
                                                          'required' => true ),
                                         'status' => array( 'name' => 'Status',
                                                            'datatype' => 'integer',
                                                            'default' => 0,
                                                            'required' => true ),
                                         'vip' => array( 'name' => 'VIP',
                                                         'datatype' => 'integer',
                                                         'default' => 0,
                                                         'required' => true ),
                                         'last_active' => array( 'name' => 'LastActive',
                                                                 'datatype' => 'integer',
                                                                 'default' => 0,
                                                                 'required' => true ),
                                         'created' => array( 'name' => 'Created',
                                                             'datatype' => 'interger',
                                                             'default' => 0,
                                                             'required' => true ),
                                         'creator_id' => array( 'name' => 'CreatorID',
                                                                'datatype' => 'interger',
                                                                'default' => 0,
                                                                'required' => true ),
                                         'confirmed' => array( 'name' => 'Confirmed',
                                                               'datatype' => 'integer',
                                                               'default' => 0,
                                                               'required' => true ),
                                         'approved' => array( 'name' => 'Approved',
                                                               'datatype' => 'integer',
                                                               'default' => 0,
                                                               'required' => true ),
                                         'removed' => array( 'name' => 'Removed',
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true ),
                                         'contentobject_id' => array( 'name' => 'ContentObjectID',
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true ),
                                         'user_id' => array( 'name' => 'UserID',
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true ),
                                         'bounce_count' => array( 'name' => 'BounceCount',
                                                                  'datatype' => 'integer',
                                                                  'default' => 0,
                                                                  'required' => false ) ),
                      'function_attributes' =>
                            array( 'user'                   => 'user',
                                   'subscription_list'      => 'subscriptionList',
                                   'usersubscriptiondata'   => 'userSubscriptionData',
                                   'firstname'              => 'firstname',
                                   'name'                   => 'name',
                                   'mobile'                 => 'mobile',
                                   'password'               => 'password' ),
                      'keys'            => array( 'id', 'version_status' ),
                      'increment_key'   => 'id',
                      'sort'            => array( 'id' => 'asc' ),
                      'class_name'      => 'eZSubscription',
                      'name'            => 'ezsubscription' );
    }

    /*!
     Send confirmation email if user is not confirmed
    */
    function sendConfirmation()
    {
        if ( $this->attribute( 'status' ) != eZSubscription::StatusPending )
        {
            return;
        }

        $res = eZTemplateDesignResource::instance();
        $ini = eZINI::instance();
        $hostname = eZSys::hostname();
        $template = 'design:eznewsletter/sendout/registration.tpl';

        $tpl = eZNewsletterTemplateWrapper::templateInit();
        $tpl->setVariable( 'userData', eZUserSubscriptionData::fetch( $this->attribute( 'email' ) ) );
        $tpl->setVariable( 'hostname', $hostname );
        $tpl->setVariable( 'subscription', $this );
        $tpl->setVariable( 'subscriptionList', $this->attribute( 'subscription_list' ) );
        
        $templateResult = $tpl->fetch( $template );
        
        if ( $tpl->hasVariable( 'subject' ) )
            $subject = $tpl->variable( 'subject' );

        $mail = new eZMail();

        $mail->setSender( $ini->variable( 'MailSettings', 'EmailSender' ) );
        $mail->setReceiver( $this->attribute( 'email' ) );
        $mail->setBody( $templateResult );
        $mail->setSubject( $subject );

        eZMailTransport::send( $mail );
    }

    /*!
     Get subscription list this subscription belongs to
    */
    function subscriptionList()
    {
        return eZSubscriptionList::fetch( $this->attribute( 'subscriptionlist_id' ),
                                          eZSubscriptionList::StatusPublished,
                                          true, true );
    }

    /*!
     \reimp
    */
    function attribute( $attr, $noFunction = false )
    {
        switch( $attr )
        {
            case 'password':
            {
                $password = false;
                $userData = eZUserSubscriptionData::fetch( $this->attribute( 'email' ) );
                if ( $userData )
                {
                    $password = $userData->attribute( 'name' );
                }

                return $password;
            } break;

            case 'firstname':
            {
                $firstname = false;
                if ( $this->attribute( 'user_id' ) != 0 )
                {
                    $user = eZUser::fetch( $this->attribute( 'user_id' ) );
                    if ( $user )
                    {
                        $contentObject = $user->attribute( 'contentobject' );
                        $dataMap = $contentObject->dataMap();
                        $firstname = $dataMap['first_name']->attribute( 'data_text' );
                    }
                }
                else
                {
                    $userData = eZUserSubscriptionData::fetch( $this->attribute( 'email' ) );
                    if ( $userData )
                    {
                        $firstname = $userData->attribute( 'firstname' );
                    }
                }
                return $firstname;
            } break;

            case 'name':
            {
                $name = false;
                if ( $this->attribute( 'user_id' ) != 0 )
                {
                    $user = eZUser::fetch( $this->attribute( 'user_id' ) );
                    if ( $user )
                    {
                        $contentObject = $user->attribute( 'contentobject' );
                        $dataMap = $contentObject->dataMap( );
                        $name = $dataMap['last_name']->attribute( 'data_text' );
                    }
                }
                else
                {
                    $userData = eZUserSubscriptionData::fetch( $this->attribute( 'email' ) );
                    if ( $userData )
                    {
                        $name = $userData->attribute( 'name' );
                    }
                }

                return $name;
            } break;

            case 'mobile':
            {
                $mobile = false;

                $userData = eZUserSubscriptionData::fetch( $this->attribute( 'email' ) );
                if ( $userData )
                {
                    $mobile = $userData->attribute( 'mobile' );
                }

                return $mobile;
            } break;

            case 'email':
            {
                if ( $this->attribute( 'user_id' ) != 0 )
                {
                    $user = eZUser::fetch( $this->attribute( 'user_id' ) );
                    if ( $user )
                    {
                        $email = $user->attribute( 'email' );
                        return $email;
                    }
                }

                $email = eZPersistentObject::attribute( $attr );
                return $email;
            } break;

            default:
            {
                $value = eZPersistentObject::attribute( $attr );
                return $value;
            } break;
        }
    }

    /*
     Unsubscribe subscription
    */
    function unsubscribe()
    {
        $this->setAttribute( 'status', eZSubscription::StatusRemovedSelf );
        $this->sync();
    }

    /*!
     \reimp
    */
    function setAttribute( $attr, $value )
    {
        $userData = eZUserSubscriptionData::fetch( $this->attribute( 'email' ) );

        switch( $attr )
        {
            case 'email':
            {
                if ( !$userData )
                {
                    $userData = eZUserSubscriptionData::create( '', '', '', $value );
                }
                else
                {
                    $userData->updateAttribute( 'email', $value );
                    $userData->store();
                }

                eZPersistentObject::setAttribute( $attr, $value );
            } break;

            case 'name':
            {
                if ( $userData )
                {
                    $userData->setAttribute( $attr, $value );
                    $userData->store();
                }
            } break;

            case 'firstname':
            {
                if ( $userData )
                {
                    $userData->setAttribute( $attr, $value );
                    $userData->store();
                }
            } break;

            case 'mobile':
            {
                if ( $userData )
                {
                    $userData->setAttribute( $attr, $value );
                    $userData->store();
                }
            } break;

            case 'password':
            {
                if ( $userData )
                {
                    $userData->setAttribute( $attr, md5( $value ) );
                    $userData->store();
                }
            } break;

            case 'user_id':
            {
                if ( $this->attribute( $attr ) != $value &&
                     $value != 0 )
                {
                    $user = eZUser::fetch( $value );
                    if ( !$user )
                    {
                        return;
                    }
                    eZPersistentObject::setAttribute( $attr, $value );
                }
                else if ( $value == 0 )
                {
                    eZPersistentObject::setAttribute( $attr, $value );
                }
            } break;

            case 'status':
            {
                if ( $this->attribute( $attr ) != $value )
                {
                    switch( $value )
                    {
                        case eZSubscription::StatusConfirmed:
                        {
                            $this->setAttribute( 'confirmed', time() );
                        } break;

                        case eZSubscription::StatusApproved:
                        {
                            $this->setAttribute( 'approved', time() );
                        } break;

                        case eZSubscription::StatusRemovedAdmin:
                        case eZSubscription::StatusRemovedSelf:
                        {
                            $this->setAttribute( 'removed', time() );
                        } break;
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
      Return user subscription data
     */
    function userSubscriptionData()
    {
        return eZUserSubscriptionData::fetch( $this->attribute( 'email' ) );
    }

    /*!
     \static

     Fetch draft of the eZSubscription. If none exist, create one base on the published object

     \param id
    */
    static function fetchDraft( $id, $asObject = true )
    {
        $subscription = eZSubscription::fetch( $id, eZSubscription::VersionStatusDraft, $asObject );
        if ( !$subscription )
        {
            $subscription = eZSubscription::fetch( $id, eZSubscription::VersionStatusPublished, $asObject );
            if ( $subscription )
            {
                $subscription->setAttribute( 'version_status', eZSubscription::VersionStatusDraft );
                $subscription->store();
            }
        }

        if ( !$subscription )
        {
            return false;
        }

        return $subscription;
    }

    /*!
     \static

     Get subscription list count

     \param subscription list ID

     \return eZSubscriptionList count
    */
    static function count( $subscriptionListID, $status = eZSubscription::VersionStatusPublished, $condition = array() )
    {
        $rows = eZPersistentObject::fetchObject( eZSubscription::definition(),
                                                 array(),
                                                 array_merge( $condition,
                                                     array( 'version_status'      => $status,
                                                            'subscriptionlist_id' => $subscriptionListID ) ),
                                                 false,
                                                 false,
                                                 array( array( 'operation' => 'count( id )',
                                                               'name' => 'count' ) ) );
        return $rows['count'];
    }

    /*!
     \static

     Fetch eZSubscription object
     \param id
     \param status
    */
    static function fetch( $id, $status = eZSubscription::VersionStatusPublished, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZSubscription::definition(),
                                                null,
                                                array( 'id' => $id,
                                                       'version_status' => $status ),
                                                $asObject );
    }

    /*!
     Publish eZSubscription.
     1. Sets status to publish
     2. Stores object
     3. Removes draft
    */
    function publish()
    {
        $this->setAttribute( 'version_status', eZSubscription::VersionStatusPublished );
        $this->store();
        $this->removeDraft();
    }

    /*!
     Remove draft of current object
    */
    function removeDraft()
    {
        $subscriptionDraft = eZSubscription::fetchDraft( $this->attribute( 'id' ) );
        $subscriptionDraft->remove();
    }

    /*!
     \static
     Remove all entries of specified ID.

     \param ID
    */
    static function removeAll( $id )
    {
        eZPersistentObject::removeObject( eZSubscription::definition(),
                                          array( 'id' => $id ) );

    }

    /*!
     \static

     Create new eZSubscription object
    */
    static function create( $subscriptionListID,
                            $firstname = '',
                            $name = '',
                            $mobile = '',
                            $email = '',
                            $userID = false )
    {
        if( !isset( $subscriptionListID ) )
        {
            return false;
        }
        
        $rows = array( 'created'             => time(),
                       'creator_id'          => eZUser::currentUserID(),
                       'hash'                => md5( mt_rand() . '-' . mt_rand() ),
                       'mobile'              => $mobile,
                       'email'               => $email,
                       'subscriptionlist_id' => $subscriptionListID,
                       'status'              => eZSubscription::VersionStatusDraft );

        if ( $userID !== false )
        {
            $rows['user_id'] = $userID;

	    
        }
        else
        {
            $userData = eZUserSubscriptionData::fetch( $email );
            if ( !$userData )
            {
                 eZUserSubscriptionData::create( $firstname,
                                                 $name,
                                                 $mobile,
                                                 $email );

                 $subscription = new eZSubscription( $rows );
                 $subscription->store();
	    }	
            else
		{	
			$subscription = new eZSubscription( $rows );
			$subscription->store();
		}

	    return $subscription;
           
	}
}
    /*!
     Fetch by hash
    */
    static function fetchByHash( $hash, $status = eZSubscription::VersionStatusPublished, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZSubscription::definition(),
                                                null,
                                                array( 'hash' => $hash,
                                                       'version_status' => $status ),
                                                $asObject );
    }

    /*!
     Fetch list by user ID
    */
    static function fetchListByUserID( $userID,
                                       $status = eZSubscription::VersionStatusPublished,
                                       $subscribeStatus = eZSubscription::StatusApproved,
                                       $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( eZSubscription::definition(),
                                                    null,
                                                    array( 'user_id' => $userID,
                                                           'status' => $subscribeStatus,
                                                           'version_status' => $status ),
                                                    null,
                                                    null,
                                                    $asObject );
    }

    /*!
     Fetch list by user ID
    */
    static function fetchListByEmail( $email,
                                      $status = eZSubscription::VersionStatusPublished,
                                      $subscribeStatus = eZSubscription::StatusApproved,
                                      $asObject = true )
    {
        $condArray = array( 'email' => $email );
        if ( $status !== false )
        {
            $condArray['version_status'] = $status;
        }
        if ( $subscribeStatus !== false )
        {
            $condArray['status'] = $subscribeStatus;
        }
        return eZPersistentObject::fetchObjectList( eZSubscription::definition(),
                                                    null,
                                                    $condArray,
                                                    null,
                                                    null,
                                                    $asObject );
    }

    /*!
     Fetch by email address
    */
    static function fetchByEmailSubscriptionListID( $email,
                                                    $subscriptionListID,
                                                    $status = eZSubscription::VersionStatusPublished,
                                                    $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZSubscription::definition(),
                                                null,
                                                array( 'email' => $email,
                                                       'subscriptionlist_id' => $subscriptionListID,
                                                       'version_status' => $status ),
                                                $asObject );
    }

    /*!
     Fetch by user id and subscriptionlist ID
    */
    static function fetchByUserSubscriptionListID( $userID,
                                                   $subscriptionListID,
                                                   $status = eZSubscription::VersionStatusPublished,
                                                   $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZSubscription::definition(),
                                                null,
                                                array( 'user_id' => $userID,
                                                       'subscriptionlist_id' => $subscriptionListID,
                                                       'version_status' => $status ),
                                                $asObject );
    }

    /*!
     Get user object
    */
    function user()
    {
        $user = false;
        if ( $this->attribute( 'user_id' ) != 0 )
        {
            $user = eZUser::fetch( $this->attribute( 'user_id' ) );
        }

        return $user;
    }

    /*!
     Get Creator user object
    */
    function creator()
    {
        return eZUser::fetch( $this->attribute( 'creator_id' ) );
    }

    /*!
     \static

     Fetch list of subcriptions

     \param offset
     \param limit
     \param as object

     \return list of subscription items
    */
    static function fetchList( $offset = 0, $limit = 10, $asObject = true, $status = eZSubscription::VersionStatusPublished )
    {
        return eZPersistentObject::fetchObjectList( eZSubscription::definition(),
                                                    null,
                                                    array( 'version_status' => $status ),
                                                    null,
                                                    array( 'limit' => $limit,
                                                           'offset' => $offset ),
                                                    $asObject );
    }

    /*!
     \static

     Fetch list by eZSubscriptionListID

     \param eZSubscriptionListID
     \param $offset
     \param $limit
     \param $asObject

     \return eZSubscription List
    */
   static function fetchListBySubscriptionListID( $subscriptionListID,
                                                  $condArray = array(),
                                                  $offset = 0,
                                                  $limit = 10,
                                                  $asObject = true )
    {    
        return eZPersistentObject::fetchObjectList( eZSubscription::definition(),
                                                    null,
                                                    array_merge( array( 'version_status' => eZSubscription::VersionStatusPublished,
                                                                        'subscriptionlist_id' => $subscriptionListID ),
                                                                 $condArray ),
                                                    null,
                                                    array( 'limit' => $limit,
                                                           'offset' => $offset ),
                                                    $asObject );
    }

    #/*!
    # \static
    # Get VIP name map
    #*/
    #static function vipNameMap()
    #{
    #    return array( eZSubscription::VIPNone     => ezpI18n::tr( 'eznewsletter/vip_type', 'Not set' ),
    #                  eZSubscription::VIPSilver   => ezpI18n::tr( 'eznewsletter/vip_type', 'Silver' ),
    #                  eZSubscription::VIPGold     => ezpI18n::tr( 'eznewsletter/vip_type', 'Gold' ),
    #                  eZSubscription::VIPPlatinum => ezpI18n::tr( 'eznewsletter/vip_type', 'Platinum' ) );
    #}

    /*!
     \static
     Get Status name map
    */
    static function statusNameMap()
    {
        return array( eZSubscription::StatusPending      => ezpI18n::tr( 'eznewsletter/subscription_status', 'Pending' ),
                      eZSubscription::StatusConfirmed    => ezpI18n::tr( 'eznewsletter/subscription_status', 'Confirmed' ),
                      eZSubscription::StatusApproved     => ezpI18n::tr( 'eznewsletter/subscription_status', 'Approved' ),
                      eZSubscription::StatusRemovedSelf  => ezpI18n::tr( 'eznewsletter/subscription_status', 'Removed by self' ),
                      eZSubscription::StatusRemovedAdmin => ezpI18n::tr( 'eznewsletter/subscription_status', 'Removed by admin' ) );
    }
}

?>
