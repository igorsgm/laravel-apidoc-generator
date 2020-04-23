<?php

return [

    /*
     * The type of documentation output to generate.
     * - "static" will generate a static HTMl page in the /public/docs folder,
     * - "laravel" will generate the documentation as a Blade view,
     * so you can add routing and authentication.
     */
    'type' => 'static',

    /*
     * Settings for `laravel` type output.
     */
    'laravel' => [
        /*
         * Whether to automatically create a docs endpoint for you to view your generated docs.
         * If this is false, you can still set up routing manually.
         */
        'autoload' => false,

        /*
         * URL path to use for the docs endpoint (if `autoload` is true).
         *
         * By default, `/doc` opens the HTML page, and `/doc.json` downloads the Postman collection.
         */
        'docs_url' => '/doc',

        /*
         * Middleware to attach to the docs endpoint (if `autoload` is true).
         */
        'middleware' => [],
    ],

    /*
     * The router to be used (Laravel or Dingo).
     */
    'router' => 'laravel',

    /*
     * The base URL to be used in examples and the Postman collection.
     * By default, this will be the value of config('app.url').
     */
    'base_url' => '{{STREAMLABS_URL}}',

    'docs' => [
        'writeMarkdownAndSourceFiles' => false,
        'writeHtmlDocs' => false,
    ],

    /*
     * Generate a Postman collection in addition to HTML docs.
     * For 'static' docs, the collection will be generated to public/docs/collection.json.
     * For 'laravel' docs, it will be generated to storage/app/apidoc/collection.json.
     * The `ApiDoc::routes()` helper will add routes for both the HTML and the Postman collection.
     */
    'postman' => [
        /*
         * Specify whether the Postman collection should be generated.
         */
        'enabled' => true,

        /*
         * The name for the exported Postman collection. Default: config('app.name')." API"
         */
        'name' => null,

        /*
         * The description for the exported Postman collection.
         */
        'description' => null,

        /*
         * The "Auth" section that should appear in the postman collection. See the schema docs for more information:
         * https://schema.getpostman.com/json/collection/v2.0.0/docs/index.html
         */
        'auth' => null,

        /*
         * Specify rules to be applied to all the postman routes in this group when generating documentation
         */
        'apply' => [
            /*
             * Specify headers to be added to the postman requests
             */
            'headers' => [
                'Accept' => 'application/json',
                'Referer' => '{{STREAMLABS_URL}}/dashboard',
                'Cookie' => '{{STREAMLABS_COOKIE}}',
                // 'Authorization' => 'Bearer {token}',
                // 'Api-Version' => 'v2',
            ],

            /*
             * Query parameters which should be sent with the API call.
             */
            'queryParams' => [
                'XDEBUG_SESSION_START' => [
                    'value' => '{{XDEBUG_SESSION_START}}',
                    'disabled' => true,
                ],
                'token' => [
                    'value' => '{{USER_TOKEN}}',
                    'disabled' => true,
                ],
            ],
        ],

        /*
         * Url parameters which should be using a specific value.
         */
        'urlParamsMap' => [
            'token' => '{{USER_TOKEN}}',
            'username' => '{{USERNAME}}',
            'userId' => '{{LARAVEL_USER_ID}}',
            'articleId' => '{{PARAM_ARTICLE_ID}}',
            'clientId' => '{{PARAM_CLIENT_ID}}',
            'email' => '{{PARAM_EMAIL}}',
            'platform' => '{{PARAM_PLATFORM}}',
            'profileId' => '{{PARAM_PROFILE_ID}}',
            'profile' => '{{PARAM_PROFILE_ID}}',
            'hash' => '{{PARAM_HASH}}',
            'id' => '{{PARAM_ID}}',
            'transactionId' => '{{PARAM_TRANSACTION_ID}}',
            'subscriptionId' => '{{PARAM_SUBSCRIPTION_ID}}',
            'streamerId' => '{{PARAM_STREAMER_ID}}',
            'itemId' => '{{PARAM_ITEM_ID}}',
            'channelId' => '{{PARAM_CHANNEL_ID}}',
            'type' => '{{PARAM_TYPE}}',
            'template' => '{{PARAM_TEMPLATE}}',
            'uuid' => '{{PARAM_UUID}}',
            'path' => '{{PARAM_PATH}}',
            'search' => '{{PARAM_SEARCH}}',
            'giveawayId' => '{{PARAM_GIVE_AWAY_ID}}',
            'viewerId' => '{{PARAM_VIEWER_ID}}',
            'configurationKey' => '{{PARAM_CONFIGURATION_KEY}}',
            'referrer' => '{{PARAM_REFERRER}}',
            'a' => '{{PARAM_A}}',
            'featureFlag' => '{{PARAM_FEATURE_FLAG}}',
            'twitchId' => '{{TWITCH_ID}}',
            'countType' => '{{PARAM_COUNT_TYPE}}',
            'period' => '{{PARAM_PERIOD}}',
            'module_slug' => '{{PARAM_MODULE_SLUG}}',
            'boardId' => '{{PARAM_BOARD_ID}}',
            'x' => '{{PARAM_X}}',
            'y' => '{{PARAM_Y}}',
            'disputeId' => '{{PARAM_DISPUTE_ID}}',
            'orderId' => '{{PARAM_ORDER_ID}}',
            'broadcastId' => '{{PERISCOPE_BROADCAST_ID}}',
            'chatToken' => '{{PERISCOPE_CHAT_TOKEN}}',
            'idx' => '{{PARAM_IDX}}',
            'promoType' => '{{PARAM_PROMOTYPE}}',
            'redeemCode' => '{{PARAM_REDEEM_CODE}}',
            'sku' => '{{PARAM_SKU}}',
            'game' => '{{PARAM_GAME}}',
            'month' => '{{PARAM_MONTH}}',
            'route' => '{{PARAM_ROUTE}}',
            'dealId' => '{{PARAM_DEAL_ID}}',
            'broadcaster' => '{{PARAM_BROADCASTER}}',
            'attachmentId' => '{{PARAM_ATTACHMENT_ID}}',
            'name' => '{{PARAM_NAME}}',
            'tab' => '{{PARAM_USER_TAB}}',
            'secret' => '{{PARAM_SECRET}}',
            'charity_name' => '{{PARAM_CHARITY_NAME}}',
            'slug' => '{{PARAM_CHARITY_SLUG}}',
            'campaign_id' => '{{PARAM_CHARITY_CAMPAIGN_ID}}',
            'charity_id' => '{{PARAM_CHARITY_ID}}',
            'streamer_campaign_id' => '{{PARAM_CHARITY_STREAMER_CAMPAIGN_ID}}',
            'team_slug' => '{{PARAM_CHARITY_TEAM_SLUG}}',
            'team_id' => '{{PARAM_CHARITY_TEAM_ID}}',
            'member_id' => '{{PARAM_CHARITY_MEMBER_ID}}',
            'importStreamElements' => '{{PARAM_IMPORT_STREAM_ELEMENTS}}',
            'orderCode' => '{{PARAM_MERCH_ORDER_CODE}}',
            'droopshipId' => '{{PARAM_MERCH_DROOPSHIP_ID}}',
            'productId' => '{{PARAM_MERCH_PRODUCT_ID}}',
            'mappingId' => '{{PARAM_MERCH_MAPPING_ID}}',
            'dropshipId' => '{{PARAM_MERCH_DROPSHIP_ID}}',
            'taskKey' => '{{PARAM_MERCH_TASK_KEY}}',
            'giftId' => '{{PARAM_GIFT_ID}}',
        ],
    ],

    /*
     * The routes for which documentation should be generated.
     * Each group contains rules defining which routes should be included ('match', 'include' and 'exclude' sections)
     * and rules which should be applied to them ('apply' section).
     */
    'routes' => [
        [
            /*
             * 'group' is a list of custom fields added by @igorsgm
             */
            'group' => [

                /*
                 * The type of grouping for the routes for Postman
                 * - "controller" will generate the documentation grouped in folders by Controller Names,
                 * - "permission" will generate the documentation grouped in folders by Permission Middleware and Controller Names
                 * (if you set 'permission', you must set the 'permission_middleware' property bellow.
                 */
                'type' => 'permission',

                /*
                 * The name of the permission middleware that should be used to group the routes.
                 * This one will just be considered if you set 'permission' in the field 'type' right above.
                 */
                'permission_middleware' => 'shared-permission',

                'permissions_map' => [
                    "analytics" => "[Role] Analytics",
                    "any" => "Any",
                    "charity" => "[Role] Charity",
                    "cloudbot" => "[Role] Cloudbot",
                    "donation_history" => "[Role] Donation History",
                    "grow" => "[Role] Grow",
                    "media_share" => "[Role] Media Share",
                    "merch_store" => "[Role] Merch Store",
                    "prime" => "[Role] Prime",
                    "recent_events" => "[Role] Recent Events",
                    "settings" => "[Role] Settings",
                    "subscriber_history" => "[Role] Subscriber History",
                    "widgets" => "[Role] Widgets",
                ]
            ],
            /*
             * Specify conditions to determine what routes will be parsed in this group.
             * A route must fulfill ALL conditions to pass.
             */
            'match' => [

                /*
                 * Match only routes whose domains match this pattern (use * as a wildcard to match any characters).
                 */
                'domains' => [
                    '*',
                    // 'domain1.*',
                ],

                /*
                 * Match only routes whose paths match this pattern (use * as a wildcard to match any characters).
                 */
                'prefixes' => [
                    '*',
                    // 'users/*',
                ],

                /*
                 * Match only routes registered under this version. This option is ignored for Laravel router.
                 * Note that wildcards are not supported.
                 */
                'versions' => [
                    'v1',
                ],
            ],

            /*
             * Include these routes when generating documentation,
             * even if they did not match the rules above.
             * Note that the route must be referenced by name here (wildcards are supported).
             */
            'include' => [
                // 'users.index', 'healthcheck*'
            ],

            /*
             * Exclude these routes when generating documentation,
             * even if they matched the rules above.
             * Note that the route must be referenced by name here (wildcards are supported).
             */
            'exclude' => [
                // 'users.create', 'admin.*'
            ],

            /*
             * Specify rules to be applied to all the routes in this group when generating documentation
             */
            'apply' => [
                /*
                 * Specify headers to be added to the example requests
                 */
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    // 'Authorization' => 'Bearer {token}',
                    // 'Api-Version' => 'v2',
                ],

                /*
                 * If no @response or @transformer declarations are found for the route,
                 * we'll try to get a sample response by attempting an API call.
                 * Configure the settings for the API call here.
                 */
                'response_calls' => [
                    /*
                     * API calls will be made only for routes in this group matching these HTTP methods (GET, POST, etc).
                     * List the methods here or use '*' to mean all methods. Leave empty to disable API calls.
                     */
                    'methods' => ['GET'],

                    /*
                     * Laravel config variables which should be set for the API call.
                     * This is a good place to ensure that notifications, emails
                     * and other external services are not triggered
                     * during the documentation API calls
                     */
                    'config' => [
                        'app.env' => 'documentation',
                        'app.debug' => false,
                        // 'service.key' => 'value',
                    ],

                    /*
                     * Cookies which should be sent with the API call.
                     */
                    'cookies' => [
                        // 'name' => 'value'
                    ],

                    /*
                     * Query parameters which should be sent with the API call.
                     */
                    'queryParams' => [
                        // 'key' => 'value',
                    ],

                    /*
                     * Body parameters which should be sent with the API call.
                     */
                    'bodyParams' => [
                        // 'key' => 'value',
                    ],
                ],
            ],
        ],
    ],

    'strategies' => [
        'metadata' => [
            \Mpociot\ApiDoc\Extracting\Strategies\Metadata\GetFromDocBlocks::class,
        ],
        'urlParameters' => [
            \Mpociot\ApiDoc\Extracting\Strategies\UrlParameters\GetFromUrlParamTag::class,
            \Mpociot\ApiDoc\Extracting\Strategies\UrlParameters\GetFromUrlString::class,
        ],
        'queryParameters' => [
            \Mpociot\ApiDoc\Extracting\Strategies\QueryParameters\GetFromQueryParamTag::class,
        ],
        'headers' => [
            \Mpociot\ApiDoc\Extracting\Strategies\RequestHeaders\GetFromRouteRules::class,
        ],
        'bodyParameters' => [
            \Mpociot\ApiDoc\Extracting\Strategies\BodyParameters\GetFromBodyParamTag::class,
        ],
        'responses' => [
            \Mpociot\ApiDoc\Extracting\Strategies\Responses\UseTransformerTags::class,
            \Mpociot\ApiDoc\Extracting\Strategies\Responses\UseResponseTag::class,
            \Mpociot\ApiDoc\Extracting\Strategies\Responses\UseResponseFileTag::class,
            \Mpociot\ApiDoc\Extracting\Strategies\Responses\UseApiResourceTags::class,
            \Mpociot\ApiDoc\Extracting\Strategies\Responses\ResponseCalls::class,
        ],
    ],

    /*
     * Custom logo path. The logo will be copied from this location
     * during the generate process. Set this to false to use the default logo.
     *
     * Change to an absolute path to use your custom logo. For example:
     * 'logo' => resource_path('views') . '/api/logo.png'
     *
     * If you want to use this, please be aware of the following rules:
     * - the image size must be 230 x 52
     */
    'logo' => false,

    /*
     * Name for the group of routes which do not have a @group set.
     */
    'default_group' => 'general',

    /*
     * Example requests for each endpoint will be shown in each of these languages.
     * Supported options are: bash, javascript, php, python
     * You can add a language of your own, but you must publish the package's views
     * and define a corresponding view for it in the partials/example-requests directory.
     * See https://laravel-apidoc-generator.readthedocs.io/en/latest/generating-documentation.html
     *
     */
    'example_languages' => [
        'php',
        'javascript',
        'bash',
    ],

    /*
     * Configure how responses are transformed using @transformer and @transformerCollection
     * Requires league/fractal package: composer require league/fractal
     *
     */
    'fractal' => [
        /* If you are using a custom serializer with league/fractal,
         * you can specify it here.
         *
         * Serializers included with league/fractal:
         * - \League\Fractal\Serializer\ArraySerializer::class
         * - \League\Fractal\Serializer\DataArraySerializer::class
         * - \League\Fractal\Serializer\JsonApiSerializer::class
         *
         * Leave as null to use no serializer or return a simple JSON.
         */
        'serializer' => null,
    ],

    /*
     * If you would like the package to generate the same example values for parameters on each run,
     * set this to any number (eg. 1234)
     *
     */
    'faker_seed' => null,

    /*
     * If you would like to customize how routes are matched beyond the route configuration you may
     * declare your own implementation of RouteMatcherInterface
     *
     */
    'routeMatcher' => \Mpociot\ApiDoc\Matching\RouteMatcher::class,
];
