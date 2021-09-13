<div class="wrap">
    <h2><?php esc_attr_e('DISCIPLE TOOLS - GENMAPPER', 'disciple-tools-genmapper') ?></h2>
    <div id="poststuff">
        <div id="post-body"
             class="metabox-holder columns-2">
            <div id="post-body-content">
                <table class="widefat striped" style="max-width: 400px;">
                    <thead>
                        <tr>
                            <th>Icons</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($icons as $icon): ?>
                            <tr>
                                <td>
                                    <strong><?php esc_html_e( $icon['label'], 'disciple-tools-genmapper' ); ?></strong>
                                </td>
                                <td>
                                    <div class="upload" style="display: flex; align-items: center">
                                        <img src="<?php echo $icon['value'] ?>" width="50" height="50" style="margin-right: 25px;"/>
                                        <div>
                                            <input type="hidden" name="<?php $icon['name'] ?>" id="<?php $icon['name'] ?>" value="<?php echo $icon['value'] ?>"/>
                                            <button type="submit" class="dt_genmapper_upload_image_button button"><?php esc_html_e( 'upload', 'disciple-tools-genmapper' ); ?></button>
                                            <button type="submit" class="dt_genmapper_remove_image_button button">&times;</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>

            </div><!-- end post-body-content -->
        </div><!-- post-body meta box container -->
    </div><!--poststuff end -->
</div><!-- wrap end -->

