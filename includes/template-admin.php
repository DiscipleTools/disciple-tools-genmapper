<div class="wrap">
    <h2><?php esc_attr_e( 'DISCIPLE TOOLS - GENMAPPER', 'disciple-tools-genmapper' ) ?></h2>
    <div class="nav-tab-wrapper">
        <?php foreach ( $tabs as $item ): ?>
            <a href="<?php esc_html( $link . $item['key'] ) ?>"
               class="nav-tab <?php ( $current_tab === $item['key'] ) ? esc_attr( 'nav-tab-active' ) : print ''; ?>"><?php echo esc_html( $item['label'] ) ?></a>
        <?php endforeach ?>
    </div>
    <div id="poststuff">
        <?php
            $tab_object->content();
        ?>
    </div><!--poststuff end -->
</div><!-- wrap end -->

