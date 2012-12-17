<?php
//
// Definition of eZNewsletter class
//
// Created on: <09-Nov-2005 16:00:00 oms>
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

/*! \file eznewsletter.php
 */

/*!
  \class eZNewsletter eznewsletter.php
  \brief The class eZNewsletter does

*/


class eZNewsletter extends eZPersistentObject
{
    const StatusDraft = 0;
    const StatusPublished = 1;
    
    const SendStatusNone = 0;
    const SendStatusBuldingList = 1;
    const SendStatusSending = 2;
    const SendStatusFinished = 3;
    const SendStatusStopped = 4;
    
    const OutputFormatText = 0;
    const OutputFormatHTML = 1;
    const OutputFormatExternalHTML = 2;
    const OutputFormatSMS = 3;
    
    const BounceLimit = 2;
    
    const RecurrenceDaily = 'd';
    const RecurrenceWeekly = 'w';
    const RecurrenceMonthly = 'm';
    
    const MaxSubscriptionFetchLimit = 10000;

    /*!
      Constructor
    */
    function __construct( $row )
    {
        parent::__construct( $row );    
    }

    static function definition()
    {
        return array( 'fields' => array( 'id' => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         'name' => array( 'name' => 'Name',
                                                          'datatype' => 'string',
                                                          'default' => '',
                                                          'required' => true ),
                                         'hash' => array( 'name' => 'Hash',
                                                          'datatype' => 'string',
                                                          'default' => '',
                                                          'required' => true ),
                                         'contentobject_id' => array( 'name' => 'ContentObjectID',
                                                                      'datatype' => 'integer',
                                                                      'default' => '0',
                                                                      'required' => true ),
                                         'contentobject_version' => array( 'name' => 'ContentObjectVersion',
                                                                      'datatype' => 'integer',
                                                                      'default' => '0',
                                                                      'required' => true ),
                                         'design_to_use' => array( 'name' => 'DesignToUse',
                                                                     'datatype' => 'string',
                                                                     'default' => '',
                                                                     'required' => true ),
                                         'send_date' => array( 'name' => 'SendDate',
                                                               'datatype' => 'integer',
                                                               'default' => 0,
                                                               'required' => true ),
                                         'send_status' => array( 'name' => 'SendStatus',
                                                                 'datatype' => 'integer',
                                                                 'default' => 0,
                                                                 'required' => true ),
                                         'newslettertype_id' => array( 'name' => 'NewsletterType',
                                                                     'datatype' => 'integer',
                                                                     'default' => 0,
                                                                     'required' => true ),
                                         'category' => array( 'name' => 'Category',
                                                              'datatype' => 'string',
                                                              'default' => '',
                                                              'required' => true ),
                                         'pretext' => array( 'name' => 'Pretext',
                                                              'datatype' => 'string',
                                                              'default' => '',
                                                              'required' => true ),
                                         'posttext' => array( 'name' => 'Posttext',
                                                              'datatype' => 'string',
                                                              'default' => '',
                                                              'required' => true ),
                                         'object_relations' => array( 'name' => 'ObjectRelations',
                                                                             'datatype' => 'string',
                                                                             'default' => '',
                                                                             'required' => true ),
                                         'preview_email' => array( 'name' => 'PreviewEmail',
                                                                   'datatype' => 'string',
                                                                   'default' => '',
                                                                   'required' => true ),
                                         'preview_mobile' => array( 'name' => 'PreviewMobile',
                                                                    'datatype' => 'string',
                                                                    'default' => '',
                                                                    'required' => true ),
                                         'recurrence_type' => array( 'name' => 'RecurrenceType',
                                                              'datatype' => 'string',
                                                              'default' => '',
                                                              'required' => true ),
                                         'recurrence_value' => array( 'name' => 'RecurrenceValue',
                                                                     'datatype' => 'string',
                                                                     'default' => '',
                                                                     'required' => true ),
                                         'recurrence_condition' => array( 'name' => 'RecurrenceCondition',
                                                                          'datatype' => 'string',
                                                                          'default' => '',
                                                                          'required' => true ),
                                         'recurrence_last_sent' => array( 'name' => 'SendStatus',
                                                                          'datatype' => 'integer',
                                                                          'default' => 0,
                                                                          'required' => true ),
                                         'status' => array( 'name' => 'Status',
                                                            'datatype' => 'integer',
                                                            'default' => 0,
                                                            'required' => true ),
                                         'created' => array( 'name' => 'Created',
                                                             'datatype' => 'integer',
                                                             'default' => 0,
                                                             'required' => true ),
                                         'creator_id' => array( 'name' => 'Creator',
                                                                'datatype' => 'integer',
                                                                'default' => 0,
                                                                'required' => true ) ),
                      'keys' => array( 'id', 'status' ),
                      'function_attributes' => array( 'creator' => 'creator',
                                                      'newsletter_type' => 'newsletterType',
                                                      'send_count' => 'sendCount',
                                                      'read_count' => 'readCount',
                                                      'contentobject' => 'contentObject',
                                                      'contentobjectversion' => 'contentObjectVersion',
                                                      'object_relation_id_list' => 'objectRelationIDList',
                                                      'output_format_list' => 'outputFormatList',
                                                      'recurrence_value_list' => 'recurrenceValueList',
                                                      'send_status_name_map' => 'sendStatusNameMap',
                                                      'status_name_map' => 'statusNameMap',
                                                      'recurrence_name_map' => 'recurrenceNameMap',
                                                      'send_year' => 'sendYear',
                                                      'send_month' => 'sendMonth',
                                                      'send_day' => 'sendDay',
                                                      'send_hour' => 'sendHour',
                                                      'send_minute' => 'sendMinute' ),
                      'increment_key' => 'id',
                      'sort' => array( 'id' => 'asc' ),
                      'class_name' => 'eZNewsletter',
                      'name' => 'eznewsletter' );
    }

