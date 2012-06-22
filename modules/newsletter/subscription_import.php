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

$subscriptionList = eZSubscriptionList::fetch( $Params['SubscriptionListID'] );

if ( !$subscriptionList )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

$delimiter = $http->hasPostVariable( 'CSVDelimiter' ) ? $http->variable( 'CSVDelimiter' ) : ',';

if ( $http->hasPostVariable( 'CancelButton' ) )
{
    if ( $http->hasSessionVariable( 'CSVData' ) )
    {
        $http->removeSessionVariable( 'CSVData' );
    }
    return $Module->redirectToView( 'subscription_list', array( $subscriptionList->attribute( 'url_alias' ) ) );
}
else if ( $http->hasPostVariable( 'ImportButton' ) )
{
    if ( $http->hasSessionVariable( 'CSVData' ) )
    {
        $data = $http->sessionVariable( 'CSVData' );

        //check if email mapping is set
        $email_set = false;
        foreach( array_keys( $data ) as $label )
        {
            $mapName = 'LabelMap_' . $label;
            if ( $http->hasPostVariable( $mapName ) )
            {
               if ( $http->postVariable( $mapName ) === "email" )
               {
                   $email_set = true;
               }
           }
        }

        //output error and return
        if ( $email_set == false ) {
           $warning = 'Please select a field mapping for the email address!';
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
                $subscriptionListID = $subscriptionList->attribute( 'id' );
                foreach( $http->postVariable( 'RowNum' ) as $rowIndex )
                {
		    if( $data[$labelMap['email']][$rowIndex] != "" )
		    {
                      if ( !eZSubscription::fetchByEmailSubscriptionListID( $data[$labelMap['email']][$rowIndex],
                                                                            $subscriptionListID ) )
                        { 


                          if ( !eZRobinsonListEntry::inList( $data[$labelMap['email']][$rowIndex] ) )
                           {
				{
                                      $subscription = eZSubscription::create( $subscriptionListID,
                                                                              $data[$labelMap['firstname']][$rowIndex],
                                                                              $data[$labelMap['name']][$rowIndex],
                                                                              $data[$labelMap['mobile']][$rowIndex],
                                                                              $data[$labelMap['email']][$rowIndex] );
					

			        					$subscription->setAttribute( 'status', eZSubscription::StatusApproved );
                                	 	$subscription->publish();
                        	}

			   }
                       }
		    }
                }
             }

            $http->removeSessionVariable( 'CSVData' );

            return $Module->redirectToView( 'subscription_list', array( $subscriptionList->attribute( 'url_alias' ) ) );
        }
    }
}

$data = array();

if ( eZHTTPFile::canFetch( 'UploadCSVFile' ) )
{
    $binaryFile = eZHTTPFile::fetch( 'UploadCSVFile' );

    $parser = new eZCSVParser( $binaryFile->attribute( 'filename' ), $http->hasPostVariable( 'FirstRowLabel' ) ? true : false, $delimiter );
    $data = $parser->data();
    $http->setSessionVariable( 'CSVData', $data );
}
else if ( $http->hasSessionVariable( 'CSVData' ) )
{
    $data = $http->sessionVariable( 'CSVData' );
}

$tpl = eZNewsletterTemplateWrapper::templateInit();

$tpl->setVariable( 'subscriptionList', $subscriptionList );
$tpl->setVariable( 'data', $data );
$tpl->setVariable( 'CSVDelimiter', $delimiter );

if ( $http->hasPostVariable( 'RowNum' ) ) 
{
    $tpl->setVariable( 'RowNum', $http->postVariable( 'RowNum' ) );
}

if ( isset($warning) ) {
    $tpl->setVariable( 'warning', $warning );
}

$Result = array();
$Result['newsletter_menu'] = 'design:parts/content/robinson_menu.tpl';
$Result['left_menu'] = 'design:parts/content/eznewsletter_menu.tpl';
$Result['content'] = $tpl->fetch( "design:eznewsletter/subscription_import.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'eznewsletter/subscription_import', 'Subscription list' ) ) );

?>
