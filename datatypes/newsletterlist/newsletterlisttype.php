<?php

include_once( 'kernel/classes/ezdatatype.php' );
include_once( 'kernel/common/i18n.php' );

define( 'EZ_DATATYPESTRING_NEWSLETTERLIST', 'newsletterlist' );


class newsletterlistType extends eZDataType
{
    const DATA_TYPE_STRING = 'newsletterlist';
    /*!
     Initializes with a string id and a description.
    */
    function newsletterlistType()
    {
        $this->eZDataType( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', 'newsletterlist', 'Datatype name' ),
                           array( 'serialize_supported' => false ) );
    }
    function storeSession( &$contentObjectAttribute )
    {

        $http = eZHTTPTool::instance();
        $selection = array();
        if ( $http->hasSessionVariable( 'register_subscription' ) or $http->hasSessionVariable( 'unregister_subscription' ) )
        {
            
            if ( $http->hasSessionVariable( 'register_subscription' ) )
                $selection['register'] = $http->sessionVariable( 'register_subscription' );
            else
                $selection['register'] = array();
            if ( $http->hasSessionVariable( 'unregister_subscription' ) )
                $selection['unregister'] = $http->sessionVariable( 'unregister_subscription' );
            else
                $selection['unregister'] = array();
            $selection['show_newsletter_tab'] = true;
            $contentObjectAttribute->setAttribute( 'data_text', serialize( $selection ) );
            
            $http->removeSessionVariable( 'register_subscription' );
            $http->removeSessionVariable( 'unregister_subscription' );
        }
        $contentObjectAttribute->store();
    }
    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        newsletterlistType::storeSession( $contentObjectAttribute );
    }

    /*!
     \reimp
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Fetches the http post var string input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $http = eZHTTPTool::instance();
        $lastdata = unserialize( $contentObjectAttribute->attribute( "data_text" ) );
        $selection = array( 'register' => array(), 'unregister' => array() );
        if ( $http->hasPostVariable( $base . "_newsletterlist_avialable" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $select = $http->postVariable( $base . "_newsletterlist_selection" . $contentObjectAttribute->attribute( "id" ) );

            foreach ( $http->postVariable( $base . "_newsletterlist_avialable" . $contentObjectAttribute->attribute( "id" ) ) as $avialable )
            {
                if ( isset( $select[$avialable] ) )
                    $selection['register'][] = $avialable;
                else
                    $selection['unregister'][] = $avialable;
            }
            if ( $lastdata['show_newsletter_tab']  )
                $selection['show_newsletter_tab'] = true;
            else
                $selection['show_newsletter_tab'] = false;
            $contentObjectAttribute->setAttribute( "data_text", serialize( $selection ) );
            return true;
        }
        return false;
    }

    /*!
     Does nothing since it uses the data_text field in the content object attribute.
     See fetchObjectAttributeHTTPInput for the actual storing.
    */
    function storeObjectAttribute( $attribute )
    {
        $http = eZHTTPTool::instance();

    }
    function onPublish( $attribute, $contentObject, $publishedNodes )
    {

        $http = eZHTTPTool::instance();
        $userID = $attribute->attribute( 'contentobject_id' );
        $data = unserialize( $attribute->attribute( 'data_text' ) );
        $avialable = array_merge( $data['unregister'], $data['register'] );

        foreach ( $avialable as $id )
        {
            $subscription = eZSubscription::fetchByUserSubscriptionListID( $userID, $id );  

            if ( isset( $subscription ) and $subscription->attribute( 'status' ) == eZSubscription::StatusApproved and in_array( $id, $data['unregister'] ) )
            {
                $subscription->unsubscribe();
            }
            elseif( isset( $subscription ) and $subscription->attribute( 'status' ) == eZSubscription::StatusRemovedSelf and in_array( $id, $data['register'] ) )
            {
                $subscription->setAttribute( 'status', eZSubscription::StatusApproved );
                $subscription->sync();
            }
            elseif ( !isset( $subscription ) and in_array( $id, $data['register'] ) )
            {
                $list = eZSubscriptionList::fetch( $id , eZSubscriptionList::StatusPublished, true, true );
                if ( $list )
                    $subscription = $list->registerUser( $userID );
            }
            if ( $subscription )
            {
                $version = eZContentObjectVersion::fetchVersion( $attribute->attribute( 'version' ), $attribute->attribute( 'contentobject_id' ) );
                $user = eZUser::fetch( $attribute->attribute( 'contentobject_id' ) );
                $userData = eZUserSubscriptionData::fetch( $user->attribute( 'email' ) );
                $dm = $version->attribute( 'data_map' );
                if ( !$userData )
                {
                    $userData = eZUserSubscriptionData::create( '', '', '', $user->attribute( 'email' ) );
                }
                if( $user->attribute( 'email' ) )
                {
                    $userData->updateAttribute( 'email', $user->attribute( 'email' ) );
                    $userData->store();
                }
                if( isset( $dm['last_name'] ) )
                {
                    $userData->setAttribute( 'name', $dm['last_name']->attribute( 'data_text' ) );
                    $userData->store();
                }
                if( isset( $dm['first_name'] ) )
                {
                    $userData->setAttribute( 'firstname', $dm['first_name']->attribute( 'data_text' ) );
                    $userData->store();
                }
                if( isset( $dm['mobile'] ) )
                {
                    $userData->setAttribute( 'mobile', $dm['mobile']->attribute( 'data_text' ) );
                    $userData->store();
                }
            }
        }
        $http->removeSessionVariable( 'register_subscription' );
        $http->removeSessionVariable( 'unregister_subscription' );
    }
    /*!
     \reimp
     Simple string insertion is supported.
    */
    function isSimpleStringInsertionSupported()
    {
        return false;
    }

    function storeClassAttribute( $attribute, $version )
    {
    }

    function storeDefinedClassAttribute( $attribute )
    {
    }

    /*!
     \reimp
    */
    function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     \reimp
    */
    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        return true;
    }

    /*!
     Returns the content.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        newsletterlistType::storeSession( $contentObjectAttribute );
        $return =  array();
        $lists = eZSubscriptionList::fetchList();
        $userID = $contentObjectAttribute->attribute( 'contentobject_id' );
        $selection = unserialize( $contentObjectAttribute->attribute( "data_text" ) );

        $subscriptions = eZPersistentObject::fetchObjectList( eZSubscription::definition(),
                                                null,
                                                array( 'user_id' => $userID,
                                                       
                                                       'version_status' => eZSubscription::VersionStatusPublished ),
                                                true );

        foreach ( $lists as $list )
        {
            $attribute_filter = array( array( 'newsletter_group/newsletter_id',
                                        '=',
                                        $list->attribute('url') ) );
            $treeParameters = array( 'OnlyTranslated' => true,
                                 'AttributeFilter' => $attribute_filter,
                                 'ClassFilterType' => 'include',
                                 'ClassFilterArray' => array( 'newsletter_group' ),
                                 'IgnoreVisibility' => false,
                                 'MainNodeOnly' => true );
            
            
            $listitem = array();
            $listitem['list'] = $list;
            if ( in_array( $list->attribute( 'id' ), $selection['register'] ) )
            {
                $listitem['selected'] = true;
            }
            elseif( in_array( $list->attribute( 'id' ), $selection['unregister'] ) )
            {
                $listitem['selected'] = false;
            }
            $nodesList =  eZContentObjectTreeNode::fetch( 2 )->subTree( $treeParameters );
            foreach ( $subscriptions as $subscription )
            {
                
                $listitem['disabled'] = false;
                if ( $subscription->attribute( 'subscriptionlist_id' ) == $list->attribute( 'id' ) )
                {
                    $listitem['subscription'] = $subscription;
                
                    if ( $listitem['subscription']->attribute( 'status' ) == eZSubscription::StatusPending or
                         $listitem['subscription']->attribute( 'status' ) == eZSubscription::StatusConfirmed or
                         $listitem['subscription']->attribute( 'status' ) == eZSubscription::StatusRemovedAdmin )
                        $listitem['disabled'] = true;

                    if ( !array_key_exists( 'selected', $listitem ) )
                    {
                            if ( $listitem['subscription']->attribute( 'status' ) == eZSubscription::StatusApproved )
                                $listitem['selected'] = true;
                            else
                                $listitem['selected'] = false;
                    }
                    break;
                }
            }  
            if ( isset( $nodesList[0] ) and is_object( $nodesList[0] ) )
                $listitem['node'] = $nodesList[0];
            if ( count( $listitem ) > 0 )
                $return['list'][] =  $listitem;
            unset( $listitem );
        }
        if ( $selection['show_newsletter_tab']  )
                $return['show_newsletter_tab'] = true;
        else
                $return['show_newsletter_tab'] = false;
        return $return;
    }

    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        return false;
    }

    /*!
     Returns the content of the string for use as a title
    */
    function title( $contentObjectAttribute, $name = null )
    {
        return false;
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return true;
    }

    /*!
     \reimp
    */
    function isIndexable()
    {
        return false;
    }

    /*!
     \reimp
    */
    function isInformationCollector()
    {
        return false;
    }

    /// \privatesection

}

eZDataType::register( newsletterlistType::DATA_TYPE_STRING, 'newsletterlisttype' );

?>