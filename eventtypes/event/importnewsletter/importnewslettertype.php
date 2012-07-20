<?php

class ImportNewsletterType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = "importnewsletter";
    function ImportNewsletterType()
    {
    	$this->eZWorkflowEventType( ImportNewsletterType::WORKFLOW_TYPE_STRING, ezi18n( 'newsletteraddon/event', 'Newsletter import event' ));
    	$this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'after' ) ) ) );
    }
    function execute( $process, $event )
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
    	return eZWorkflowType::STATUS_ACCEPTED;
    }
}
eZWorkflowEventType::registerEventType( ImportNewsletterType::WORKFLOW_TYPE_STRING, "ImportNewsletterType" );

?>
