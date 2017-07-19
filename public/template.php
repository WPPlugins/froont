<?php
$froont_meta = get_post_meta($post->ID, 'froont', true);
$wp_upload_dir = wp_upload_dir();
$full_path = $wp_upload_dir['basedir'] . '/froont/' . $froont_meta['date'] . '/froont-page/index.html';

echo Froont_Public::output_template($full_path);
