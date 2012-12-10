<?php
//
// Definition of eZNewsletterFunctionCollection class
//
// Created on: <05-Dec-2005 15:37:33 oms>
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
// Licencees holding a valid "eZ publish professional licence" version 2
// may use this file in accordance with the "eZ publish professional licence"
// version 2 Agreement provided with the Software.
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

/*! \file eznewsletterfunctioncollection.php
*/

/*!
  \class eZNewsletterFunctionCollection eznewsletterfunctioncollection.php
  \brief The class eZNewsletterFunctionCollection does

*/


class eZNewsletterFunctionCollection 
{
    /*!
     Constructor
    */
    function __construct()
    {
    }

    function fetchFullVersionString() 
    {
        return array( 'result' => eZNewsletterSysInfo::version() );
    }

    function fetchMajorVersion()
    {
        return array( 'result' => eZNewsletterSysInfo::majorVersion() );
    }

    function fetchMinorVersion()
    {
        return array( 'result' => eZNewsletterSysInfo::minorVersion() );
    }

    function fetchRelease()
    {
        return array( 'result' => eZNewsletterSysInfo::release() );

    }

    function fetchState()
    {
        return array( 'result' => eZNewsletterSysInfo::state() );
    }

    function fetchIsDevelopment()
    {
        return array( 'result' => eZNewsletterSysInfo::developmentVersion() ? true : false );
    }

    function fetchRevision()
    {
        return array( 'result' => eZNewsletterSysInfo::revision() );
    }

    function fetchDatabaseVersion( $withRelease = true )
    {
        return array( 'result' => eZNewsletterSysInfo::databaseVersion( $withRelease ) );
    }

    function fetchDatabaseRelease()
    {
        return array( 'result' => eZNewsletterSysInfo::databaseRelease() );
    }
 
    /*!
      \return The number of eZNewsletterType objects in the system.
     */
    function fetchNewsletterTypeCount( $useFilter )
    {
        $extension = 'eznewsletter';
        $base = eZExtension::baseDirectory();
        $baseDir = "$base/$extension/classes/";
        $customOperation = array( array( 'operation' => 'count( * )',
                                         'name' => 'count' ) );
	$custom_conds = null;
	if( $useFilter )
	{
	    $currentAccessArray = $GLOBALS['eZCurrentAccess'];
	    $custom_conds = 'AND allowed_siteaccesses LIKE \'%'.$currentAccessArray['name'].'%\'';
	}

        $cond = array( 'status' => eZNewsletterType::StatusPublished );

        $rows = eZPersistentObject::fetchObjectList( eZNewsletterType::definition(),
                                                     array(),
                                                     $cond, 
                                                     null, 
                                                     null, 
                                                     false, 
                                                     false,
                                                     $customOperation,
                                                     null,
                                                     $custom_conds );

        return array( 'result' => $rows[0]['count'] );
    }

    function listSubscriptions( $offset, $count, $useFilter )
    {
        $extension = 'eznewsletter';
        $base = eZExtension::baseDirectory();
        $baseDir = "$base/$extension/classes/";
        return array( 'result' => eZSubscriptionList::fetchList( $offset, $count, $useFilter ) );
    }

    function userDataByHash( $hash )
    {
        return array( 'result' => eZUserSubscriptionData::fetchByHash( $hash ) );
    }

    function subscriptionArrayByUserID( $userid )
    {
        return array( 'result' => eZSubscriptionList::fetchSubscribeListByUserID( $userid ) );
    }

    function subscriptionArrayByEmail( $email )
    {
		
        //return array( 'result' => eZSubscriptionList::fetchSubscribeListByUserID( $email ) );
    	  include_once( eZExtension::baseDirectory() . '/eznewsletter/classes/ezsubscriptionlist.php' );
	  return array( 'result' => eZSubscriptionList::fetchSubscribeListByEmail( $email ) );
    }

    /*!
     Fetch subscription a user is subscribed to, base on user_id
     should be named to "subscriptionArrayByUserID" but this is taken
    */
    function activeSubscriptionsByUserID( $userID )
    {
        return array( 'result' => eZSubscription::fetchListByUserID( $userID ) );
    }
 
    /*!
     Fetch subscription a user is subscribed to, base on email
     should be named to "subscriptionArrayByEmail" but this is taken
    */
    function activeSubscriptionsByEmail( $email )
    {
        return array( 'result' => eZSubscription::fetchListByEmail( $email ) );
    }

    function subscriptionByID( $id )
    {
        return array( 'result' => eZSubscription::fetch( $id ) );
    }

    /*!
      \return eZNewsletterType list.
     */
    function fetchNewsletterTypeList( $useFilter )
    {
        $extension = 'eznewsletter';
        $base = eZExtension::baseDirectory();
        $baseDir = "$base/$extension/classes/";

        return array( 'result' => eZNewsletterType::fetchList( eZNewsletterType::StatusPublished, true, $useFilter ) );
    }

