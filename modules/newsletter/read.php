<?php
//
// Created on: <22-Mar-2007 15:00:00 tos>
//
// Copyright (C) 1999-2006 eZ systems as. All rights reserved.
//
// This source file is part of the eZ publish (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE included in
// the packaging of this file.
//
// Licencees holding a valid "eZ publish professional licence" version 2
// may use this file in accordance with the "eZ publish professional licence"
// version 2 Agreement provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "eZ publish professional licence" version 2 is available at
// http://ez.no/ez_publish/licences/professional/ and in the file
// PROFESSIONAL_LICENCE included in the packaging of this file.
// For pricing of this licence please contact us via e-mail to licence@ez.no.
// Further contact information is available at http://ez.no/company/contact/.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact licence@ez.no if any conditions of this licencing isn't clear to
// you.
//

$Module = $Params['Module'];
$http = eZHTTPTool::instance();

$sendItemHash = $Params['sendItemHash'];
$objectID = $Params['objectID'];
$newsletterNodeID = $Params['newsletterNodeID'];

$newsletterINI = eZINI::instance( 'eznewsletter.ini' );

// Fetch newsletter and send item
$sendItem = eZSendNewsletterItem::fetchByHash( $sendItemHash );

if( !$sendItem )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$newsletter = $sendItem->attribute( 'newsletter' );
if( !$newsletter )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$newsletterObjectID = $newsletter->attribute( 'contentobject_id' );

if( !$newsletterObjectID )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}
// Count newsletter read
$sendItem->addObjectRead( $newsletterObjectID );

// Get send item, and check that is contains the object id.
if ( !$sendItem )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

// Check if object has been send out with the newsletter
$sendItemIDList = $sendItem->attribute( 'newsletter_related_object_list' );
if( 'enabled' == $newsletterINI->variable( 'NewsletterReadcount', 'confidentRedirect' ) ||
    ( is_array( $sendItemIDList ) &&
    in_array( $objectID, $sendItemIDList ) ) )
{
    // Count object read
    $sendItem->addObjectRead( $objectID );
}

// Redirection logic
if( is_numeric( $newsletterNodeID ) )
{
    // We have a specific node id - verify
    $newsletterNode = eZContentObjectTreeNode::fetch( $newsletterNodeID );
}
else
{
    $newsletterNode = false;
}

if( !is_object( $newsletterNode ) )
{
    // We have no specific node id - use main node
    $newsletterObject = eZContentObject::fetch( $newsletterObjectID );
    if( is_object( $newsletterObject ) )
    {
        $newsletterNode = eZContentObjectTreeNode::fetch( $newsletterObject->attribute( 'main_node_id' ) );
    }   
}

if( !is_object( $newsletterNode ) )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

if ( 'enabled' != $newsletterINI->variable( 'NewsletterReadcount', 'confidentRedirect' ) )
{
    $redirectURI = $newsletterNode->attribute( 'url_alias' );

    // We have a valid object ID append
    if( $objectID &&
        is_object( eZContentObject::fetch( $objectID ) ) )
    {
        $redirectURI = $redirectURI . '/(article)/' . $objectID;
    }
}
else 
{
    $contentObject = eZContentObject::fetch( $objectID );
    if( $contentObject )
    {
        $objectMainNode = $contentObject->mainNode();
        $redirectURI = $objectMainNode->attribute( 'url_alias' );
    }
}

if( !isset( $redirectURI ) )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

return $Module->redirectTo( $redirectURI );
?>
