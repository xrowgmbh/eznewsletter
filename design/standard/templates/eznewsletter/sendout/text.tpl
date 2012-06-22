{set-block variable=$subject scope=root}{$contentobject.name|wash(string)} - [name]{/set-block}

{foreach $contentobject.contentobject_attributes as $attribute}
{$attribute.contentclass_attribute.name|wash} :
    {attribute_view_gui attribute=$attribute}
{/foreach}
