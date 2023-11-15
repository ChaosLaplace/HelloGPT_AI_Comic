<?php
return [
    // （選配）文檔標題，顯示在左上角與首頁
    'title'              => 'Apidoc',
    // （選配）文檔描述，顯示在首頁
    'desc'               => '',
    // （必須）設置文檔的應用/版本
    'apps'           => [
        [
            // （必須）標題
            'title' => 'Api接口',
            // （必須）控制器目錄地址
            'path' => 'app/Http/Controllers',
            // （必須）唯一的key
            'key' => 'api',
        ]
    ],

    // （必須）指定通用註釋定義的文件地址
    'definitions'        => "app\common\controller\Definitions",
    // （必須）自動生成url規則，當接口不添加@Apidoc\Url ("xxx")註解時，使用以下規則自動生成
    'auto_url' => [
        // 字母規則，lcfirst=首字母小寫；ucfirst=首字母大寫；
        'letter_rule' => "lcfirst",
        // url前綴
        'prefix' => "",
    ],
    // 是否自動註冊路由
    'auto_register_routes' => false,
    // （必須）緩存配置
    'cache'              => [
        // 是否開啟緩存
        'enable' => false,
    ],
    // （必須）權限認證配置
    'auth'               => [
        // 是否啟用密碼驗證
        'enable'     => false,
        // 全局訪問密碼
        'password'   => "123456",
        // 密碼加密鹽
        'secret_key' => "apidoc#hg_code",
        // 授權訪問後的有效期
        'expire' => 24 * 60 * 60
    ],
    // 全局參數
    'params' => [
        // （選配）全局的請求Header
        'header' => [
            // name=字段名，type=字段類型，require=是否必須，default=默認值，desc=字段描述
            ['name' => 'Authorization', 'type' => 'string', 'require' => true, 'desc' => '身份令牌 Token'],
        ],
        // （選配）全局的請求Query
        'query' => [
            // 同上 header
        ],
        // （選配）全局的請求Body
        'body' => [
            // 同上 header
        ],
    ],
    // 全局響應體
    'responses' => [
        // 成功響應體
        'success' => [
        ],
        // 異常響應體
        'error' => [
            
        ]
    ],
    // （選配）apidoc路由前綴,默認apidoc
    'route_prefix' => '/apidoc',
    //（選配）默認作者
    'default_author' => '',
    //（選配）默認請求類型
    'default_method' => 'GET',
    //（選配）允許跨域訪問
    'allowCrossDomain' => false,
    /**
     * （選配）解析時忽略帶@註解的關鍵詞，當註解中存在帶@字符並且非Apidoc註解，如 @key test，此時Apidoc頁面報類似以下錯誤時:
     * [Semantical Error] The annotation "@key" in method xxx() was never imported. Did you maybe forget to add a "use" statement for this annotation?
     */
    'ignored_annitation' => [],

    // （選配）數據庫配置
    'database' => [],
    // （選配）Markdown文檔
    'docs'              => [],
    // （選配）代碼生成器配置 註意：是一個二維數組
    'generator' => [],
    // （選配）代碼模板
    'code_template' => [],
    // （選配）接口分享功能
    'share' => [
        // 是否開啟接口分享功能
        'enable' => false,
        // 自定義接口分享操作，二維數組，每個配置為一個按鈕操作
        'actions' => []
    ],
];
