    {let children       = fetch( newsletter, newsletter_type_list, hash( 'filter', true ) )
         numChildren    = fetch( newsletter, newsletter_type_count, hash( 'filter', true ) )
         haveChildren   = $numChildren|gt(0)
         showToolTips   = ezini( 'TreeMenu', 'ToolTips' , 'contentstructuremenu.ini' )
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
            {set isRootNode=$is_root_node}
        {/section}

        <li id="n0"{cond( $:last_item, 'class="lastli"', '' )}>

            {* Fold/Unfold/Empty: [-]/[+]/[ ] *}
                   <a class="openclose" href="#" title="{'Fold/Unfold'|i18n('design/eznewsletter/contentstructuremenu')}"
                      onclick="ezpopmenu_hideAll(); ezcst_onFoldClicked( this.parentNode ); return false;"></a>

            {* Label *}
                    {set toolTip = ''}

                {* Text *}
                {section show=or( eq($ui_context, 'browse')|not(), eq($:parentNode.object.is_container, true()))}
                    <a class="nodetext" href={'newsletter/list_type'|ezurl} title="{$:toolTip}"><span class="node-name-normal">{'Newsletter type list'|i18n( 'design/eznewsletter/contentstructuremenu' )}</span></a>
                {section-else}
                            <span class="node-name-normal">{'Newsletter type list'|i18n( 'design/eznewsletter/contentstructuremenu' )}</span>
                {/section}

                {* Show children *}
                {section show=$:haveChildren}
                    <ul>
                        {section var=child loop=$:children}
                            {include name=SubMenu uri="design:contentstructuremenu/show_newsletter_type.tpl" newsletterType=$:child csm_menu_item_click_action=$:csm_menu_item_click_action last_item=eq( $child.number, $:numChildren ) ui_context=$ui_context}
                        {/section}
                    </ul>
                {/section}
        </li>
        {/default}
    {/let}
