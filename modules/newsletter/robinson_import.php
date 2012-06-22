<?php
//
// Created on: <18-Jan-2006 18:25:13 hovik>
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

/*! \file subscription_import.php
*/

$Module = $Params['Module'];

$http = eZHTTPTool::instance();


if ( $http->hasPostVariable( 'CancelButton' ) )
{
    if ( $http->hasSessionVariable( 'CSVData' ) )
    {
        $http->removeSessionVariable( 'CSVData' );
    }
    return $Module->redirectToView( 'robinson_show', array() );
}
else if ( $http->hasPostVariable( 'ImportButton' ) )
{

    if ( !$http->hasPostVariable( 'import_list' ) || 
       ( !$http->hasPostVariable( 'import_options' ) ) || 
       ( !$http->hasPostVariable( 'import_datatype' ) ) ) 
    {
           $warning = 'Please select a all required options!';
    }
    else
    {
    
    if ( $http->hasSessionVariable( 'CSVData' ) )
    {
        $data = $http->sessionVariable( 'CSVData' );

    //check if correct mapping is set
        $field_set = false;
        foreach( array_keys( $data ) as $label )
        {
            if ( $http->postVariable( 'import_datatype' ) == eZRobinsonListEntry::MOBILE )
        {
        $value = "mobile";
        }
        else if ( $http->postVariable( 'import_datatype' ) == eZRobinsonListEntry::EMAIL )
        {
        $value = "email";
        }
         
        $mapName = 'LabelMap_' . $label;
            if ( $http->hasPostVariable( $mapName ) )
            {
               if ( $http->postVariable( $mapName ) === $value )
               {
                   $field_set = true;
               }
           }
        }       
       
        //output error and return
        if ( $field_set == false ) {
           $warning = 'Please set a field mapping!';
        } 
        else 
        {
            $labelMap = array();
            foreach( array_keys( $data ) as $label )
            {
            $mapName = 'LabelMap_' . $label;
            if ( $http->hasPostVariable( $mapName ) )
            {
                    if ( $http->postVariable( $mapName ) != '0' )
                    {
                    $labelMap[$http->postVariable( $mapName )] = $label;
                    }
            }
            }
        
        if ( $http->hasPostVariable( 'RowNum' ) )
        {
        //call import function in class
        eZRobinsonListEntry::importData($data, $labelMap, $http->postVariable( 'RowNum' ), $http->postVariable( 'import_list' ), $http->postVariable( 'import_datatype' ), $http->postVariable( 'import_options' ) );
        }
            $http->removeSessionVariable( 'CSVData' );

        return $Module->redirectToView( 'robinson_show', array() );
    }
    }
    
    } // options not set
}

$data = array();
if ( eZHTTPFile::canFetch( 'UploadCSVFile' ) )
{

    $binaryFile = eZHTTPFile::fetch( 'UploadCSVFile' );
    $parser = new eZCSVParser( $binaryFile->attribute( 'filename' ), $http->hasPostVariable( 'FirstRowLabel' ) ? true : false );
    $data = $parser->data();
    $http->setSessionVariable( 'CSVData', $data );
}
else if ( $http->hasSessionVariable( 'CSVData' ) )
{
    $data = $http->sessionVariable( 'CSVData' );
}

$tpl = eZNewsletterTemplateWrapper::templateInit();
$tpl->setVariable( 'data', $data );
if ( isset($warning) ) {
    $tpl->setVariable( 'warning', $warning );
}
$tpl->setVariable( 'global_map', eZRobinsonListEntry::globalNameMap() );
$tpl->setVariable( 'type_map', eZRobinsonListEntry::typeNameMap() );
$tpl->setVariable( 'import_map', eZRobinsonListEntry::importNameMap() );

if ( $http->hasPostVariable( 'import_list' ) ) { $tpl->setVariable( 'import_list', $http->postVariable( 'import_list' ) ); }
if ( $http->hasPostVariable( 'import_options' ) ) { $tpl->setVariable( 'import_options', $http->postVariable( 'import_options' ) ); }
if ( $http->hasPostVariable( 'import_datatype' ) ) { $tpl->setVariable( 'import_datatype', $http->postVariable( 'import_datatype' ) ); }
if ( $http->hasPostVariable( 'RowNum' ) ) { $tpl->setVariable( 'RowNum', $http->postVariable( 'RowNum' ) ); }

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/robinson_menu.tpl';
$Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:eznewsletter/robinson_import.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'eznewsletter/robinson_import', 'Opt-out list' ) ) );

?>
