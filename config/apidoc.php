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
         * https://schema.getpostman.com/json/collection/v2.1.0/docs/index.html
         */
        'auth' => [
            "type" => "bearer",
            "bearer" => [
                [
                    "key" => "token",
                    "value" => "{{OAUTH_TOKEN}}",
                    "type" => "string"
                ]
            ]
        ],

        /*
         * Specify rules to be applied to all the postman routes in this group when generating documentation
         */
        'apply' => [
            /*
             * Specify headers to be added to the postman requests
             */
            'headers' => [
                'Accept' => 'application/json',
                'Referer' => '{{STREAMLABS_URL}}dashboard',
                'Cookie' => '{{STREAMLABS_COOKIE}}',
                'X-CSRF-TOKEN' => '{{X-CSRF-TOKEN}}'
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
            'token' => [
                'value' => '{{USER_TOKEN}}',
                'description' => 'Streamlabs user token'
            ],
            'username' => [
                'value' => '{{USERNAME}}',
                'description' => 'Username (used for User, Profile and Facemasks)'
            ],
            'userId' => [
                'value' => '{{LARAVEL_USER_ID}}',
                'description' => 'Laravel User ID of Streamlabs Dashboard'
            ],
            'articleId' => [
                'value' => '{{PARAM_BLOG_ARTICLE_ID}}',
                'description' => 'Admin Blog article ID'
            ],
            'clientId' => [
                'value' => '{{PARAM_CLIENT_ID}}',
                'description' => 'Dashboard API Settings client_id'
            ],
            'email' => [
                'value' => '{{PARAM_ADMIN_PANEL_EMAIL}}',
                'description' => 'Streamlabs user email (used on Dashboard Admin Panel)'
            ],
            'handle' => [
                'value' => '{{PARAM_ADMIN_PANEL_HANDLE}}',
                'description' => 'Dashboard username/email or something else (used on Dashboard Admin Panel search)'
            ],
            'platform' => [
                'value' => '{{PARAM_PLATFORM}}',
                'description' => 'Platform type, like twitch, mixer, facebook, etc (used on Dashboard Admin Panel, ChatBotApi, Giveaway, Integrations, Leaderboard, Test alerts, Accounts, Streamlabels API)'
            ],
            'profileId' => [
                'value' => '{{PARAM_PROFILE_ID}}',
                'description' => 'Used for Widget Profiles'
            ],
            'profile' => [
                'value' => '{{PARAM_PROFILE_ID}}',
                'description' => 'Used for Widget Profiles'
            ],
            'hash' => [
                'value' => '{{PARAM_HASH}}',
                'description' => 'Hash code for Widget Profiles'
            ],
            'id' => [
                'value' => '{{PARAM_ID}}',
                'description' => 'General ID number, you might like to use a specific value here. (used in multiple places in this collection, like: AlertProfile, Changelog, Giveaway, Media, Poll, Restream, StreamerLoyalty, StreamerLoyaltyAdmin, UserManagement, Gfycat, Merchandise)'
            ],
            'transactionId' => [
                'value' => '{{PARAM_TRANSACTION_ID}}',
                'description' => 'Used for Admin Billing and BrainTree refunds.'
            ],
            'subscriptionId' => [
                'value' => '{{PARAM_SUBSCRIPTION_ID}}',
                'description' => 'Used on Admin Billing, Braintree and Prime subsriptions'
            ],
            'streamerId' => [
                'value' => '{{TWITCH_ID}}',
                'description' => 'The platform id of a streamer on Twitch.'
            ],
            'itemId' => [
                'value' => '{{PARAM_COLLECTIBLE_ITEM_ID}}',
                'description' => 'Collectible item id'
            ],
            'channelId' => [
                'value' => '{{TWITCH_ID}}',
                'description' => 'The platform id of a streamer on Twitch.'
            ],
            'type' => [ // RETURN HERE
                'value' => '{{PARAM_TYPE}}',
                'description' => ''
            ],
            'template' => [
                'value' => '{{PARAM_TEMPLATE}}',
                'description' => 'Email or Twig Donation template.'
            ],
            'uuid' => [
                'value' => '{{PARAM_UUID}}',
                'description' => 'UUID value (used for Facemasks)'
            ],
            'path' => [
                'value' => '{{PARAM_PATH}}',
                'description' => 'Storage path (used on Dashboard Closure, FileUpload and Cloudbot)'
            ],
            'search' => [
                'value' => '{{PARAM_GFYCAT_SEARCH}}',
                'description' => 'Gfycat search string.'
            ],
            'giveawayId' => [
                'value' => '{{PARAM_GIVEAWAY_ID}}',
                'description' => 'GiveAway ID'
            ],
            'viewerId' => [
                'value' => '{{PARAM_GIVEAWAY_VIEWER_ID}}',
                'description' => 'GiveAway Viewer ID'
            ],
            'configurationKey' => [
                'value' => '{{PARAM_CONFIGURATION_KEY}}',
                'description' => 'Configuration Key for Global or Mobile configurations.'
            ],
            'referrer' => [
                'value' => '{{PARAM_REFERRER}}',
                'description' => 'Home Referrer.'
            ],
            'a' => [
                'value' => '{{PARAM_A}}',
                'description' => 'Home slobs or Slobs Download "a" param.'
            ],
            'featureFlag' => [
                'value' => '{{PARAM_FEATURE_FLAG}}',
                'description' => 'Incremental Rollout feature flag'
            ],
            'twitchId' => [
                'value' => '{{TWITCH_ID}}',
                'description' => 'The platform id of a streamer on Twitch.'
            ],
            'countType' => [
                'value' => '{{LEADERBOARD_COUNT_TYPE}}',
                'description' => 'Leaderboard count type.'
            ],
            'period' => [
                'value' => '{{LEADERBOARD_PERIOD}}',
                'description' => 'Leaderboard period of date.'
            ],
            'module_slug' => [
                'value' => '{{LOYALTY_MODULE_SLUG}}',
                'description' => 'Loyalty Module Slug'
            ],
            'boardId' => [
                'value' => '{{PAINT_BOARD_ID}}',
                'description' => 'Paint Board ID'
            ],
            'x' => [
                'value' => '{{PAINT_X}}',
                'description' => 'Paint X Param'
            ],
            'y' => [
                'value' => '{{PAINT_Y}}',
                'description' => 'Paint Y Param'
            ],
            'disputeId' => [
                'value' => '{{PAYPAL_DISPUTE_ID}}',
                'description' => 'PayPal dispute ID'
            ],
            'orderId' => [
                'value' => '{{PAYPAL_ORDER_ID}}',
                'description' => 'PayPal order ID'
            ],
            'broadcastId' => [
                'value' => '{{PERISCOPE_BROADCAST_ID}}',
                'description' => 'Periscope broadcast ID'
            ],
            'chatToken' => [
                'value' => '{{PERISCOPE_CHAT_TOKEN}}',
                'description' => 'Periscope Chat Token'
            ],
            'idx' => [
                'value' => '{{PARAM_POLL_IDX}}',
                'description' => 'Poll ID'
            ],
            'promoType' => [
                'value' => '{{PRIME_PROMOTYPE}}',
                'description' => 'Prime Promotion type'
            ],
            'redeemCode' => [
                'value' => '{{PARAM_REDEMPTION_REDEEM_CODE}}',
                'description' => 'Redemption Redeem code'
            ],
            'sku' => [
                'value' => '{{PARAM_SLOBS_INTELCONFIG_SKU}}',
                'description' => 'Slobs Intelconfig SKU'
            ],
            'game' => [
                'value' => '{{PARAM_SLOBS_GAME}}',
                'description' => 'Slobs game preset'
            ],
            'month' => [
                'value' => '{{PARAM_SLOBS_MONTH}}',
                'description' => 'Slobs affiliate month for monthly stats.'
            ],
            'route' => [
                'value' => '{{PARAM_STATS_OR_UPLOAD_ROUTE}}',
                'description' => 'Stats or Upload Router route'
            ],
            'dealId' => [
                'value' => '{{LOYALTY_DEAL_ID}}',
                'description' => 'Streamer Loyalty Deal ID'
            ],
            'broadcaster' => [
                'value' => '{{PARAM_TIP_BROADCASTER}}',
                'description' => 'Tip Broadcaster'
            ],
            'attachmentId' => [
                'value' => '{{PARAM_TIP_ATTACHMENT_ID}}',
                'description' => 'Tip Attachment ID'
            ],
            'name' => [
                'value' => '{{USERNAME}}',
                'description' => 'Username (used for User, Profile and Facemasks)'
            ],
            'secret' => [ // RETURN HERE
                'value' => '{{PARAM_INVITE_SECRET}}',
                'description' => 'User Management or Charity invite secret.'
            ],
            'charity_name' => [
                'value' => '{{CHARITY_NAME}}',
                'description' => 'Charity Name'
            ],
            'slug' => [
                'value' => '{{CHARITY_SLUG}}',
                'description' => 'Charity Slug'
            ],
            'campaign_id' => [
                'value' => '{{CHARITY_CAMPAIGN_ID}}',
                'description' => 'Charity campaign ID'
            ],
            'charity_id' => [
                'value' => '{{CHARITY_CAMPAIGN_ID}}',
                'description' => 'Charity campaign ID'
            ],
            'streamer_campaign_id' => [
                'value' => '{{CHARITY_STREAMER_CAMPAIGN_ID}}',
                'description' => 'Charity streamer campaign ID (should be different of CHARITY_CAMPAIGN_ID variable value)'
            ],
            'team_slug' => [
                'value' => '{{CHARITY_TEAM_SLUG}}',
                'description' => 'Charity Team slug name'
            ],
            'team_id' => [
                'value' => '{{CHARITY_TEAM_ID}}',
                'description' => 'Charity Team ID'
            ],
            'member_id' => [
                'value' => '{{CHARITY_MEMBER_ID}}',
                'description' => 'Charity Member ID'
            ],
            'importStreamElements' => [
                'value' => '{{STREAM_ELEMENTS_JWT}}',
                'description' => 'JWT for StreamElements import'
            ],
            'orderCode' => [
                'value' => '{{MERCH_ORDER_CODE}}',
                'description' => 'Merch order code.'
            ],
            'droopshipId' => [
                'value' => '{{MERCH_DROOPSHIP_ID}}',
                'description' => 'Merch Droopship ID'
            ],
            'productId' => [
                'value' => '{{MERCH_PRODUCT_ID}}',
                'description' => 'Merch Product ID'
            ],
            'mappingId' => [
                'value' => '{{MERCH_MAPPING_ID}}',
                'description' => 'Merch Mapping ID'
            ],
            'dropshipId' => [
                'value' => '{{MERCH_DROPSHIP_ID}}',
                'description' => 'Merch Dropship ID'
            ],
            'taskKey' => [
                'value' => '{{MERCH_TASK_KEY}}',
                'description' => 'Merch Task Key'
            ],
            'giftId' => [
                'value' => '{{PRIME_GIFT_ID}}',
                'description' => 'Prime Gift ID for cancellation.'
            ],
            'widgetType' => [
                'value' => '{{PARAM_LOGS_WIDGET_TYPE}}',
                'description' => 'Widget type for widget logs'
            ],
            // Closure Params
            'all' => [
                'value' => '{{PARAM_ALL}}',
                'description' => 'Anything'
            ],
            'widget' => [
                'value' => '{{PARAM_LOGS_WIDGET_TYPE}}',
                'description' => 'A widget name'
            ],
            'twitchUsername' => [
                'value' => '{{TWITCH_USERNAME}}',
                'description' => 'Twitch username'
            ],
            'url' => [
                'value' => '{{PARAM_PROXY_URL}}',
                'description' => 'Twitch Proxy URL, usually: "https://static-cdn.jtvnw.net"'
            ]

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
            \Mpociot\ApiDoc\Extracting\Strategies\Metadata\CustomMetadata::class,
        ],
        'urlParameters' => [
            \Mpociot\ApiDoc\Extracting\Strategies\UrlParameters\GetFromUrlParamTag::class,
            \Mpociot\ApiDoc\Extracting\Strategies\UrlParameters\GetFromUrlString::class,
        ],
        'queryParameters' => [
            \Mpociot\ApiDoc\Extracting\Strategies\QueryParameters\GetFromQueryParamTag::class,
            \Mpociot\ApiDoc\Extracting\Strategies\QueryParameters\CustomGetFromConfig::class,
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
