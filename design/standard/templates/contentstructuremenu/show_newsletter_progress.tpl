    {let children       = $:newsletterType.article_pool_object.main_node.children
         numChildren    = count( $children )
         haveChildren   = false()
         showToolTips   = ezini( 'TreeMenu', 'ToolTips'         , 'contentstructuremenu.ini' )
         translation    = ezini( 'URLTranslator', 'Translation', 'site.ini' )
         toolTip        = ""
         visibility     = 'Visible'
         isRootNode     = false() }

        {default classIconsSize = ezini( 'TreeMenu', 'ClassIconsSize', 'contentstructuremenu.ini' )
                 last_item      = false() }

        {section show=is_set($class_icons_size)}
            {set classIconsSize=$class_icons_size}
        {/section}

        {section show=is_set($is_root_node)}
            {set isRootNode=false}
        {/section}

<li id="n{$:newsletterType.id}_progress">
    <span class="openclose"></span>
    <a class="nodetext" href={concat('newsletter/list_inprogress/',$:newsletterType.id)|ezurl} title="{$:toolTip}">
	<span class="node-name-normal">{'In progress'|i18n( 'design/eznewsletter/contentstructuremenu' )}</span>
    </a>
</li>
        {/default}
    {/let}
