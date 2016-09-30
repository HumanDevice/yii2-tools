# No inline JS

Every JS code added via `registerJs()` is sent using session to separate file rendered by `JsController`.  
Now `SafeResponse` can be used with CSP set to `*-src: 'self'`.

## Configuration

    return [
        // ...
        'components' => [
            // ...
            'view' => 'common\components\web\View',
            'urlManager' => [
                // ...
                'rules' => [
                    // ...
                    [
                        'pattern' => 'js/<action>',
                        'route' => 'js/<action>',
                        'suffix' => '.js',
                    ],
                ]
            ]
        ],
    ];
