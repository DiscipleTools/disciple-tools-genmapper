<?php if ($collapse_fields): ?>
    <div id="show-metrics-fields" style="text-align: center; background-color: rgb(236, 245, 252); margin: 3px -15px -15px; border-radius: 0px 0px 10px 10px; display: block;">
        <a class="button clear " style="margin:0;padding:3px 0; width:100%">
            <?php echo esc_html__( 'Show all fields', 'disciple-tools-genmapper' )?>
        </a>
    </div>
<?php endif; ?>
<div id="metrics-extra-fields" style="display: <?php echo $collapse_fields ? "none" : "block"; ?>;">
    <?php foreach ($fields as $key => $options): ?>
        <?php render_field_for_display( $key, $fields, $post, true ); ?>
    <?php endforeach; ?>
    <div class="clear"></div>
</div>

