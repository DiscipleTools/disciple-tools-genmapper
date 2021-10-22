<div class="wrap">
    <h2><?php esc_attr_e('DISCIPLE TOOLS - GENMAPPER', 'disciple-tools-genmapper') ?></h2>
    <div id="poststuff">
        <div id="post-body"
             class="metabox-holder columns-2">
            <div id="post-body-content">
                <?php foreach($iconGroups as $name => $icons): ?>
                    <table class="widefat striped" style="max-width: 400px; margin-bottom: 25px;">
                        <thead>
                        <tr>
                            <th><?php echo strtoupper(_e($name, "dt_genmapper" )); ?></th>
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
                                    <form style="display: flex; align-items: center" method="POST" class="dt-genmapper__icon-form" data-icon-default="<?php echo $icon['default'] ?>" id="icon-form--<?php echo $icon['option'] ?>">
                                        <img src="<?php echo $icon['url'] ?>" width="50" height="50" style="margin-right: 25px;"  class="dt-genmapper__icon-image"/>
                                        <input type="hidden" class="dt-genmapper__icon-url-field" name="value" value="<?php echo $icon['value'] ?>"/>
                                        <input type="hidden" name="icon" value="<?php echo $icon['option'] ?>" />
                                        <a class="dt-genmapper__icon-upload-button button"><?php esc_html_e( 'replace', 'disciple-tools-genmapper' ); ?></a>
                                        <a class="dt-genmapper__icon-reset-button button"><?php esc_html_e( 'reset', 'disciple-tools-genmapper' ); ?></a>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        </tbody>
                    </table>
                <?php endforeach; ?>
            </div><!-- end post-body-content -->
        </div><!-- post-body meta box container -->
    </div><!--poststuff end -->
</div><!-- wrap end -->