    /*!
     \reimp
    */
    function attribute( $attr, $noFunction = false )
    {
        $retVal = false;
        switch( $attr )
        {
            case 'send_year':
            {
                $retVal = date( 'Y', $this->attribute( 'send_date' ) );
            } break;

            case 'send_month':
            {
                $retVal = date( 'm', $this->attribute( 'send_date' ) );
            } break;

            case 'send_day':
            {
                $retVal = date( 'd', $this->attribute( 'send_date' ) );
            } break;

            case 'send_hour':
            {
                $retVal = date( 'H', $this->attribute( 'send_date' ) );
            } break;

            case 'send_minute':
            {
                $retVal = date( 'i', $this->attribute( 'send_date' ) );
            } break;

            case 'send_count':
            {
                $retVal = eZSendNewsletterItem::sendCount( $this->attribute( 'id' ) );
            } break;

            case 'read_count':
            {
                $retVal = eZSendNewsletterItem::readCount( $this->attribute( 'id' ) );
            } break;

            case 'output_format_list':
            {
                $retVal = explode( ',', $this->attribute( 'output_format' ) );
            } break;

            case 'recurrence_value_list':
            {
                $retVal = explode( ',', $this->attribute( 'recurrence_value' ) );
            } break;

            case 'object_relation_id_list':
            {
                $retVal = explode( '/', $this->attribute( 'object_relations' ) );
                array_pop( $retVal );
                array_shift( $retVal );
            } break;

            default:
            {
                $retVal = eZPersistentObject::attribute( $attr );
            } break;
        }

        return $retVal;
    }

