<?php

include_once( 'kernel/common/eztemplatedesignresource.php' );
include_once( 'kernel/common/template.php' );
include_once( "lib/ezutils/classes/ezmail.php" );
include_once( "lib/ezutils/classes/ezmailtransport.php" );
include_once( "lib/ezfile/classes/ezfile.php" );

include_once( eZExtension::baseDirectory() . '/eznewsletter/classes/ezsubscriptionlist.php' );
include_once( eZExtension::baseDirectory() . '/eznewsletter/classes/ezsubscription.php' );
include_once( eZExtension::baseDirectory() . '/eznewsletter/classes/eznewsletter.php' );

$Module =& $Params['Module'];

$tpl = templateInit();

$http = eZHTTPTool::instance();

$res = eZTemplateDesignResource::instance();
$res->setKeys( array( array( 'newsletter_view', 'register_subscription' ) ) );
$NewsletterItem = eZSendNewsletterItem::fetchByHash( $Params['UserHash'] );
if ( !$NewsletterItem )
{
    return $Module->handleError( EZ_ERROR_KERNEL_NOT_AVAILABLE, 'kernel' );
}
$sub  = $NewsletterItem->userData();
$subscription = eZSubscription::fetch( $NewsletterItem->attribute( 'subscription_id' ) );
$tpl->setVariable( 'NewsletterItem', $NewsletterItem );

$tpl->setVariable( 'UserHash', $Params['UserHash'] );
$tpl->setVariable( 'subscriptions', $subscription );

if ( $http->hasPostVariable( 'OKButton' ) )
{
    $subscription->unsubscribe();
    $siteini = eZINI::instance();
    $sender = $siteini->variable( 'MailSettings', 'EmailSender' );
    $mail = new eZMail();
    $mail->setReceiver( $sub['email'] );
    $mail->setSender( $sender );
    $mail->setSubject( ezi18n( 'newsletteraddons', "Your subscription removal" ) );
    $hostName = eZSys::hostname();
    $mailtpl = templateInit();
    $mailtpl->setVariable( 'hostname', $hostName );
    $mailtpl->setVariable( 'siteaccess', $GLOBALS['eZCurrentAccess']['name'] );
    $mailtpl->setVariable( 'NewsletterItem', $NewsletterItem );
    $mailtext = $mailtpl->fetch( 'design:eznewsletter/unregister_subscription_email.tpl' );

    $mail->setBody( $mailtext );

    eZMailTransport::send( $mail );
    
    $Result = array();
    $Result['content'] = $tpl->fetch( "design:eznewsletter/unregister_subscription_success.tpl" );
    $Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'eznewsletter', 'Remove subscription' ) ) );
    return;
}
if ( $http->hasPostVariable( 'CancelButton' ) )
{
    $ini = eZINI::instance();
    return $Module->redirectTo( $ini->variable( 'SiteSettings', 'DefaultPage' ) );
}

$Result = array();
$Result['content'] = $tpl->fetch( "design:eznewsletter/unregister_subscription.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'eznewsletter', 'Remove subscription' ) ) );

?>