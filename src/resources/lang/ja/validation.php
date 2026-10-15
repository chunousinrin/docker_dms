<?php

return [
    'required' => ':attributeは必須です。',
    'unique'   => 'この:attributeはすでに使用されています。',
    'date'     => ':attributeは正しい日付形式で入力してください。',

    'attributes' => [
        'classificationId' => '分類',
        'CreatedDate'      => '発行日',
        'BillNumber'       => '請求書番号',
        'Customer'        => '顧客名',
        'CustomerAdd'     => '顧客宛名',
        'BizName'         => '事業名',
        'items.*.Summary'  => '摘要',
        // 他の項目もここに追加
    ],
];
