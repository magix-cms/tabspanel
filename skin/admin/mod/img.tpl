<div class="row">
    <div class="col-ph-12">
        {include file="section/form/progressBar.tpl"}
    </div>
    <form id="add_img_tabspanel" action="{$smarty.server.SCRIPT_NAME}?controller={$smarty.get.controller}&amp;action=edit&amp;edit={$smarty.get.edit}&amp;plugin={$smarty.get.plugin}&amp;mod_action={if !$edit}add{else}edit{/if}" method="post" enctype="multipart/form-data" class="form-gen col-ph-12">
        {*<div id="drop-zone">
            Déposez vos images ici...
            <div id="drop-buttons" class="form-group">
                <label id="clickHere" class="btn btn-default">
                    ou cliquez ici.. <span class="fa fa-upload"></span>
                    <input type="hidden" name="MAX_FILE_SIZE" value="4048576" />
                    <input type="file" id="img_multiple" name="img_multiple[]" value="" multiple />
                    <input type="hidden" id="id_tp" name="id" value="{$tabspanel.id_tp}">
                </label>
                <button class="btn btn-main-theme" type="submit" name="action" value="img" disabled>{#send#|ucfirst}</button>
            </div>
        </div>*}
        <div class="dropzone multi-img-drop">
            {*Déposez vos images ici...*}
            <div class="drop-buttons form-group">
                <div class="drop-text">{#drop_imgs_here#}</div>
                <label class="btn btn-default" for="imgs">ou cliquez ici.. <span class="fa fa-upload"></span></label>
                <button class="btn btn-main-theme" type="submit" name="action" value="img" disabled>{#send#|ucfirst}</button>
            </div>
            <input type="hidden" name="MAX_FILE_SIZE" value="4048576" />
            <input type="hidden" id="id_tp" name="id" value="{$tabspanel.id_tp}">
            <input type="file" accept="image/*" id="imgs" name="img_multiple[]" value="" multiple />
        </div>
    </form>
</div>