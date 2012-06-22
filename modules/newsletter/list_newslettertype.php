<?php
//
// Created on: <02-Dec-2005 13:09:53 oms>
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

/*! \file list_newslettertype.php
*/

$extension = 'eznewsletter';
$base = eZExtension::baseDirectory();
$baseDir = "$base/$extension/classes/";

$Module = $Params['Module'];
$http = eZHTTPTool::instance();

$userParameters = $Params['UserParameters'];
$offset = isset( $userParameters['offset'] ) ? $userParameters['offset'] : 0;

$limitList = array ( '1' => 10,
                     '2' => 25,
                     '3' => 50 );

$limit = $limitList[(string)(isset( $userParameters['limitkey'] ) ? $userParameters['limitkey'] : '1' )];

$tpl = eZNewsletterTemplateWrapper::templateInit();
$tpl->setVariable( 'module', $Module );

if ( $http->hasPostVariable( 'CreateNewslettertypeButton' ) )
{
    $Module->redirectTo( $Module->functionURI( 'edit_type' ) );
    return;
}
else if ( $http->hasPostVariable( 'RemoveNewslettertypeButton' ) )
{
    $newsletterTypeIDArray = $http->postVariable( 'NewsletterTypeIDArray' );
    $http->setSessionVariable( 'NewsletterTypeIDArray', $newsletterTypeIDArray );
    $newsletters = array();


if (is_array ($newsletterTypeIDArray))
{
    foreach( $newsletterTypeIDArray as $newsletterID )
    {
        $newsletter = eZNewsletterType::fetch( $newsletterID );
        $newsletters[] = $newsletter;
    }
    $tpl->setVariable( 'delete_result', $newsletters );
    $Result = array();
    $Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
    $Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
    $Result['content'] = $tpl->fetch( "design:$extension/confirmremove_newslettertype.tpl" );
    $Result['path'] = array( array( 'url' => false,
                                    'text' => ezpI18n::tr( 'eznewsletter/list_newslettertype', 'Newsletter types' ) ) );
    return;
}
}
else if ( $http->hasPostVariable( 'ConfirmRemoveNewslettertypeButton' ) )
{
    $newsletterTypeIDArray = $http->sessionVariable( 'NewsletterTypeIDArray' );

    $db = eZDB::instance();
    $db->begin();
    foreach ( $newsletterTypeIDArray as $newsletterID )
    {
        eZNewsletterType::removeAll( $newsletterID );
    }
    $db->commit();
}
else if ( $http->hasPostVariable( 'ConfirmRemoveNewsletterTypeListButton' ) )
{
    $newsletterTypeListIDArray = $http->sessionVariable( 'NewsletterTypeListIDArray' );
    $redirectAfterDelete       = $http->postVariable( 'RedirectAfterDelete' );
    $db = eZDB::instance();
    $db->begin();
    foreach ( $newsletterTypeListIDArray as $newsletterID )
    {
        eZNewsletter::removeAll( $newsletterID );
    }
    $db->commit();
    return $Module->redirectTo( $redirectAfterDelete );
}
else if ( $http->hasPostVariable( 'ConfirmRemoveAllNewsletterTypeListButton' ) )
{
    $redirectAfterDelete       = $http->postVariable( 'RedirectAfterDelete' );
    $NewsletterTypeID          = $http->postVariable( 'NewsletterTypeID' );
    $db = eZDB::instance();
    $deleteAllNewsletter = 'DELETE eznewsletter 
                            FROM eznewsletter, eznewslettertype 
                            WHERE eznewslettertype.id = eznewsletter.newslettertype_id 
                                  AND '.$NewsletterTypeID.' = eznewsletter.newslettertype_id ';
    $db->query($deleteAllNewsletter);
    return $Module->redirectTo( $redirectAfterDelete );
}
else if ( $http->hasPostVariable( 'RemoveNewsletterButton' ) )
{
    $newsletterIDArray = $http->postVariable( 'NewsletterList' );
    $http->setSessionVariable( 'NewsletterList', $newsletterIDArray );
    $newsletters = array();

    foreach( $newsletterIDArray as $id )
    {
        $newsletter = eZNewsletter::fetch( $id );
        $newsletters[] = $newsletter;
    }

    $tpl->setVariable( 'delete_result', $newsletters );
    $Result = array();
    $Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
    $Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
    $Result['content'] = $tpl->fetch( "design:$extension/confirmremove_newsletter.tpl" );
    $Result['path'] = array( array( 'url' => false,
                                    'text' => ezpI18n::tr( 'eznewsletter/list_newslettertype', 'Newsletter types' ) ) );

    return;
}
else if ( $http->hasPostVariable( 'ConfirmRemoveNewsletterButton' ) )
{
    $newsletterIDArray = $http->sessionVariable( 'NewsletterList' );
    $db = eZDB::instance();
    $db->begin();
    foreach ($newsletterIDArray as $id )
    {
        eZNewsletter::removeAll( $id );
    }
    $db->commit();
    return $Module->redirectToView( 'list_type' );
}


$viewParameters = array( 'offset' => $offset,
                         'limitkey' => ( isset( $userParameters['limitkey'] ) ? $userParameters['limitkey'] : 1 ) );

$newsletterTypeArray = eZNewsletterType::fetchByOffset( $offset, $limit, eZNewsletterType::StatusPublished, true, true );

$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'newsletter_type_array', $newsletterTypeArray );

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:$extension/list_newsletter_type.tpl" );
$Result['path'] = array( array( 'url' => false,
                               'text' => ezpI18n::tr( 'eznewsletter/list_newslettertype', 'Newsletter types' ) ) );
?>
