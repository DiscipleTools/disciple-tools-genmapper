<div id="post-body"
     class="metabox-holder columns-2">
    <div id="post-body-content">
        <form class="columns-2" method="POST">
            <input type="hidden" name="field_add_nonce" value="<?php echo esc_attr( $nonce ) ?>">
            <table class="widefat striped"
                   style="max-width: 700px; margin-bottom: 25px;">
                <thead>
                    <tr>
                        <th><b><?php esc_html_e( 'Church Circles', 'disciple-tools-genmapper' ) ?></b></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <label for="genmapper-health-icons">
                                <b><?php esc_html_e( 'Show health icons in genmap?', 'disciple-tools-genmapper' )?></b>
                            </label>
                        </td>
                        <td>
                            <input type="checkbox" name="dt_genmapper_show_health_icons" <?php if ($show_health_icons): ?>checked<?php endif; ?> id="genmapper-health-icons">
                            <p>
                                <?php esc_html_e( 'Controls if the health metrics icon are shown in the group circles genmap.', 'disciple-tools-genmapper' ) ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="genmapper-health-metrics">
                                <b><?php esc_html_e( 'Show health metrics in genmap?', 'disciple-tools-genmapper' )?></b>
                            </label>
                        </td>
                        <td>
                            <input type="checkbox" name="dt_genmapper_show_health_metrics" <?php if ($show_health_metrics): ?>checked<?php endif; ?> id="genmapper-health-metrics">
                            <p>
                                <?php esc_html_e( 'Controls if there fields are shown/enabled: Believer Count, Baptizer Count and Baptized in Group count. These fields are shown in the member list tile on a group record and over the circle on a group circles genmap.', 'disciple-tools-genmapper' ) ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="genmapper-collapse-health-metrics">
                                <b><?php esc_html_e( 'Collapse group page health metric fields on groups page?', 'disciple-tools-genmapper' )?></b>
                            </label>
                        </td>
                        <td>
                            <input type="checkbox" name="dt_genmapper_collapse_metrics" <?php if ($collapse_health_metrics_fields): ?>checked<?php endif; ?> id="genmapper-collapse-health-metrics">
                            <p>
                                <?php esc_html_e( 'Controls the display style of the count fields in the member list tile on a group record.', 'disciple-tools-genmapper' ) ?>
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input class="button hollow" type="submit" value="Save" />
        </form>
    </div><!-- end post-body-content -->
</div><!-- post-body meta box container -->
