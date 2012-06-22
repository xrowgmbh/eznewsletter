<?php
//
// Created on: <06-Dec-2005 10:18:58 oms>
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

/*! \file view_newslettertype.php
*/

$extension = 'eznewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";


$http = eZHTTPTool::instance();
$newsletterTypeID = $Params['NewsletterTypeID'];
$Module = $Params['Module'];

$tpl = eZNewsletterTemplateWrapper::templateInit();
$tpl->setVariable( 'module', $Module );
$newslettertypeID = null;

$newsletterType = eZNewsletterType::fetch( $newsletterTypeID );

if ( !$newsletterType )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

if ( !$newsletterType->siteaccessAllowed() )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

if ( $http->hasPostVariable( 'RemoveNewsletterTypeListButton' ) )
{
    $newsletterTypeListIDArray = $http->postVariable( 'NewsletterTypeListIDArray' );
    $http->setSessionVariable( 'NewsletterTypeListIDArray', $newsletterTypeListIDArray );
    $types = array();

    if( count( $newsletterTypeListIDArray ) > 0 )
    {
        foreach( $newsletterTypeListIDArray as $typeID )
        {   
            $newsletterTypeList = eZNewsletter::fetch( $typeID );
            $types[] = $newsletterTypeList;
        }
    }
    $tpl->setVariable( 'delete_result', $types );
    $Result = array();
    $Result['newsletter_menu'] = 'design:parts/content/bounce_menu.tpl';
    $Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
    $Result['content'] = $tpl->fetch( "design:$extension/confirmremove_newslettertype_list.tpl" );
    $Result['path'] = array( array( 'url' => false,
                                    'text' => ezpI18n::tr( 'eznewsletter/list_newsletterbounce', 'Newsletter types' ) ) );
    return;
}
if ( $http->hasPostVariable( 'RemoveAllNewsletterTypeListButton' ) )
{
    $NewsletterTypeID          = $http->postVariable( 'NewsletterTypeID' );

    $tpl = eZNewsletterTemplateWrapper::templateInit();
    $tpl->setVariable( 'NewsletterTypeID', $NewsletterTypeID );

    $Result = array();
    $Result['newsletter_menu'] = 'design:parts/content/bounce_menu.tpl';
    $Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
    $Result['content'] = $tpl->fetch( "design:$extension/confirmremoveall_newslettertype_list.tpl" );
    $Result['path'] = array( array( 'url' => false,
                                    'text' => ezpI18n::tr( 'eznewsletter/view_newslettertype', 'Newsletter types' ) ) );
    return;
}
if ( $http->hasPostVariable( "CreateNewsletter" ) )
{

    $user = eZUser::currentUser();
    $userID = $user->attribute( 'contentobject_id' );

    // Set redirect URIs if present
    if ( $http->hasPostVariable( 'RedirectURIAfterPublish' ) )
    {
        $http->setSessionVariable( 'RedirectURIAfterPublish', $http->postVariable( 'RedirectURIAfterPublish' ) );
    }
    if ( $http->hasPostVariable( 'RedirectIfDiscarded' ) )
    {
        $http->setSessionVariable( 'RedirectIfDiscarded', $http->postVariable( 'RedirectIfDiscarded' ) );
    }

    $lang = $http->postVariable( 'ContentObjectLanguageCode' );

    $class = eZContentClass::fetch( $http->postVariable( 'ClassID' ) );
    if ( !$class )
    {
        return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
    }

    $parentObject = $newsletterType->attribute( 'article_pool_object' );
    if ( !$parentObject )
    {
        return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
    }
    $parentNode = $parentObject->attribute( 'main_node' );

    $db = eZDB::instance();
    $db->begin();

    $contentObject = $class->instantiate( $userID, $parentObject->attribute( 'section_id' ), false, $lang );

    $mapSettings = eZINI::instance('eznewsletter.ini');

    // Map default values if necessary
    if( 'enabled' == $mapSettings->variable('NewsletterAutomapping', 'autoMapping') ) 
    {
        $dataMap = $contentObject->attribute('data_map');

        $map = $mapSettings->variable('NewsletterAutomapping', $class->attribute('identifier'));
        foreach( $map as $objectAttribute => $typeSource ) {
            // Check if attribute has content
            
            if( 0 < strlen( trim( $newsletterType->attribute($typeSource) ) ) )
            {
                $attribute = $dataMap[$objectAttribute];
                if( 'ezxmltext' === $attribute->DataTypeString  )
                {
                    $inputData  = '<section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/" >';
                    $inputData .= '<paragraph>';                   
                    $inputData .= html_entity_decode( $newsletterType->attribute($typeSource) );
                    $inputData .= '</paragraph>';
                    $inputData .= '</section>';
                    
                    try
                    {
                        $parser = new eZSimplifiedXMLInputParser( $attribute->ContentObjectID );
                        $parser->setParseLineBreaks( true );              
                        $document = $parser->process( $inputData );
                             
                        $input = $document->saveXML();
                        
                        if( $input === false )
                        {
                            throw new Exception();
                        }
                    }
                    catch( Exception $e )
                    {
                       eZDebug::writeError( "Invalid XML input data.", 'view_newslettertype' );                    
                       $input = "";
                    }
                 }
                else
                {
                    $input = $newsletterType->attribute($typeSource);
                }
                $attribute->setAttribute( 'data_text', $input );
                $attribute->store();
            }
        }
        $contentObject->store();
    }

    $nodeAssignment = eZNodeAssignment::create( array( 'contentobject_id' => $contentObject->attribute( 'id' ),
                                                       'contentobject_version' => $contentObject->attribute( 'current_version' ),
                                                       'parent_node' => $parentNode->attribute( 'node_id' ),
                                                       'is_main' => 1 ) );
    $nodeAssignment->store();
    $newsletter = eZNewsletter::create( 'New "' . $class->attribute( 'name' ) . '" newsletter.', $userID, $newsletterType->attribute( 'id' ) );
    $newsletter->setAttribute( 'contentobject_id', $contentObject->attribute( 'id' ) );
    $newsletter->setAttribute( 'contentobject_version', $contentObject->attribute( 'current_version' ) );
    $newsletter->setAttribute( 'design_to_use', strtok( $newsletterType->attribute( 'allowed_designs' ), ',' ) );

    $newsletter->store();

    $db->commit();

    return  $Module->redirectTo( 'content/edit/' . $contentObject->attribute( 'id' ) . '/' . $contentObject->attribute( 'current_version' ) );
}

