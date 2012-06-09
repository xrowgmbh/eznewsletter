{if is_set($:newsletterType.inbox_object)}
    {def $newsletter_children = $:newsletterType.inbox_object.main_node.children}
{else}
    {def $newsletter_children = array()}
{/if}

    {let children       = $newsletter_children
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

<li id="n{$:newsletterType.id}_inbox" {cond( $last_item, 'class="lastli"', '' )}>
            <span class="openclose"></span>
        {if is_set($:newsletterType.inbox_object)}
                    <a class="nodetext" href={$:newsletterType.inbox_object.main_node.url_alias|ezurl} title="{$:toolTip}"><span class="node-name-normal">{'Ideas'|i18n( 'design/eznewsletter/contentstructuremenu' )}</span></a>
        {else}
                    <span class="node-name-normal">{'Ideas'|i18n( 'design/eznewsletter/contentstructuremenu' )}</span>
        {/if}
</li>
        {/default}
    {/let}
