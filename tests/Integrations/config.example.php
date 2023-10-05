<?php

return [
    'bitrix24_webhook_endpoint' => 'https://**HOME**.bitrix24.ru/rest/*USERID*/***CODE***',
    'bitrix24_webhook_code' => '**CODE**',
    'deal'=>[
        'fieldCodes' => [
            //Example: name fields to code
            'city' => "UF_CRM_**",
            'order_id' => "UF_CRM_**",
            'payment_type' => "UF_CRM_**",
            'delivery_type' => "UF_CRM_**",
            'delivery_payer' => "UF_CRM_**",
        ],
        'defaultFields' => [
            //Example: default properties deal
            'delivery_payer' => '122',
            'currency' => 'RUB',
            'source_id' => '9',
            'stage_id' => '2',
            'category_id' => '0',
        ],
        'stage'=>[
            //Example: deal stage
            'new' => 'NEW',
            'pending' => '2',
            'prepayment' => '3',
            'paid' => '12',
            'build' => '7',
            'ready_for_delivery' => '11',
            'delivered' => '10',
            'delivered_with_payment' => '4',
            'waiting_for_payment' => '8',
            'success' => 'WON',
            'lose' => 'LOSE'
        ]
    ],
    'product' => [
        'fieldCodes' => [
            //Example: name fields to code
            'article' => "PROPERTY_**",
        ],
    ]
];