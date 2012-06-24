{set-block variable=$subject scope=root}{$contentobject.name|wash(string)} - [name]{/set-block}
<html>
<body>
{foreach $contentobject.contentobject_attributes as $attribute}
<h2>{$attribute.contentclass_attribute.name|wash} :</h2>
    {attribute_view_gui attribute=$attribute}
<div class="break"></div>
{/foreach}
</body>
</html>