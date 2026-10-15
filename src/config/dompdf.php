<?php

// config/dompdf.php などの設定ファイルとして使う場合
return [
    'show_warnings' => false,
    'orientation'   => 'portrait',
    'defines' => [
        // こちらにオプションをまとめる（パッケージによって構造が異なる場合があります）
        'isRemoteEnabled' => true,
        'font_dir'        => storage_path('fonts/'),
        'font_cache'      => storage_path('fonts/'),
        'default_font'    => 'NotoSansJP-Regular',
        'chroot'          => base_path(),
    ]
];
