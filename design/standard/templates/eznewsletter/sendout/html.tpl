{set-block variable=$subject scope=root}{$contentobject.name|wash} - [name]{/set-block}

{foreach $contentobject.contentobject_attributes as $attribute}
<h2>{$attribute.contentclass_attribute.name|wash} :</h2>
    {attribute_view_gui attribute=$attribute}
<div class="break"></div>
{/foreach}
