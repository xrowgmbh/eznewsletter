<?php
class eZNewsletterAddons
{
    function removeDrafts( $user )
    {
        $list = eZPersistentObject::fetchObjectList( eZContentObjectVersion::definition(),
                                                    null, array( 'creator_id' => $user->id(),
                                                                 'status' => array( EZ_VERSION_STATUS_DRAFT, EZ_VERSION_STATUS_INTERNAL_DRAFT )
                                                                 ),
                                                    null, null,
                                                    true );
                                                    foreach ( $list as $item )
                                                    {
                                                        $item->remove();
                                                    }
    }
}
?>