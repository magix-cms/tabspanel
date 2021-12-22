<div class="row">
    <form id="edit_banner" action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;tabs=banner&amp;action={if !$edit}add{else}edit{/if}" method="post" class="validate_form{if !$edit} add_form collapse in{else} edit_form{/if} col-ph-12 col-sm-8 col-md-6">
        <div id="drop-zone"{if !isset($banner.img_banner) || empty($banner.img_banner)} class="no-img"{/if}>
            <div id="drop-buttons" class="form-group">
                <label id="clickHere" class="btn btn-default">
                    ou cliquez ici.. <span class="fa fa-upload"></span>
                    <input type="hidden" name="MAX_FILE_SIZE" value="4048576" />
                    <input type="file" id="img" name="img" />
                    <input type="hidden" id="id_product" name="id" value="{$banner.id_banner}">
                </label>
            </div>
            <div class="preview-img">
                {if isset($banner.img_banner) && !empty($banner.img_banner)}
                    <img id="preview" src="/upload/banner/{$banner.id_banner}/{$banner.img_banner}" alt="banner" class="preview img-responsive" />
                {else}
                    <img id="preview" src="#" alt="Déposez votre images ici..." class="no-img img-responsive" />
                {/if}
            </div>
        </div>
        {include file="language/brick/dropdown-lang.tpl"}
        <div class="row">
            <div class="col-ph-12">
                <div class="tab-content">
                    {foreach $langs as $id => $iso}
                        <div role="tabpanel" class="tab-pane{if $iso@first} active{/if}" id="lang-{$id}">
                            <fieldset>
                                <legend>Texte</legend>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-8 col-md-9 col-lg-10">
                                        <div class="form-group">
                                            <label for="title_banner_{$id}">{#title_banner#|ucfirst} :</label>
                                            <input type="text" class="form-control" id="title_banner_{$id}" name="banner[content][{$id}][title_banner]" value="{$banner.content[{$id}].title_banner}" />
                                        </div>
                                        <div class="form-group">
                                            <label for="desc_banner_{$id}">{#desc_banner#|ucfirst} :</label>
                                            <textarea class="form-control" id="desc_banner_{$id}" name="banner[content][{$id}][desc_banner]" cols="65" rows="3">{$banner.content[{$id}].desc_banner}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-4 col-md-3 col-lg-2">
                                        <div class="form-group">
                                            <label for="published_banner_{$id}">Statut</label>
                                            <input id="published_banner_{$id}" data-toggle="toggle" type="checkbox" name="banner[content][{$id}][published_banner]" data-on="Publiée" data-off="Brouillon" data-onstyle="success" data-offstyle="danger"{if (!isset($banner) && $iso@first) || $banner.content[{$id}].published_banner} checked{/if}>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend>Options</legend>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12">
                                        <div class="form-group">
                                            <label for="url_banner_{$id}">{#url_banner#|ucfirst} :</label>
                                            <input type="text" class="form-control" id="url_banner_{$id}" name="banner[content][{$id}][url_banner]" value="{$banner.content[{$id}].url_banner}" size="50" />
                                        </div>
                                        <div class="form-group">
                                            <label for="blank_banner_{$id}">{#blank_banner#|ucfirst}</label>
                                            <div class="switch">
                                                <input type="checkbox" id="blank_banner_{$id}" name="banner[content][{$id}][blank_banner]" class="switch-native-control"{if $banner.content[{$id}].blank_banner} checked{/if} />
                                                <div class="switch-bg">
                                                    <div class="switch-knob"></div>
                                                </div>
                                            </div>
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
            <legend>Enregistrer</legend>
            {if $edit}
                <input type="hidden" name="banner[id]" value="{$banner.id_banner}" />
            {/if}
            <button class="btn btn-main-theme" type="submit" name="action" value="edit">{#save#|ucfirst}</button>
        </fieldset>
    </form>
</div>