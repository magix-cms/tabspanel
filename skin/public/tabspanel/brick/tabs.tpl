{if is_array($data) && !empty($data)}
<div class="display">
    <div class="container">
        <p class="h2">{#format_available#}</p>
        <div class="tabpanels">
            <ul class="nav nav-tabs" role="tablist">
                {foreach $data as $key => $value}
                    {if isset($value.imgs) && is_array($value.imgs) && count($value.imgs) > 0}
                    <li {if $value@first}class="active"{/if}>
                        <a role="tab" href="#tab{$value@iteration}" aria-controls="tab{$value@iteration}" data-toggle="tab">
                            {foreach $value.imgs as $k => $item}
                                {if $item.default}
                                {include file="img/img.tpl" img=$item.img lazy=true size='small'}
                                {/if}
                            {/foreach}
                            <span>{$value.name|ucfirst}</span>
                        </a>
                    </li>
                    {/if}
                {/foreach}
            </ul>
            <div id="colors" class="tab-content">
                {foreach $data as $key => $value}
                    {if isset($value.imgs) && is_array($value.imgs) && count($value.imgs) > 0}
                    <section id="tab{$value@iteration}" class="tab-pane{if $value@first} active{/if}" role="tabpanel">
                        <div class="row">
                            <div class="col-12 col-sm-4">
                                <p class="h4">{$value.name|ucfirst}</p>
                                {$value.content}
                            </div>
                            <div class="col-12 col-sm-8">
                                <p class="h4">Exemples</p>
                                <div class="tabs-img-gallery">
                                    {foreach $value.imgs as $k => $item}
                                    <div class="col-6 col-xs-4 col-md-3">
                                        <a class="img-gallery" href="{$item.img.large.src}" title="{$value.name|ucfirst} {$item@iteration}" itemprop="image" itemscope itemtype="http://schema.org/ImageObject">
                                            <meta itemprop="contentUrl" content="{$item.img.small.src}" />
                                            <span itemprop="thumbnail" itemscope itemtype="http://schema.org/ImageObject">
                                            {include file="img/img.tpl" img=$item.img lazy=true size='small'}
                                            </span>
                                        </a>
                                    </div>
                                    {/foreach}
                                </div>
                            </div>
                        </div>
                    </section>
                    {/if}
                {/foreach}
            </div>
        </div>
    </div>
</div>
{/if}