    /*!
      \return The number of eZSubscriptionList objects in the system.
     */
    function fetchSubscriptionListCount( $useFilter )
    {
        return array( 'result' => eZSubscriptionList::countAll( eZSubscriptionList::StatusPublished, $useFilter ) );
    }

    /*!
      \return The number of eZNewsletter objects in the system.
    */
    function fetchNewsletterCount( $type = false )
    {

        $condArray = array( 'status' => eZNewsletter::StatusPublished );
        if ( $type !== false )
        {
            $condArray['newslettertype_id'] = $type;
        }

        $customOperation = array( array( 'operation' => 'count( * )',
                                         'name' => 'count' ) );
        $rows = eZPersistentObject::fetchObjectList( eZNewsletter::definition(),
                                                     array(),
                                                     $condArray, null, null, false, false,
                                                     $customOperation );
        return array( 'result' => $rows[0]['count'] );
    }

    /*!
     Fetch existing newsletter

     \param newsletter ID
    */
    function fetchNewsletter( $id, $status = false )
    {
        if ( $status == false )
        {
            $result = eZNewsletter::fetchDraft( $id );
        }
        else
        {
            $result = eZNewsletter::fetch( $id );
        }

        return array( 'result' => $result );
    }

    /*!
     Fetch newsletter by hash

     \param newsletter hash
    */
    function fetchNewsletterByHash( $hash )
    {
        $result = eZNewsletter::fetchByHash( $hash );

        return array( 'result' => $result );
    }

    function fetchNewsletterByObject( $contentObjectID, $contentObjectVersion, $published = false )
    {
        if ( !$published )
        {
                $newsletter = eZNewsletter::fetchByContentObject( $contentObjectID, $contentObjectVersion, eZNewsletter::StatusDraft );
        }
        else
        {
                $newsletter = eZNewsletter::fetchByContentObject( $contentObjectID, $contentObjectVersion, eZNewsletter::StatusPublished );
        }

        if ( !$newsletter )
        {
            $newsletter = eZNewsletter::fetchByContentObject( $contentObjectID, $contentObjectVersion, eZNewsletter::StatusPublished );
            if ( $newsletter )
            {
                $newsletter->setAttribute( 'status', eZNewsletter::StatusDraft );
                $newsletter->store();
            }
        }

        if ( !$newsletter )
        {
            if ( !$newsletter )
            {
                $newsletter = eZNewsletter::fetchByContentObject( $contentObjectID, $contentObjectVersion - 1, false );
            }

            if ( !$newsletter )
            {
                $newsletter = eZNewsletter::fetchByContentObject( $contentObjectID, false, false );
            }

            if ( $newsletter )
            {
                $newsletter->setAttribute( 'contentobject_version', $contentObjectVersion );
                $newsletter->setAttribute( 'status', eZNewsletter::StatusDraft );
                $newsletter->store();
            }
        }

        return array( 'result' => $newsletter );
    }

    /*!
     Get newsletter read statistics

     \return array( 'num_sent' => <total emails send>,
                    'num_read' => <number of email resulted in object read>,
                    'object_list' => array( <object_id_1> => <num_read>,
                                            <object_id_2> => <num_read>,
                                            ... ) )
    */
    function fetchNewsletterReadStat( $newsletterID )
    {
        $newsletter = eZNewsletter::fetch( $newsletterID );
        if ( !$newsletter )
        {
            return array();
        }

        $db = eZDB::instance();

        // Get total send
        $totalSend = eZSendNewsletterItem::sendCount( $newsletterID );

        // Get total read
        $totalRead = eZSendNewsletterItem::readCount( $newsletterID );

        $objectStat = array();
        foreach( $newsletter->attribute( 'object_relation_id_list' ) as $objectID )
        {
            $objectReadSQL = 'SELECT count(*) AS count
                              FROM ezsendnewsletteritem
                              WHERE newsletter_id = \'' . $db->escapeString( $newsletterID ) . '\' AND
                                    send_status = \'' . eZSendNewsletterItem::SendStatusSent . '\' AND
                                    object_read_ids like \'%/' . $db->escapeString( $objectID ) . '/%\'';
            $objectReadResult = $db->arrayQuery( $objectReadSQL );
            $objectStat[(string)$objectID] = $objectReadResult[0]['count'];
        }

        return array( 'result' => array( 'num_sent' => $totalSend,
                                         'num_read' => $totalRead,
                                         'object_list' => $objectStat ) );
    }

    /*!
      \return The number of eZBounce objects in the system.
    */
    function fetchNewsletterBounceCount()
    {
        return array( 'result' => eZBounce::count( eZBounce::definition() ) );
    }
    