$userParameters = $Params['UserParameters'];
$offset = isset( $userParameters['offset'] ) ? $userParameters['offset'] : 0;
$limitKey = isset( $userParameters['limit'] ) ? $userParameters['limit'] : '1';
$limitList = array ( '1' => 10,
                     '2' => 25,
                     '3' => 50 );
$limit = $limitList[(string)$limitKey];

$viewParameters = array( 'offset' => $offset,
                         'limitkey' => ( isset( $userParameters['limitkey'] ) ? $userParameters['limitkey'] : 1 ) );

$tpl = eZNewsletterTemplateWrapper::templateInit();


$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'limitKey', $limitKey );
$tpl->setVariable( 'offset', $offset );
$tpl->setVariable( 'view_parameters', $viewParameters );

$tpl->setVariable( 'newsletter_type', $newsletterType );

//unserialize the contentclass_list, which is stored as imploded arrays
$tpl->setVariable( 'contentclass_list', eZNewsletterType::unserializeArray( $newsletterType->attribute( 'contentclass_list' ) ) );

$tpl->setVariable( 'allowed_designs', eZNewsletterType::unserializeArray( $newsletterType->attribute( 'allowed_designs' ) ) );

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:$extension/view_newslettertype.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'eznewsletter/view_newslettertype', 'View newsletter type' ) ) );
?>
