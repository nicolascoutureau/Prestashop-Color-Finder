    <div class="panel product-tab">
        <h3>{l s='Ajouter la couleur dominante du produit'}</h3>


        {if isset($image_link) }
            <div class="form-group">
                <div class="col-lg-5 col-lg-offset-3">
                    <h2>Image de cover</h2>
                    <br/>
                    <img src="http://{$image_link}" class="img-responsive" alt=""/>
                    <hr/>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3" for="reference">
                    <span class="label-tooltip" data-toggle="tooltip"
                          title="{l s='Couleur Dominante.'} {l s='Allowed special characters:'} .-_#\">
                        {$bullet_common_field} {l s='Couleurs de l\'image:'}
                    </span>
                </label>
                <div class="colors-container col-lg-5">
                    {foreach from=$product->dominant_colors|unserialize item=color}
                        <div class="color" data-color="{$color}" style="background-color: {$color};"></div>
                    {/foreach}
                    <hr/>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3" for="reference">
                    <span class="label-tooltip" data-toggle="tooltip"
                          title="{l s='Couleur Dominante.'} {l s='Allowed special characters:'} .-_#\">
                        {$bullet_common_field} {l s='Couleur Dominante'}
                    </span>
                </label>
                <div class="col-lg-5">
                    <input id="color1" name="dominant_color" type="color" name="color1" value="{$product->dominant_color|htmlentitiesUTF8}">
                </div>
            </div>
        {else}
            <div class="row">
                <div class="col-lg-5 col-lg-offset-3">
                    <p>Commencez par ajouter une image de cover</p>
                </div>
            </div>
        {/if}

        <div class="panel-footer">
            <a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'}</a>
            <button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save'}</button>
            <button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save and stay'}</button>
        </div>
    </div>

<style>

    .colors-container .color{
        width: 40px;
        height: 40px;
        display: inline-block;
        margin-left: 2px;
        margin-right: 2px;
        margin-bottom: 2px;
        margin-top: 2px;
    }

    .color.selected{
        border: 2px solid rgba(255, 255, 255, 1);
        box-shadow: 2px 2px 8px #888888;
    }
</style>

{*<script type="text/javascript" src="{$base_uri}js/jquery/plugins/jquery.colorpicker.js"></script>*}
<script>
    (function($){

        //Click on Color
        $('.color').on('click', function() {
            var $this = $(this);
            var color = $this.data('color');

            $('#color1').val(color).keyup();

            $('.color').each(function(index) {
                $(this).removeClass('selected');
            });
            $this.addClass('selected');
        });

        //Add selected class to good color if there is one
        var color = $('#color1').val();
        $('.color').each(function() {
            if($(this).data('color') == color){
                $(this).addClass('selected');
                return false;
            }
        });
    })(jQuery)
</script>