{* if $smarty.get.controller === 'category'}{$dir = 'catalog/'|cat:$smarty.get.controller}{else}{$dir = $smarty.get.controller}{/if}
{extends file="{$dir}/edit.tpl"*}
{extends file="{$extends}"}
{block name="plugin:content"}
    {if {employee_access type="edit" class_name=$cClass} eq 1}
        {if $debug}
            {$debug}
        {/if}
        {include file="mod/tabspanel.tpl" controller="tabspanel"}
        <div id="gallery-pages" class="block-img">
            {if $images != null}
                {include file="brick/img.tpl"}
            {/if}
        </div>
        {include file="delete.tpl" controller="tabspanel" data_type='tabspanel' title={#modal_delete_title#|ucfirst} info_text=true delete_message={#delete_img_message#}}
    {/if}
{/block}
{block name="modal"}{/block}
{block name="foot"}
    {include file="section/footer/editor.tpl"}
    {capture name="vendorsFiles"}{strip}
        libjs/vendor/jquery-ui-1.12.min.js,
        {baseadmin}/template/js/table-form.min.js,
        libjs/vendor/progressBar.min.js,
        plugins/tabspanel/js/admin.min.js
    {/strip}{/capture}
    {capture name="vendors"}{strip}
        /{baseadmin}/min/?f={$smarty.capture.vendorsFiles}
    {/strip}{/capture}
    {script src=$smarty.capture.vendors type="vendors"}
    {capture name="mod_url"}{strip}
        {$smarty.server.SCRIPT_NAME}
        ?controller={$smarty.get.controller}
        {if isset($smarty.get.action)}&action={$smarty.get.action}{/if}
        {if isset($smarty.get.edit)}&edit={$smarty.get.edit}{/if}
        {if isset($smarty.get.tabs)}&tabs={$smarty.get.tabs}{/if}
        {if isset($smarty.get.tab)}&tab={$smarty.get.tab}{/if}
        &plugin={$smarty.get.plugin}&mod_edit={$smarty.get.mod_edit}
    {/strip}{/capture}
    <script type="text/javascript">
        window.addEventListener('load', function() {
            var controller = "{$smarty.capture.mod_url}";
            typeof globalForm === "undefined" ? console.log("globalForm is not defined") : globalForm.run(controller);
            typeof tableForm === "undefined" ? console.log("tableForm is not defined") : tableForm.run(controller);
            typeof tabspanel === "undefined" ? console.log("tabspanel is not defined") : tabspanel.runEdit(globalForm,tableForm,controller);
        });
    </script>
{/block}