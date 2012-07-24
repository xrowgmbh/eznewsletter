<?php
//
// Definition of eZNewsletterReadType class
//
// Created on: <11-Jan-2006 01:09:53 hovik>
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

/*! \file eznewsletterreadtype.php
*/

/*!
  \class eZNewsletterReadType eznewsletterreadtype.php
  \brief The class eZNewsletterReadType does

*/

class eZNewsletterReadType extends eZWorkflowEventType
{
    const EventName = "eznewsletterread";   
    
    function eZNewsletterReadType()
    {
        $this->eZWorkflowEventType( eZNewsletterReadType::EventName, ezpI18n::tr( 'eznewsletter/workflow/event', "Newsletter read" ) );
        $this->setTriggerTypes( array( 'content' => array( 'read' => array( 'before' ) ) ) );
    }

    function execute( $process, $event )
    {
        $user = eZUser::currentUser();
        if ( $user->isLoggedIn() )
        {
            return eZWorkflowType::STATUS_ACCEPTED;
        }
        $http = eZHTTPTool::instance();

        // Get current content object ID.
        $parameters = $process->attribute( 'parameter_list' );
        $nodeID = $parameters['node_id'];
        $node = eZContentObjectTreeNode::fetch( $nodeID );
        if ( !$node )
        {
            return eZWorkflowType::STATUS_REJECTED;
        }
        $objectID = $node->attribute( 'contentobject_id' );

        // Get newsletter hash
        $uri = $GLOBALS['eZRequestedURI'];
        $userParameters = $uri->userParameters();
        $hash = isset( $userParameters['hash'] ) ? $userParameters['hash'] : false;
        $sendItem = eZSendNewsletterItem::fetchByHash( $hash );

        if ( $http->hasSessionVariable( 'NewsletterNodeIDArray' ) )
        {
            $globalNodeIDList = $http->sessionVariable( 'NewsletterNodeIDArray' );

            if ( in_array( $nodeID, $http->sessionVariable( 'NewsletterNodeIDArray' ) ) )
            {
                $sendID = $http->sessionVariable( 'NewletterNodeMap_' . $nodeID );
                $sendItem = eZSendNewsletterItem::fetch( $sendID );
                $sendItem->addObjectRead( $objectID );

                return eZWorkflowType::STATUS_ACCEPTED;
            }
        }

        // Get send item, and check that is contains the object id.
        if ( !$sendItem )
        {
            return eZWorkflowType::STATUS_REJECTED;
        }
        $sendItemIDList = $sendItem->attribute( 'newsletter_related_object_list' );

        if ( !$sendItemIDList ||
             !in_array( $objectID, $sendItemIDList ) )
        {
            return eZWorkflowType::STATUS_REJECTED;
        }

        $sendNodeIDArray = array();
        // Set session variables
        foreach( $sendItemIDList as $sendObjectID )
        {
            $sendObject = eZContentObject::fetch( $sendObjectID );
            if ( $sendObject )
            {
                foreach( $sendObject->assignedNodes( false ) as $nodeArray )
                {
                    $http->setSessionVariable( 'NewletterNodeMap_' . $nodeArray['node_id'], $sendItem->attribute( 'id' ) );
                    $sendNodeIDArray[] = $nodeArray['node_id'];
                }
            }
        }
        $globalNodeIDList = array_unique( array_merge( $globalNodeIDList,
                                                       $sendNodeIDArray ) );
        $http->setSessionVariable( 'NewsletterNodeIDArray', $globalNodeIDList );

        // Add object read
        $sendItem->addObjectRead( $objectID );

        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( eZNewsletterReadType::EventName,
                                        "eznewsletterreadtype"
                                      );

?>
