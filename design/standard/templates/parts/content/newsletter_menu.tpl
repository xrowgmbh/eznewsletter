<div id="content-tree">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

{section show=ezpreference( 'admin_treemenu' )}
<h4><a class="showhide" href={'/user/preferences/set/admin_treemenu/0'|ezurl} title="Hide content structure."></a> {'Newslettertypes'|i18n( 'design/eznewsletter/parts/newsletter_menu' )}</h4>
{section-else}
<h4><a class="showhide" href={'/user/preferences/set/admin_treemenu/1'|ezurl} title="Show content structure."></a> {'Newslettertypes'|i18n( 'design/eznewsletter/parts/newsletter_menu' )}</h4>
{/section}

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">

{* Treemenu. *}
<div id="contentstructure">
{if ezini('TreeMenu','Dynamic','contentstructuremenu.ini')|eq('enabled')}
    {include uri='design:contentstructuremenu/content_structure_menu_dynamic_newsletter.tpl' custom_root_node=$custom_root_node menu_persistence=false() hide_node_list=array(ezini( 'NodeSettings', 'DesignRootNode', 'content.ini'), ezini( 'NodeSettings', 'SetupRootNode', 'content.ini'))}
{else}
    {include uri='design:contentstructuremenu/content_structure_menu.tpl' custom_root_node_id=1}
{/if}
</div>
{* DESIGN: Content END *}</div></div></div></div></div></div>



{* This is the border placed to the left for draging width, js will handle disabling the one above and enabling this *}
<div id="widthcontrol-handler" class="hide">
<div class="widthcontrol-grippy"></div>
</div>

</div>
