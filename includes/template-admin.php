<div class="wrap">
    <h2><?php esc_attr_e( 'DISCIPLE TOOLS - GENMAPPER', 'disciple-tools-genmapper' ) ?></h2>
    <div class="nav-tab-wrapper">
        <?php foreach ($tabs as $tab): ?>
            <a href="<?php echo $link . $tab['key'] ?>"
               class="nav-tab <?php ( $current_tab === $tab['key'] ) ? esc_attr_e( 'nav-tab-active', 'disciple-tools-genmapper' ) : print ''; ?>"><?php _e( $tab['label'] ) ?></a>
        <?php endforeach ?>
    </div>
    <div id="poststuff">
        <?php
            $tab_object->content();
        ?>
    </div><!--poststuff end -->
</div><!-- wrap end -->

