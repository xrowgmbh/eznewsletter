<?php
define( "EZ_WORKFLOW_TYPE_IMPORTNEWSLETTER_ID", "importnewsletter" );

include_once( eZExtension::baseDirectory() . '/eznewsletter/classes/eznewsletter.php' );

class ImportNewsletterType extends eZWorkflowEventType
{
    function ImportNewsletterType()
    {
    	$this->eZWorkflowEventType( EZ_WORKFLOW_TYPE_IMPORTNEWSLETTER_ID, ezi18n( 'newsletteraddon/event', 'Newsletter import event' ));
    	$this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'after' ) ) ) );
    }
    function execute( &$process, &$event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        $co = eZContentObject::fetch( $parameters['object_id'] );
        $parent = eZContentObjectTreeNode::fetch( $co->attribute( 'main_parent_node_id' ) );
        $parentco = $parent->attribute( 'object' );

        $newslettertype = eZPersistentObject::fetchObject( eZNewsletterType::definition(),
                                                null,
                                                array( 'article_pool_object_id' => $parentco->attribute( 'id' ),
                                                       'status' => eZNewsletterType_StatusPublished ) );
        
                               
        $list = eZNewsletter::fetchByContentObject( $parameters['object_id'] );
        if ( empty( $list ) and is_object( $newslettertype ) )
        {
            $newsletter = eZNewsletter::create( $co->attribute('name'), $co->attribute( 'owner_id' ), $newslettertype->attribute('id') );
            $newsletter->setAttribute( 'contentobject_id', $parameters['object_id'] );
            $newsletter->setAttribute( 'template_to_use', 'mobotixnewsletter' );
            $newsletter->setAttribute( 'contentobject_version', $parameters['version'] );
            $newsletter->store();
            $newsletter->publish();
        }
    	return EZ_WORKFLOW_TYPE_STATUS_ACCEPTED;
    }
}
eZWorkflowEventType::registerType( EZ_WORKFLOW_TYPE_IMPORTNEWSLETTER_ID, 'importnewslettertype' );
?>