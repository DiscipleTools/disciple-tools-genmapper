<div id="post-body"
     class="metabox-holder columns-2">
    <div id="post-body-content">
        <form class="columns-2" method="POST">
            <input type="hidden" name="field_add_nonce" value="<?php _e($nonce) ?>">
            <table class="widefat striped"
                   style="max-width: 400px; margin-bottom: 25px;">
                <thead>
                    <tr>
                        <th><?php esc_attr_e( 'Church Circles', 'disciple-tools-genmapper' ) ?></b></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <label for="genmapper-health-icons">
                                <?php echo _e( 'Show health icons?', 'disciple-tools-genmapper' )?>
                            </label>
                        </td>
                        <td>
                            <input type="checkbox" name="dt_genmapper_show_health_icons" <?php if ($show_health_icons): ?>checked<?php endif; ?> id="genmapper-health-icons">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="genmapper-health-metrics">
                                <?php echo _e( 'Show health metrics?', 'disciple-tools-genmapper' )?>
                            </label>
                        </td>
                        <td>
                            <input type="checkbox" name="dt_genmapper_show_health_metrics" <?php if ($show_health_metrics): ?>checked<?php endif; ?> id="genmapper-health-metrics">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="genmapper-collapse-health-metrics">
                                <?php echo _e( 'Collapse group page health metric fields?', 'disciple-tools-genmapper' )?>
                            </label>
                        </td>
                        <td>
                            <input type="checkbox" name="dt_genmapper_collapse_metrics" <?php if ($collapse_health_metrics_fields): ?>checked<?php endif; ?> id="genmapper-collapse-health-metrics">
                        </td>
                    </tr>
                </tbody>
            </table>
            <input class="button hollow" type="submit" value="Save" />
        </form>
    </div><!-- end post-body-content -->
</div><!-- post-body meta box container -->
