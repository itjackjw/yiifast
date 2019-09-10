<?php
return [
    'helper'=>[
        'class'=>"yiifast\services\Helper",
        'childService' => [
            'errors'=>[
                'class'=>'yiifast\services\helper\Errors'
            ]
        ]
    ]
];