    /*!
     \static

     Fetch by hash
    */
    static function fetchByHash( $hash, $status = eZNewsletter::StatusPublished, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZNewsletter::definition(),
                                                null,
                                                array( 'hash' => $hash,
                                                       'status' => $status ),
                                                $asObject );
    }

    /*!
     Fetch contentobject
    */
    function contentObject()
    {
        if( eZContentObject::exists( $this->attribute( 'contentobject_id' ) ) )
        {
            return eZContentObject::fetch( $this->attribute( 'contentobject_id' ) );
        }
        return false;
    }

    /*!
     Fetch contentobject version
     */
    function contentObjectVersion()
    {
        $retVal = false;
        if( eZContentObject::exists( $this->attribute( 'contentobject_id' ) ) )
        {
            $retVal = eZContentObjectVersion::fetchVersion(
                          $this->attribute( 'contentobject_version' ),
                          $this->attribute( 'contentobject_id' ) );
        }
        return $retVal;
    }

    /*!
     Build sending list
    */
    function buildSendList()
    {
        $mailsettings = eZINI::instance( 'bounce.ini' );
        $bounceCountStop = ( $mailsettings->variable( 'BounceSettings', 'BounceCount' ) ? $mailsettings->variable( 'BounceSettings', 'BounceCount' ) : 2 );

        $db = eZDB::instance();
        $db->begin();

        $sql = "SELECT email, SUM(bounce_count) FROM ezsubscription GROUP BY email HAVING SUM(bounce_count) >=" . $bounceCountStop;
        $bouncedRecipients = $db->arrayQuery( $sql );

        $bounceEmails = array();
        foreach ( $bouncedRecipients as $bounce )
        {
            if ( !in_array( $bounce['email'], $bounceEmails ) )
            {
                $bounceEmails[] = $bounce['email'];
            }
        }

        $newsletterType = $this->attribute( 'newsletter_type' );

        foreach( $newsletterType->attribute( 'subscription_list' ) as $newsletterSubscriptionLink )
        {
            $offset = 0;
            $subscriptionList = $newsletterSubscriptionLink->attribute( 'subscription_object' );

            while( $subscriptionArray = $subscriptionList->fetchSubscriptionArray(
                                            $offset,
                                            eZNewsletter::MaxSubscriptionFetchLimit,
                                            true,
                                            eZSubscription::VersionStatusPublished,
                                            array( array(
                                                eZSubscription::StatusApproved,
                                                eZSubscription::StatusConfirmed  ) ) ) )
            {
                foreach( $subscriptionArray as $subscription )
                {
                    if ( !eZRobinsonListEntry::inList( $subscription->attribute( 'email' ), eZRobinsonListEntry::EMAIL ) )
                    {
                            if ( in_array( $subscription->attribute( 'email' ), $bounceEmails ) )
                            {
                                    //This recipient is in the bounce register, we set mailing on hold
                                    eZSendNewsletterItem::create( $this->attribute( 'id' ),
                                                                  $subscription->attribute( 'id' ),
                                                                  eZSendNewsletterItem::SendStatusOnHold );
                            }
                            else
                            {
                                if ( eZMail::validate( $subscription->attribute( 'email' ) ) )
                                {
                                    eZSendNewsletterItem::create( $this->attribute( 'id' ),
                                                                  $subscription->attribute( 'id' ) );
                                    echo ".";
                                }
                                else
                                {
                                    echo "Invalid email address: <".$subscription->attribute( 'email' ).">"."\n";
                                }
                            }
                    }
                    else
                    {
                        echo "Ignoring <".$subscription->attribute( 'email' )."> (in robinsonlist)"."\n";
                    }
                }
                $offset += eZNewsletter::MaxSubscriptionFetchLimit;
            }
        }

        $db->commit();
    }

    /*!
     Fetch newsletter type
    */
    function newsletterType()
    {
        return eZNewsletterType::fetch( $this->attribute( 'newslettertype_id' ) );
    }

    /*!
     Fetch newsletter list

     \param send status
     \param status
     \param as object
     \param additional parameters

     \return newsletter list
    */
    static function fetchListBySendStatus( $sendStatus,
                                    $status = eZNewsletter::StatusPublished,
                                    $asObject = true,
                                    $additionalParameters = array() )
    {
        return eZPersistentObject::fetchObjectList( eZNewsletter::definition(),
                                                    null,
                                                    array_merge( array( 'send_status' => $sendStatus,
                                                                        'status' => $status ),
                                                                        $additionalParameters ),
                                                    null,
                                                    null,
                                                    $asObject );
    }

    /*!
      \return eZNewsletter object.
     */
    static function fetch( $newsletterID, $status = eZNewsletter::StatusPublished, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZNewsletter::definition(),
                                               null,
                                               array( 'id' => $newsletterID,
                                                      'status' => $status ),
                                               $asObject );
    }

    /*!
     Fetch newsletter by content object values.

     If no newsletter matching content object id and version is found, create a new draft based on a previoud newsletter with the same contentobject ID.

     \param content object id
     \param content object version
    */
    static function fetchByContentObject( $contentObjectID,
                                   $contentObjectVersion = false,
                                   $status = eZNewsletter::StatusPublished,
                                   $asObject = true )
    {
        $condArray = array( 'contentobject_id' => $contentObjectID );
        if ( $contentObjectVersion !== false )
        {
            $condArray['contentobject_version'] = $contentObjectVersion;
        }
        if ( $status !== false )
        {
            $condArray['status'] = $status;
        }

        return eZPersistentObject::fetchObject( eZNewsletter::definition(),
                                                null,
                                                $condArray,
                                                $asObject );
    }

    /*!
     Fetch newlsetter list by related content object ids

     \param content object id

     \return list of newsletter objects.
    */
    static function fetchListByRelatedContentObject( $contentObjectID,
                                              $status = eZNewsletter::StatusPublished,
                                              $asObject = true )
    {
        $db = eZDB::instance();
        $condSQL = 'object_relations LIKE \'%/' . $db->escapeString( $contentObjectID ) . '/%\' AND
                    status = \'' . $db->escapeString( $status ) . '\'';
        $sql = 'SELECT *
                FROM eznewsletter
                WHERE ' . $condSQL;
        $rows = $db->arrayQuery( $sql );

        $definition = eZNewsletter::definition();
        $className = $definition['class_name'];
        return eZPersistentObject::handleRows( $rows, $className, $asObject );
    }

    /*!
      Fetches a specified sequence of eZNewsletter objects
      \return Array of eZNewsletter objects
    */
    static function fetchByOffset( $offset,
                            $limit,
                            $status = eZNewsletter::StatusPublished,
                            $asObject = true )
    {
        $newsletterArray = eZPersistentObject::fetchObjectList( eZNewsletter::definition(),
                                                                null,
                                                                array( 'status' => $status ),
                                                                array( 'id' => 'ASC' ),
                                                                array( 'offset' => $offset, 'length' => $limit ),
                                                                $asObject );
        return $newsletterArray;
    }

    /*!
      \static
      Fetch draft of eZNewsletter object. A new object is created if none exist.
     */
    static function fetchDraft( $id, $asObject = true )
    {
        $newsletter = eZNewsletter::fetch( $id, array( array( eZNewsletter::StatusDraft, eZNewsletter::StatusPublished ) ), $asObject );
        if ( eZNewsletter::StatusPublished == $newsletter->attribute( 'status' ) )
        {
            $newsletter->setAttribute( 'status', eZNewsletter::StatusDraft );
            $newsletter->store();
        }

        if ( !$newsletter )
        {
            return false;
        }
        return $newsletter;
    }

    /*!
     Remove draft.
    */
    function removeDraft()
    {
        $newsletterDraft = eZNewsletter::fetchDraft( $this->attribute( 'id' ) );
        $newsletterDraft->remove();
    }

    /*!
      \static
      Remove all objects of \a id
    */
    static function removeAll( $id )
    {
        eZPersistentObject::removeObject( eZNewsletter::definition(),
                                          array( 'id' => $id ) );
    }

    /*!
      Publish eZNewsletter object.
      Sets the status to published, stores the object and removes the draft version.
    */
    function publish()
    {
        $db = eZDB::instance();
        $db->begin();
        $this->removeAll( $this->ID );
        $object = $this->attribute( 'contentobject' );
        if ( $object )
        {
            $objectRelations = $object->relatedContentObjectList( false, false, false );
            $objectRelationIDString = array();

            foreach( $objectRelations as $relatedObject )
            {
                $objectRelationIDString[] = $relatedObject->attribute( 'id' );
            }
            $this->setAttribute( 'object_relations', '/' . implode( '/', $objectRelationIDString ) . '/' );
        }

        $this->setAttribute( 'status', eZNewsletter::StatusPublished );
        $this->store();
        $db->commit();
    }

    /*!
      \static
      Create a new eZNewsletter object
    */
    static function create( $name = '', $userID = false, $newsletterTypeID )
    {
        if ( $userID === false )
        {
            $userID = eZUser::currentUserID();
        }

        $user = eZUser::fetch( $userID, true );
        $userObject = $user->contentObject();
        $data_map = $userObject->attribute( 'data_map' );
        $userEmail = $user->attribute( 'email' );
        
        $userMobile = "";
        
        if( isset( $data_map['mobile_number'] ) )
        {
            $userMobile = $data_map['mobile_number']->DataText;
        }

        $newsletter_type = eZNewsletterType::fetch( $newsletterTypeID );

        $send_date_modifier = $newsletter_type->attribute( 'send_date_modifier' );
        $pretext = $newsletter_type->attribute('pretext');
        $posttext = $newsletter_type->attribute('posttext');

        $newsletter = new eZNewsletter( array( 'created' => time(),
                                               'send_date' => time() + $send_date_modifier,
                                               'newslettertype_id' => $newsletter_type->attribute( 'id' ),
                                               'pretext' => $pretext,
                                               'posttext' => $posttext,
                                               'creator_id' => $userID,
                                               'preview_email' => $userEmail,
                                               'preview_mobile' => $userMobile,
                                               'name' => $name,
                                               'hash' => md5( time() . '-' . mt_rand() ),
                                               'status' => eZNewsletter::StatusDraft ) );
        $newsletter->store();
        return $newsletter;
    }

    /*!
      Get creator user object
    */
    function creator()
    {
        $user = eZUser::fetch( $this->attribute( 'creator_id' ) );
        return $user;
    }
    
    function outputFormatList()
    {
        //return explode( ',', )
    }

    /*!
     Generate Newsletter

     \param output type, text, HTML, or linked
    */
    function generateNewsletter( $contentObject )
    {
        // 1. Set resource keys
        

        $res = eZTemplateDesignResource::instance();
        $res->setKeys( array( 
            array( 
                'newslettertype_id' , 
                $this->attribute( 'newslettertype_id' ) 
            ) , 
            array( 
                'newsletter_id' , 
                $this->attribute( 'id' ) 
            ) , 
            array( 
                'class_identifier' , 
                '' 
            ) , 
            array( 
                'newsletter_type' , 
                'mail' 
            ) 
        ) );
        
        // 2. Set general mail and template properties
        $ini = eZINI::instance();
        $hostname = eZSys::hostname();
        $newsletterType = $this->attribute( 'newsletter_type' );
        
        // 3. get skin for newsletter
        if ( $this->attribute( 'design_to_use' ) )
        {
            $skin_prefix = $this->attribute( 'design_to_use' );
        }
        else
        {
            $skin_prefix = 'eznewsletter';
        }

        $mail = new ezpMailComposer();
        $mail->charset = 'utf-8';
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'hostname', $hostname );
        $tpl->setVariable( 'contentobject', $contentObject );
        $tpl->setVariable( 'newsletter', $this );
        $tpl->setVariable( 'SkipMIMEPart', false );
        
        $mail->plainText = $tpl->fetch( 'design:' . $skin_prefix . '/sendout/text.tpl' );
        $mail->plainText = rtrim( preg_replace('/(\r\n|\r|\n)/', "\r\n", $mail->plainText) );
        
        
        if ( $tpl->hasVariable( 'SkipMIMEPart' ) && $tpl->variable( 'SkipMIMEPart' ) === true )
        {
            $mail->plainText = null;
        }
        
        $tpl->setVariable( 'SkipMIMEPart', false );
        $mail->htmlText = $tpl->fetch( 'design:' . $skin_prefix . '/sendout/html.tpl' );
        $mail->htmlText = rtrim( preg_replace('/(\r\n|\r|\n)/', "\r\n", $mail->htmlText) );

        if ( $tpl->hasVariable( 'SkipMIMEPart' ) && $tpl->variable( 'SkipMIMEPart' ) === true )
        {
            $mail->htmlText = null;
        }

        if ( $tpl->hasVariable( 'attachments' ) )
        {
            foreach ( $tpl->hasVariable( 'attachments' ) as $attachment )
            {
                $mail->addFileAttachment( $attachment );
            }
        }
        
        $emailSender = $newsletterType->attribute( 'sender_address' ) ? $newsletterType->attribute( 'sender_address' ) : $ini->variable( 'MailSettings', 'EmailSender' );
        if ( $tpl->hasVariable( 'emailSenderName' ) )
        {
            $mail->from = new ezcMailAddress( $emailSender, $tpl->variable( 'emailSenderName' ), 'utf-8' );
        }
        else
        {
            $mail->from = new ezcMailAddress( $emailSender, null, 'utf-8' );
        }
        $subject = $this->attribute( 'name' );
        if ( $tpl->hasVariable( 'subject' ) )
        {
            $subject = $tpl->variable( 'subject' );
        }
        $mail->subject = $subject;
        $mail->subjectCharset = 'utf-8';
        
        if ( preg_match_all( '/(<img)\s (src="\/([a-zA-Z0-9-\.;:!\/\?&=_|\r|\n]{1,})")/isxmU', $mail->htmlText, $patterns ) )
        {
            foreach ( $patterns[3] as $key => $file )
            {
                if ( file_exists( $file ) and ! is_dir( $file ) )
                {
                    $mail->htmlText = preg_replace( "/" . preg_quote( $patterns[0][$key], '/' ) . "/", $patterns[1][$key] . ' src="file://' . str_replace( "\\", "/", eZSys::rootDir() ) . '/' . $file . "\"", $mail->htmlText );
                }
            }
        }
        
        //setting the reply-to head from site.ini
        if ( $ini->variable( 'MailSettings', 'EmailReplyTo' ) != "" )
        {
        	$mail->setHeader( 'Reply-To', $ini->variable( 'MailSettings', 'EmailReplyTo' ) );
        }
        return $mail;
    }

    /*!
     \static
     Get send status name map
    */
    static function sendStatusNameMap()
    {
        return array( eZNewsletter::SendStatusNone => ezpI18n::tr( 'eznewsletter/send_status', 'Not sent' ),
                      eZNewsletter::SendStatusBuldingList => ezpI18n::tr( 'eznewsletter/send_status', 'Building sendout list' ),
                      eZNewsletter::SendStatusSending => ezpI18n::tr( 'eznewsletter/send_status', 'Sending' ),
                      eZNewsletter::SendStatusFinished => ezpI18n::tr( 'eznewsletter/send_status', 'Finished' ),
                      eZNewsletter::SendStatusStopped => ezpI18n::tr( 'eznewsletter/send_status', 'Stopped' ) );
    }

    /*!
     \static
     Get status name map
    */
    static function statusNameMap()
    {
        return array( eZNewsletter::StatusDraft => ezpI18n::tr( 'eznewsletter/object_status', 'Draft' ),
                      eZNewsletter::StatusPublished => ezpI18n::tr( 'eznewsletter/object_status', 'Published' ) );
    }

    /*!
     \static
     Get recurrence name map
    */
    static function recurrenceNameMap()
    {
        return array( eZNewsletter::RecurrenceDaily   => ezpI18n::tr( 'eznewsletter/recurrencetype', 'Daily' ),
                      eZNewsletter::RecurrenceWeekly  => ezpI18n::tr( 'eznewsletter/recurrencetype', 'Weekly' ),
                      eZNewsletter::RecurrenceMonthly => ezpI18n::tr( 'eznewsletter/recurrencetype', 'Monthly' ) );
    }

    /*!
      Sends an email with MIME headers.      
     */
    static function sendNewsletterMail( eZNewsletter $newsletter, $sendPreview = false )
    {
        $sendMailSettings = eZINI::instance( 'ezsendmailsettings.ini' );
        $hostname = $sendMailSettings->variable( 'SendNewsletter', 'Host' );
        
        $sys = eZSys::instance();
        
        // Check that the newsletter type exists, if not, process next newsletter
        if ( ! $newsletter->attribute( 'newsletter_type' ) )
        {
            return false;
        }

        if ( $sendPreview )
        {
            // Fetch the draft because the object is not published during preview
            $contentObject = $newsletter->contentObjectVersion();
        }
        else
        {
            $contentObject = $newsletter->contentObject();
        }
        $mailTemplate = $newsletter->generateNewsletter( $contentObject );
        
        $partCounter = 0;

        // 4. Go through revceivers, and send emails.
        if ( ! $sendPreview )
        {
            $idcounter = 0;
            $sendCount = 0;
            $skipCount = 0;
            
            while ( $receiverList = eZSendNewsletterItem::fetchByNewsletterID( $newsletter->attribute( 'id' ) ) )
            {
                foreach ( $receiverList as $receiver )
                {
                    $mail = clone $mailTemplate;
                    $userData = $receiver->attribute( 'user_data' );
                    if ( ! $userData )
                    {
                        //When no userdata is found, it is usually the result of a deleted subscription,
                        //we mark the mail as being sent, without sending it.
                        $receiver->setAttribute( 'send_status', eZSendNewsletterItem::SendStatusSent );
                        $receiver->setAttribute( 'send_ts', time() );
                        $receiver->sync();
                        continue;
                    }
                    
                    //personalize if set in type
                    $newsletter_type = eZNewsletterType::fetch( $newsletter->attribute( 'newslettertype_id' ) );
                    
                    if ( $newsletter_type->attribute( 'personalise' ) === '1' )
                    {
                        $mail->personalize( $userData, true );
                    }
                    else
                    {
                        $mail->personalize( $userData, false );
                    }
                    $mail->generateMessageId( $receiver->attribute( 'id' ) . '.' . $newsletter->attribute( 'id' ) );
                    $mail->to = array();
                    $mail->addTo( new ezcMailAddress( $userData['email'], null, 'utf-8' ) );
                    $mail->build();
                    $mailResult = eZNewsletterMailTransport::send( $mail, false );
                    $sendCount ++;
                    
                    $receiver->setAttribute( 'send_status', eZSendNewsletterItem::SendStatusSent );
                    $receiver->setAttribute( 'send_ts', time() );
                    $receiver->sync();
                }
                
            }
            return array( 
                'sendCount' => $sendCount , 
                'skipCount' => $skipCount 
            );
        }
        else
        {
            $mail = clone $mailTemplate;
            //send preview
            
            $user = eZUser::currentUser();
            $userObject = $user->attribute( 'contentobject' );
            
            //personalize if set in type
            $newsletter_type = eZNewsletterType::fetch( $newsletter->attribute( 'newslettertype_id' ) );

            if ( $newsletter_type->attribute( 'personalise' ) === '1' )
            {
                $mail->personalize( array( 
                    'name' => $userObject->attribute( 'name' ) 
                ), true );
            }
            else
            {
                $mail->personalize( array( 
                    'name' => $userObject->attribute( 'name' ) 
                ), false );
                $newsletterMail = $newsletter->personalize( $mail->plainText, array( 
                    'name' => $userObject->attribute( 'name' ) 
                ), false );
            }
            
            $mail->addTo( new ezcMailAddress( $newsletter->attribute( 'preview_email' ) ) );
            
            $mail->build();
            
            $mailResult = eZNewsletterMailTransport::send( $mail, true );
        }
    
    }

    function copy( $frontendcopy = false )
    {
        $userID = eZUser::currentUserID();
        if (!$userID)
        {
            $userID = $this->attribute( 'creator_id' );
        }

        $newNewsletter = eZNewsletter::create( $this->attribute( 'name'), $userID, $this->attribute( 'newslettertype_id') );

        $newNewsletter->setAttribute( 'output_format', $this->attribute( 'output_format' ) );
        $newNewsletter->setAttribute( 'design_to_use', $this->attribute( 'design_to_use' ) );
        $newNewsletter->setAttribute( 'send_date', $this->attribute( 'send_date' ) );
        $newNewsletter->setAttribute( 'newslettertype_id', $this->attribute( 'newslettertype_id' ) );
        $newNewsletter->setAttribute( 'category', $this->attribute( 'category' ) );
        $newNewsletter->setAttribute( 'pretext', $this->attribute( 'pretext' ) );
        $newNewsletter->setAttribute( 'posttext', $this->attribute( 'posttext' ) );
        $newNewsletter->setAttribute( 'object_relations', $this->attribute( 'object_relations' ) );
        $newNewsletter->setAttribute( 'preview_email', $this->attribute( 'preview_email' ) );
        $newNewsletter->setAttribute( 'recurrence_type', $this->attribute( 'recurrence_type' ) );
        $newNewsletter->setAttribute( 'recurrence_value', $this->attribute( 'recurrence_value' ) );
        $newNewsletter->setAttribute( 'creator_id',  $this->attribute( 'creator_id' ) );

        $newNewsletter->setAttribute( 'status', eZNewsletter::StatusPublished );

        if( $frontendcopy == true )
        {
            $newNewsletter->setAttribute( 'send_status', eZNewsletter::SendStatusNone );
            $newNewsletter->setAttribute( 'status', eZNewsletter::StatusDraft );
        }
        else
        {
            $newNewsletter->setAttribute( 'send_status', $this->attribute( 'send_status' ) );
        }

        $objectID = $this->attribute( 'contentobject_id' );
        $objectVersion = $this->attribute( 'contentobject_version' );

        $object = eZContentObject::fetch( $objectID );
        
        if( is_object( $object ))
        {
            $newObject = $object->copy( $objectVersion );

            $newNewsletter->setAttribute( 'contentobject_id', $newObject->attribute( 'id' ) );
            $newNewsletter->setAttribute( 'contentobject_version', $newObject->attribute( 'current_version' ) );
            $newNewsletter->store();

            return $newObject;
        }
    }
}

?>
