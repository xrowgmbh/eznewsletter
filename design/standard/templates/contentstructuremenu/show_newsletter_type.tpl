    {let children       = array()
         numChildren    = array()
         haveChildren   = $numChildren|gt(0)
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

        <li id="nt{$:newsletterType.id}" {section show=$:last_item} class="lastli"{/section}>

            {* Fold/Unfold/Empty: [-]/[+]/[ ] *}
                   <a class="openclose" href="#" title="{'Fold/Unfold'|i18n('design/eznewsletter/contentstructuremenu')}"
                      onclick="ezpopmenu_hideAll(); ezcst_onFoldClicked( this.parentNode ); return false;"></a>

            {* Label *}
                    {set toolTip = ''}

                {* Text *}
                {section show=or( eq($ui_context, 'browse')|not(), eq($:parentNode.object.is_container, true()))}
                    <a class="nodetext" href={concat( 'newsletter/view_type/', $:newsletterType.id )|ezurl} title="{$:toolTip}"><span class="node-name-normal">{$:newsletterType.name|wash}</span></a>
                {section-else}
                            <span class="node-name-normal">{$:newsletterType.name|wash}</span>
                {/section}

                {* Show children *}
	    <ul>
            {include name=SubMenu uri="design:contentstructuremenu/show_newsletter_archive.tpl" newsletterType=$:newsletterType csm_menu_item_click_action=$:csm_menu_item_click_action last_item=false() ui_context=$ui_context}
            {include name=SubMenu uri="design:contentstructuremenu/show_newsletter_progress.tpl" newsletterType=$:newsletterType csm_menu_item_click_action=$:csm_menu_item_click_action last_item=false() ui_context=$ui_context}
            {include name=SubMenu uri="design:contentstructuremenu/show_newsletter_draft.tpl" newsletterType=$:newsletterType csm_menu_item_click_action=$:csm_menu_item_click_action last_item=true() ui_context=$ui_context}
            {include name=SubMenu uri="design:contentstructuremenu/show_newsletter_recurring.tpl" newsletterType=$:newsletterType csm_menu_item_click_action=$:csm_menu_item_click_action last_item=true() ui_context=$ui_context}
            {include name=SubMenu uri="design:contentstructuremenu/show_newsletter_inbox.tpl" newsletterType=$:newsletterType csm_menu_item_click_action=$:csm_menu_item_click_action last_item=true() ui_context=$ui_context}
	    </ul>
        </li>
        {/default}
    {/let}