	/*!
      \return The number of eZBounce objects in the system.
    */
    function fetchNewsletterBounceCountGroupedByAddress()
    {
    	return array( 'result' => eZBounce::count( eZBounce::definition(), null, "DISTINCT ADDRESS" ) );
    }

    /*!
      \return The number of eZSendNewsletterItem objects in the system.
    */
    function fetchNewsletterOnHoldCount( $status )
    {
        return array( 'result' => eZSendNewsletterItem::countAll( $status ) );
    }

    /*!
     Get object statistics

     \return array( 'num_sent' => <total emails send>,
                    'num_read' => <number of email resulted in object read>,
                    'newsletter_list' => array( <newsletter object 1>,
                                                <newsletter object 2>,
                                                ... ) )
    */
    function fetchObjectStat( $contentObjectID )
    {
        $db = eZDB::instance();

        // Get total send
        $totalCountSQL = 'SELECT count(*) AS count
                          FROM ezsendnewsletteritem item, eznewsletter newsletter
                          WHERE newsletter.object_relations like \'%/' . $db->escapeString( $contentObjectID ) . '/%\' AND
                                item.send_status = \'' . eZSendNewsletterItem::SendStatusSent . '\' AND
                                newsletter.status = \'' . eZNewsletter::StatusPublished . '\' AND
                                item.newsletter_id = newsletter.id';
        $totalCountResult = $db->arrayQuery( $totalCountSQL );
        $totalSend = $totalCountResult[0]['count'];

        // Get total read
        /* With relation check
        $totalReadSQL = 'SELECT count(*) AS count
                         FROM ezsendnewsletteritem item, eznewsletter newsletter
                         WHERE newsletter.object_relations like \'%/' . $db->escapeString( $contentObjectID ) . '/%\' AND
                               newsletter.status = \'' . eZNewsletter::StatusPublished . '\' AND
                               item.send_status = \'' . eZSendNewsletterItem::SendStatusSent . '\' AND
                               item.object_read_ids like \'%/' . $db->escapeString( $contentObjectID ) . '/%\' AND
                               item.newsletter_id = newsletter.id';
        */
        /* We ignore the relation */
        $totalReadSQL = 'SELECT count(*) AS count
                         FROM ezsendnewsletteritem item, eznewsletter newsletter
                         WHERE item.send_status = \'' . eZSendNewsletterItem::SendStatusSent . '\' AND
                               item.object_read_ids like \'%/' . $db->escapeString( $contentObjectID ) . '/%\' AND
                               item.newsletter_id = newsletter.id';

        $totalReadResult = $db->arrayQuery( $totalReadSQL );
        $totalRead = $totalReadResult[0]['count'];

        return array( 'result' => array( 'num_sent' => $totalSend,
                                         'num_read' => $totalRead,
                                         'newsletter_list' => eZNewsletter::fetchListByRelatedContentObject( $contentObjectID ) ) );
    }

    /*!
     Fetch newsletter list by newsletter type

     \param newsletter type ID
     \param offset ( default 0 )
     \param limit ( default 10 )
     \param isSent null - ignore , true - only return sent, false - only return unsend ( default null )

     \return list of newsletters
    */
    function fetchNewsletterListByType( $typeID,
                                        $offset = 0,
                                        $limit = 10,
                                        $isSend = null,
                    $isDraft = null,
                    $grouping = null,
                    $recurring = null )
    {
        $sendStatus = false;
        $draftStatus = eZNewsletterType::StatusPublished;
    
        if ( ( $isDraft === true ) && ( ( $isSend === true ) || ( $isSend === false ) ) )
        {
            $sendStatus = array( array( eZNewsletter::SendStatusNone ) );
            $draftStatus = eZNewsletterType::StatusDraft;
        }
        else
        {       
            if ( $isSend === false )
            {
                $sendStatus = array( array( eZNewsletter::SendStatusNone,
                                            eZNewsletter::SendStatusBuldingList,
                                            eZNewsletter::SendStatusSending ) );
            }
            else if ( $isSend === true )
            {
                $sendStatus = eZNewsletter::SendStatusFinished;
            }
        }
        
        if ( $recurring === true)
        {
            $sendStatus = array( array( eZNewsletter::SendStatusNone,
                                        eZNewsletter::SendStatusStopped ) );
        
        }

        $result = false;
        $newsletterType = eZNewsletterType::fetch( $typeID );

        if ( $newsletterType )
        {
            $result = $newsletterType->fetchNewsletterList( $offset,
                                                            $limit,
                                                            $sendStatus,
                                                            $draftStatus,
                                                            true,
                                                            $grouping,
                                                            $recurring );
        }
        return array( 'result' => $result );
    }

}

?>
