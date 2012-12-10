<?php
//
// Created on: <08-Dec-2005 10:40:16 hovik>
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
// This source file is part of the eZ publish (tm) Open Source Content
// Management System.
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

/*! \file bounce_search.php
*/

$Module = $Params['Module'];
$extension = 'eznewsletter';
$http = eZHTTPTool::instance();
$tpl = eZNewsletterTemplateWrapper::templateInit();
$tpl->setVariable( 'module', $Module );
if ( $http->hasPostVariable( 'RemoveBounceButton' ) )
{

	if (is_array ( $http->postVariable( 'BounceIDArray' )))
	{       
		
	        $bounceIDArray = $http->postVariable( 'BounceIDArray' );
	        $http->setSessionVariable( 'BounceIDArray', $bounceIDArray );
	        $bounces = array();
	
	        if( count( $bounceIDArray ) > 0 )
	        {
	            foreach( $bounceIDArray as $bounceID )
	            {
	                $bounce = eZBounce::fetch( $bounceID );
	                $bounces[] = $bounce;
	            }
	        }
	        
	        $tpl->setVariable( 'delete_result', $bounces );
	        $Result = array();
	        $Result['newsletter_menu'] = 'design:parts/content/bounce_menu.tpl';
	        $Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
	        $Result['content'] = $tpl->fetch( "design:$extension/confirmremove_bounce_search.tpl" );
	        $Result['path'] = array( array( 'url' => false,
	                                        'text' => ezpI18n::tr( 'eznewsletter/bounce_search', 'Bounce Search' ) ) );
	        return;
	}
}
if ( $http->hasPostVariable( 'searchString' ) && trim( $http->postVariable( 'searchString' ) ) != ""   )
{
    $search = trim( strtolower( $http->postVariable( 'searchString' ) ) );

    $db = eZDB::instance();
    $searchSQL = "SELECT enl.name, ebd.address, ebd.id FROM ez_bouncedata ebd, eznewsletter enl, ezsendnewsletteritem ensi 
                    WHERE ebd.newslettersenditem_id = ensi.id
                        AND ensi.newsletter_id = enl.id
                        AND ( 
                            LOWER( enl.name ) LIKE '%".$db->escapeString( $search )."%' 
                            OR LOWER( ebd.address ) LIKE '%".$db->escapeString( $search )."%'
                        )";

    $bounceSearch = $db->arrayQuery( $searchSQL );
    $tpl->setVariable( 'bounceSearch', $bounceSearch );
    $tpl->setVariable( 'searchString', $http->postVariable( 'searchString' ) );
}
else
{
    $tpl->setVariable( 'searchString', '' );
    $tpl->setVariable( 'subscriberSearch', array() );
}

$Result = array();
$Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:eznewsletter/bounce_search.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'eznewsletter/bounce_search', 'Search Bounce' ) ) );

?>
