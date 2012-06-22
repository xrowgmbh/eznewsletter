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


$newsletterType = eZNewsletterType::fetch( $newsletterTypeID );

if ( !$newsletterType )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

$userParameters = $Params['UserParameters'];
$offset = isset( $userParameters['offset'] ) ? $userParameters['offset'] : 0;
$limitKey = isset( $userParameters['limit'] ) ? $userParameters['limit'] : '1';
$limitList = array ( '1' => 10,
                     '2' => 25,
                     '3' => 50 );
$limit = $limitList[(string)$limitKey];
$viewParameters = array( 'offset' => $offset );

$tpl = eZNewsletterTemplateWrapper::templateInit();

$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'limitKey', $limitKey );
$tpl->setVariable( 'offset', $offset );
$tpl->setVariable( 'view_parameters', $viewParameters );

$tpl->setVariable( 'newsletter_type', $newsletterType );

//unserialize the contentclass_list, which is stored as imploded arrays
$tpl->setVariable( 'contentclass_list', eZNewsletterType::unserializeArray( $newsletterType->attribute( 'contentclass_list' ) ) );

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/newsletter_menu.tpl';
$Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:$extension/list_inprogress.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'eznewsletter/list_inprogress', 'View newsletter type' ) ) );


?>
