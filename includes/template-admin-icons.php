<div id="post-body"
     class="metabox-holder columns-2">
    <div id="post-body-content">
        <div class="columns-2">
            <?php foreach ($icon_groups as $name => $icons): ?>
                <table class="widefat striped"
                       style="max-width: 400px; margin-bottom: 25px;">
                    <thead>
                    <tr>
                        <th><?php echo esc_html( ucfirst( $name ) ); ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($icons as $icon): ?>
                        <tr>
                            <td>
                                <strong><?php echo esc_html( $icon['label'] ); ?></strong>
                            </td>
                            <td>
                                <form style="display: flex; align-items: center"
                                      method="POST"
                                      class="dt-genmapper__icon-form"
                                      data-icon-default="<?php echo esc_attr( $icon['default'] ) ?>"
                                      id="icon-form--<?php echo esc_attr( $icon['option'] ) ?>">
                                    <img src="<?php echo esc_attr( $icon['url'] ) ?>"
                                         width="50"
                                         height="50"
                                         style="margin-right: 25px;"
                                         class="dt-genmapper__icon-image"/>
                                    <input type="hidden"
                                           class="dt-genmapper__icon-url-field"
                                           name="value"
                                           value="<?php echo esc_attr( $icon['url'] ) ?>"/>
                                    <input type="hidden"
                                           name="icon"
                                           value="<?php echo esc_attr( $icon['option'] ) ?>"/>
                                    <a class="dt-genmapper__icon-upload-button button"><?php esc_html_e( 'replace', 'disciple-tools-genmapper' ); ?></a>
                                    <a class="dt-genmapper__icon-reset-button button"><?php esc_html_e( 'reset', 'disciple-tools-genmapper' ); ?></a>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>
            <?php endforeach; ?>
        </div>
    </div><!-- end post-body-content -->
</div><!-- post-body meta box container -->
