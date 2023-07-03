{* if $smarty.get.controller === 'category'}{$dir = 'catalog/'|cat:$smarty.get.controller}{else}{$dir = $smarty.get.controller}{/if*}
{extends file="{$extends}"}
{block name="plugin:content"}
{if {employee_access type="view" class_name=$cClass} eq 1}
    <p class="text-right">
        {#nbr_tabspanel#|ucfirst}: {$tabspanel|count}<a href="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;action=edit&amp;edit={$smarty.get.edit}&amp;plugin={$smarty.get.plugin}&amp;mod_action=add" title="{#add_tabspanel#}" class="btn btn-link">
            <span class="fa fa-plus"></span> {#add_tabspanel#|ucfirst}
        </a>
    </p>
    {if $debug}
        {$debug}
    {/if}
    {include file="section/form/table-form-3.tpl" controller=$smarty.get.controller plugin='tabspanel' subcontroller='tabspanel' data=$tabspanel idcolumn='id_tp' ajax_form=true activation=false search=false sortable=true}

    {include file="modal/delete.tpl" plugin='tabspanel' data_type='tabspanel' title={#modal_delete_title#|ucfirst} info_text=true delete_message={#delete_tabspanel_message#}}
    {include file="modal/error.tpl"}
    {else}
        {include file="section/brick/viewperms.tpl"}
    {/if}
{/block}

{block name="foot"}
    {capture name="mod_url"}{strip}
        {$smarty.server.SCRIPT_NAME}
        ?controller={$smarty.get.controller}
        {if isset($smarty.get.action)}&action={$smarty.get.action}{/if}
        {if isset($smarty.get.edit)}&edit={$smarty.get.edit}{/if}
        {if isset($smarty.get.tabs)}&tabs={$smarty.get.tabs}{/if}
        {if isset($smarty.get.tab)}&tab={$smarty.get.tab}{/if}
        &plugin={$smarty.get.plugin}{if isset($smarty.get.mod_edit)}&mod_edit={$smarty.get.mod_edit}{/if}
    {/strip}{/capture}
    {capture name="scriptForm"}/{baseadmin}/min/?f=libjs/vendor/jquery-ui-1.12.min.js,{baseadmin}/template/js/table-form.min.js,plugins/tabspanel/js/admin.min.js{/capture}
    {script src=$smarty.capture.scriptForm type="javascript"}
    <script type="text/javascript">
        window.addEventListener('load', function() {
            var controller = "{$smarty.capture.mod_url}";
            typeof tabspanel === "undefined" ? console.log("tabspanel is not defined") : tabspanel.run(controller) ;
        });
    </script>
{/block}