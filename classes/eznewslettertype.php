<?php
//
// Definition of eZNewsletterType class
//
// Created on: <30-Nov-2005 10:41:07 oms>
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

/*! \file eznewslettertype.php
*/

/*!
  \class eZNewsletterType eznewslettertype.php
  \brief The class eZNewsletterType does

*/


class eZNewsletterType extends eZPersistentObject
{
    const StatusDraft = 0;
    const StatusPublished = 1;
    
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
                                         'name' => array( 'name' => 'Name',
                                                          'datatype' => 'string',
                                                          'default' => '',
                                                          'required' => true ),
                                         'contentclass_list' => array( 'name' => 'ContentClassList',
                                                                       'datatype' => 'string',
                                                                       'default' => '',
                                                                       'required' => true ),
                                         'description' => array( 'name' => 'Description',
                                                                 'datatype' => 'string',
                                                                 'default' => '',
                                                                 'required' => true ),
                                         'defaultsubscriptionlist_id' => array( 'name' => 'DefaultSubscriptionListID',
                                                                                'datatype' => 'integer',
                                                                                'default' => 0,
                                                                                'required' => true ),
                                         // #DEPRECATED#
                                         'related_object_id_1' => array( 'name' => 'RelatedObjectID1',
                                                                         'datatype' => 'integer',
                                                                         'default' => 0,
                                                                         'required' => true ),
                                         // #DEPRECATED#
                                         'related_object_id_2' => array( 'name' => 'RelatedObjectID2',
                                                                         'datatype' => 'integer',
                                                                         'default' => 0,
                                                                         'required' => true ),
                                         // #DEPRECATED#
                                         'related_object_id_3' => array( 'name' => 'RelatedObjectID3',
                                                                         'datatype' => 'integer',
                                                                         'default' => 0,
                                                                         'required' => true ),
                                         'inbox_id' => array( 'name' => 'InboxID',
                                                              'datatype' => 'integer',
                                                              'default' => 0,
                                                              'required' => false ),
                                         'allowed_designs' => array( 'name' => 'AllowedDesigns',
                                                                            'datatype' => 'string',
                                                                            'default' => 0,
                                                                            'required' => true ),
                                         'article_pool_object_id' => array( 'name' => 'ArticlePool',
                                                                            'datatype' => 'integer',
                                                                            'default' => 0,
                                                                            'required' => true ),
                                         'sender_address' => array( 'name' => 'SendAddress',
                                                                    'datatype' => 'string',
                                                                    'default' => '',
                                                                    'required' => true ),
                                         'status' => array( 'name' => 'Status',
                                                            'datatype' => 'integer',
                                                            'default' => 0,
                                                            'required' => true ),
                                         'created' => array( 'name' => 'Created',
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true ),
                                         'send_date_modifier' => array( 'name' => 'SendDateModifier',
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true ),
                                         'creator_id' => array( 'name' => 'Creator',
                                                                'datatype' => 'integer',
                                                                'default' => 0,
                                                                'required' => true ),
                                         'pretext' => array( 'name' => 'Pretext',
                                                                 'datatype' => 'string',
                                                                 'default' => '',
                                                                 'required' => true ),
                                         'posttext' => array( 'name' => 'Posttext',
                                                                 'datatype' => 'string',
                                                                 'default' => '',
                                                                 'required' => true ),
                                         'personalise' => array( 'name' => 'Personalise',
                                                                'datatype' => 'integer',
                                                                'default' => 1,
                                                                'required' => true ),
                                         'allowed_siteaccesses' => array( 'name' => 'AllowedSiteaccesses',
                                                                          'datatype' => 'string',
                                                                          'default' => '',
                                                                          'required' => false ) ),
                      'keys' => array( 'id', 'status' ),
                      'function_attributes' => array( 'article_pool_object' => 'articlePoolObject',
                                                      'creator' => 'creator',
                                                      'inbox_object' => 'inbox',
                                                      'subscription_list' => 'subscriptionList',
                                                      'subscription_id_list' => 'subscriptionIDList',
                                                      'allowed_designs_array' => 'allowedDesignsArray',
                                                      'related_object_1' => 'relatedObject1',
                                                      'related_object_2' => 'relatedObject2',
                                                      'related_object_3' => 'relatedObject3',
                                                      'send_date_modifier_list' => 'sendDateModifierList',
                                                      'default_subscription_list' => 'defaultSubscriptionList',
                                                      'allowed_siteaccesses_array' => 'allowedSiteaccessesArray' ),
                      'increment_key'   => 'id',
                      'sort'            => array( 'id' => 'asc' ),
                      'class_name'      => 'eZNewsletterType',
                      'name'            => 'eznewslettertype' );
    }

    function sendDateModifierList() 
    {
        $sendDateModifier = $this->attribute( 'send_date_modifier');
        
        $sendModifierList = array( );
        $sendModifierList['days'] = (int) ($sendDateModifier / 86400);
        $sendModifierList['hours'] = (int) (( $sendDateModifier % 86400 ) / 3600);
        $sendModifierList['minutes'] = (int) (( $sendDateModifier % 86400 % 3600 ) / 60);

        return $sendModifierList;
    }

    /*!
     Fetch related object 1, 2 and 3
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
                // #DEPRECATED#
                $retVal = eZContentObject::fetch( $this->attribute( 'related_object_id_' . substr( $attr, -1, 1 ) ) );
            } break;

            case 'inbox_object':
            {
                if( $this->attribute( 'inbox_id' ) ) {
                    $retVal = eZContentObject::fetch( $this->attribute( 'inbox_id' ) );
                }
            } break;

            case 'subscription_id_list':
            {
                $list = eZNewsletterTypeSubscription::fetchList( $this->attribute( 'id' ), false, $this->attribute( 'status' ) );
                if ( $list )
                {
                    $retVal = array();
                    foreach( $list as $subItem )
                    {
                        $retVal[] = $subItem->attribute( 'subscription_id' );
                    }
                }
            } break;

            case 'default_subscription_list':
            {
                $subscriptionListLink = eZSubscriptionList::fetch( $this->attribute( 'defaultsubscriptionlist_id' ) );
                if ( !$retVal )
                {
                    $subscriptionList = $this->attribute( 'subscription_list' );
                    if( 0 < count( $subscriptionList ) )
                    {
                        $subscriptionListLink = $subscriptionList[0];
                    }
                }
                if ( $subscriptionListLink )
                {
                    $retVal = $subscriptionListLink->attribute( 'subscription_object' );
                }
            } break;
           
            default:
            {
                $retVal = eZPersistentObject::attribute( $attr );
            } break;
        }

        return $retVal;
    }

    /*!
     Fetch newsletter list.

     \param $offset ( default = 0 )
     \param $limit ( default = 10 )
     \param $sendStatus ( default = false )
     \param $status ( default = eZNewsletter::StatusPublished )
     \param $asObject ( default = true )

     \return List of newsletter items.
    */
    function fetchNewsletterList( $offset = 0,
                                  $limit = 10,
                                  $sendStatus = false,
                                  $status = eZNewsletter::StatusPublished,
                                  $asObject = true,
                                  $grouped = false,
                                  $recurring = false )
    {
        $condArray = array( 'newslettertype_id' => $this->attribute( 'id' ),
                            'status' => $status );

        if ( $sendStatus !== false )
        {
            $condArray['send_status'] = $sendStatus;
        }
        
        $grouping = $grouped ? array( 'contentobject_id' ) : null;
        
        if ( $recurring == true )
        {
            $condArray['recurrence_type'] = array( array( 'd', 'w', 'm' ) );
        }
    
        if ( ( $offset === -1 ) && ( $limit === -1 ) )
        {
            return eZPersistentObject::fetchObjectList( eZNewsletter::definition(),
                                                        null,
                                                        $condArray,
                                                        null,
                                                        $asObject,
                                                        $grouping );
        }
        else
        {
            return eZPersistentObject::fetchObjectList( eZNewsletter::definition(),
                                                        null,
                                                        $condArray,
                                                        null,
                                                        array( 'length' => $limit,
                                                               'offset' => $offset ),
                                                        $asObject,
                                                        $grouping ); // gouping
        }
    }

    /*!
     Fetch assigned subscription lists
    */
    function &subscriptionList()
    {
        $retVal = eZNewsletterTypeSubscription::fetchList( $this->attribute( 'id' ), false, $this->attribute( 'status' ) );
        if ( !$retVal )
        {
            $retVal = array();
        }
        return $retVal;
    }

    /*!
     Assign specified subscription list to newsletter type

     \param subscription list id
    */
    function assignSubscription( $subscriptionListID )
    {
        eZNewsletterTypeSubscription::add( $this->attribute( 'id' ),
                                           $subscriptionListID,
                                           $this->attribute( 'status' ) );
    }

    /*!
     Remove specified subscription list to newsletter type

     \param subscription list id
    */
    function removeSubscription( $subscriptionListID = false )
    {
        $condArray = array( 'newsletter_id' => $this->attribute( 'id' ),
                            'status' => $this->attribute( 'status' ) );

        if ( $subscriptionListID !== false )
        {
            $condArray['subscription_id'] = $subscriptionListID;
        }
        
        eZNewsletterTypeSubscription::removeByCondition( $condArray );
    }

    /*!
     \return Node id which contain the available articles to be used in the newsletter type.
     */
    function articlePoolObject()
    {
        if ( $this->attribute( 'article_pool_object_id' ) == 0 )
        {
            return false;
        }
        else
        {
            return eZContentObject::fetch( $this->attribute( 'article_pool_object_id' ) );
        }
    }

    /*!
      \a $asObject BOOL, defaults to true, specifies whether the returned result should be an object or a row.
      \return Array of all newsletter types in the system
    */
    static function fetchList( $status = eZNewsletterType::StatusPublished, $asObject = true, $useFilter = false )
    {
	$custom_conds = null;
	if( $useFilter )
	{
	    $currentAccessArray = $GLOBALS['eZCurrentAccess'];
	    $custom_conds = 'AND allowed_siteaccesses LIKE \'%'.$currentAccessArray['name'].'%\'';
	}
        return eZPersistentObject::fetchObjectList( eZNewsletterType::definition(),
                                                    null,
                                                    array( 'status' => $status ),
                                                    null,
                                                    null,
                                                    $asObject,
                                                    false,
                                                    null,
                                                    null,
                                                    $custom_conds );
    }

    /*!
      \a $newsletterTypeID The id of the requested eZNewsletterType object
      \a $asObject BOOL specifies whether to return object or row
      \return An eZNewsletterType in object or row form
    */
    static function fetch( $newsletterTypeID, $status = eZNewsletterType::StatusPublished, $asObject= true )
    {
        return eZPersistentObject::fetchObject( eZNewsletterType::definition(),
                                                null,
                                                array( 'id' => $newsletterTypeID,
                                                       'status' => $status ),
                                                $asObject );
    }

    /*!
      \static
      Fetch draft of eZNewsletterType object. A new object will be created if none exist.

      \param id
    */
    static function fetchDraft( $id, $asObject = true )
    {
        $newsletterType = eZNewsletterType::fetch( $id, eZNewsletterType::StatusDraft, $asObject );
        if ( !$newsletterType )
        {
            $newsletterType = eZNewsletterType::fetch( $id, eZNewsletterType::StatusPublished, $asObject );
            if ( $newsletterType )
            {
                $newsletterType->setAttribute( 'status', eZNewsletterType::StatusDraft );
                $newsletterType->store();
            }
        }

        if ( !$newsletterType )
        {
            return false;
        }
        return $newsletterType;
    }

    /*!
      Publish eZNewsletterType
      1. Sets status to publish
      2. Stores the object
      3. Removes the draft
     */
    function publish()
    {
        $this->setAttribute( 'status', eZNewsletterType::StatusPublished );
        $this->store();

        eZNewsletterTypeSubscription::publish( $this->attribute( 'id' ) );

        $this->removeDraft();
    }

    /*!
      Remove draft of current object.
     */
    function removeDraft()
    {
        $newsletterTypeDraft = eZNewsletterType::fetchDraft( $this->attribute( 'id' ) );
        $newsletterTypeDraft->remove();
    }

    /*!
      \static
      Remove all entries in system of \a id

      \param id
     */
    static function removeAll( $id )
    {
        eZPersistentObject::removeObject( eZNewsletterType::definition(),
                                          array( 'id' => $id ) );
    }

    /*!
       \static
       Create new eZNewsletterType object
     */
    static function create( $name = '', $userID = false )
    {
        if ( $userID === false )
        {
            $userID = eZUser::currentUserID();
        }

        $newsletterType = new eZNewsletterType( array( 'created' => time(),
                                                       'creator_id' => $userID,
                                                       'name' => $name,
                                                       'status' => eZNewsletterType::StatusDraft ) );
        $newsletterType->store();
        return $newsletterType;
    }

    /*!
      Get Creator user object
     */
    function creator()
    {
        $user = false;
        if ( $this->attribute( 'creator_id' ) )
        {
            $user = eZUser::fetch( $this->attribute( 'creator_id' ) );
        }
        return $user;
    }

    /*!
      Verify if current siteaccess is allowed to use this type
     */
    function siteaccessAllowed()
    {
	$currentAccessArray = $GLOBALS['eZCurrentAccess'];
	$allowedSiteaccessesList = $this->attribute( 'allowed_siteaccesses_array' );
	if( in_array( $currentAccessArray['name'], $allowedSiteaccessesList ) || $this->attribute( 'status' ) == eZNewsletterType::StatusDraft )
	{
	    return true;
	}
	return false;
    }

    /*!
      \a $offset Offset from start of dataset.
      \a $limit Number of elements to return in each batch.
      \a $asObject Specifies whether to return datasat as objects or rows.
      \return Array of eZNewsletterType.
     */
    static function fetchByOffset( $offset, $limit, $status = eZNewsletterType::StatusPublished, $asObject = true, $useFilter = false )
    {
	$custom_conds = null;
	if( $useFilter )
	{
	    $currentAccessArray = $GLOBALS['eZCurrentAccess'];
	    $custom_conds = 'AND allowed_siteaccesses LIKE \'%'.$currentAccessArray['name'].'%\'';
	}

        $newsletterTypeList = eZPersistentObject::fetchObjectList( eZNewsletterType::definition(),
                                                                   null,
                                                                   array( 'status' => $status ),
                                                                   array( 'id' => 'ASC' ),
                                                                   array( 'offset' => $offset, 'length' => $limit ),
                                                                   $asObject,
                                                                   false,
                                                                   null,
                                                                   null,
                                                                   $custom_conds );
        return $newsletterTypeList;
    }

    /*!
      \static
      Reads a string, and recreates an array.
     */
    static function unserializeArray( $string )
    {
        $retVal = '';
        if ( strlen( $string ) >= 1 )
        {
            $retVal = explode( eZNewsletterType::FieldSeparationCharacter,
                               $string );
        }
        return $retVal;
    }

    /*!
      \static
      Serializes an arraay into a string
     */
    static function serializeArray( $arr )
    {
        $retVal = '';
        if ( is_array( $arr ) )
        {
            $string = implode( eZNewsletterType::FieldSeparationCharacter,
                               $arr );
            $retVal = $string;
        }
        return $retVal;
    }

    /*!
     Return allowed designs
     #DEPRECATED# ???
     */
    function allowedDesignsArray()
    {
        $allowedDesigns = $this->attribute( 'allowed_designs', true );
        $allowedDesignsArray = explode( eZNewsletterType::FieldSeparationCharacter,
                                        $allowedDesigns );
        return $allowedDesignsArray;
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
