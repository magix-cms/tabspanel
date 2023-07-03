<div class="row">
    <form id="edit_tabspanel" action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;action=edit&amp;edit={$smarty.get.edit}&amp;plugin={$smarty.get.plugin}&amp;mod_action={if !$edit}add{else}edit&amp;mod_edit={$smarty.get.mod_edit}{/if}" method="post" class="validate_form{if !$edit} add_form collapse in{else} edit_form{/if} col-ph-12 col-sm-12 col-md-12">
        {include file="language/brick/dropdown-lang.tpl"}
        <div class="row">
            <div class="col-ph-12">
                <div class="tab-content">
                    {foreach $langs as $id => $iso}
                        <div role="tabpanel" class="tab-pane{if $iso@first} active{/if}" id="lang-{$id}">
                            <fieldset>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-10">
                                        <div class="form-group">
                                            <label for="tab_id_tp_{$id}">{#id_banner#|ucfirst} :</label>
                                            <input type="text" class="form-control" id="tab_id_tp_{$id}" name="content[{$id}][tab_id_tp]" value="{$tabspanel.content[{$id}].id_banner}" />
                                        </div>
                                        <div class="form-group">
                                            <label for="title_tabspanel_{$id}">{#title_tp#|ucfirst} :</label>
                                            <input type="text" class="form-control" id="title_tabspanel_{$id}" name="content[{$id}][title_tp]" value="{$tabspanel.content[{$id}].title_tp}" />
                                        </div>
                                        <div class="form-group">
                                            <label for="desc_tabspanel_{$id}">{#desc_tp#|ucfirst} :</label>
                                            <textarea id="desc_tabspanel_{$id}" name="content[{$id}][desc_tp]" class="form-control mceEditor">{call name=cleantextarea field=$tabspanel.content[{$id}].desc_tp}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-4 col-md-3 col-lg-2">
                                        <div class="form-group">
                                            <label for="published_tabspanel_{$id}">Statut</label>
                                            <input id="published_tabspanel_{$id}" data-toggle="toggle" type="checkbox" name="content[{$id}][published_tp]" data-on="Publiée" data-off="Brouillon" data-onstyle="success" data-offstyle="danger"{if (!isset($tabspanel) && $iso@first) || $tabspanel.content[{$id}].published_tp} checked{/if}>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
        <fieldset>
            <button class="btn btn-main-theme" type="submit" name="action" value="edit">{#save#|ucfirst}</button>
        </fieldset>
    </form>
</div>
{if $edit}
    <br />
    <h3>Gestion des images</h3>
{include file="mod/img.tpl"}
{/if}