<?php

return [
    'service_manager' => [
        'factories' => [
            CloudMessaging\Mapper\MulticastMapper::class => \CloudMessaging\Mapper\MulticastMapperFactory::class,
        ],
    ],
    'controllers' => [
        'factories' => [
            \CloudMessaging\Controller\IndexController::class => \CloudMessaging\Controller\IndexControllerFactory::class,
        ]
    ],
    'view_manager' => [
        'template_map' => [
            'cloud-messaging/index/index' => __DIR__ . '/../view/cloud-messaging/index/index.phtml'
        ]
    ],
    'cloud_messaging' => [
        //"gcm_endpoint" => "https://android.googleapis.com/gcm/send",
        //"gcm_api_key" => "Your api key - not here for security reasons",
        "gcm_config" => [
            /*
             * This parameter specifies the recipient of a message.The value
             * must be a registration token, notification key, or topic.
             */
            "to" => "",
            /**
             * This parameter specifies a list of devices (registration tokens,
             * or IDs) receiving a multicast message. It must contain at least 1
             * and at most 1000 registration tokens.
             *
             * Note: If you have to use registration_ids values set manually
             * (under this key), remember to provide corresponding userIds when
             * invoking $message->send
             */
            "registration_ids" => [],
            /*
             * Sets the priority of the message. Valid values are "normal" and
             * "high." On iOS, these correspond to APNs priority 5 and 10.
             * By default, messages are sent with normal priority. Normal
             * priority optimizes the client app's battery consumption,
             * and should be used unless immediate delivery is required.
             * For messages with normal priority, the app may receive the
             * message with unspecified delay. When a message is sent with
             * high priority, it is sent immediately, and the app can wake a
             * sleeping device and open a network connection to your server.
             */
            "priority" => "high",
            /*
             * This parameter identifies a group of messages
             * (e.g., with collapse_key: "Updates Available") that can be
             * collapsed, so that only the last message gets sent when delivery
             * can be resumed. This is intended to avoid sending too many of the
             * same messages when the device comes back online or becomes active
             * (see delay_while_idle)
             */
            //DON'T use it for now "collapse_key" => "Update Available",
            /*
             * On iOS, use this field to represent content-available in the APNS
             * payload. When a notification or message is sent and this is set
             * to true, an inactive client app is awoken. On Android,
             * data messages wake the app by default. On Chrome, currently not supported.
             *
             * Setting the property in this part of the payload will result in
             * the PushPlugin not getting the data correctly. Setting
             * content-available: true will cause the Android OS to handle the
             * push payload for you and not pass the data to the PushPlugin.
             * 
             * "content_available" => false,
             */
            /*
             * This parameter specifies the package name of the application
             * where the registration tokens must match in order to receive the message.
             */
            "restricted_package_name" => "com.notisha.mobile",
            /*
             * This parameter, when set to true, allows developers to test a
             * request without actually sending a message.
             */
            "dry_run" => false,
            /**
             * 
             */
            "data" => [
                /**
                 * Message title
                 */
                "title" => null,
                /**
                 * message body
                 */
                "message" => null,
                /**
                 * default|ringtone|'' - no tone|in res/raw folder
                 */
                "soundname" => "default",
                /**
                 * call the background on(notification) handler
                 */
                "content_available" => "1",
                /**
                 * Key used on client side to decide what to do upon receiving
                 * notification. Distinguishes the different
                 * types of notifications that might be received.
                 * Named after table names.
                 * 
                 * user_inbox etc
                 */
                "notification_category" => 'lesson_for_the_day',
                /**
                 * User device compares with the current device version
                 */
                "meta" => [
                    // compares with local version and suggests update
                    "latest_android_version" => 1,
                    // if 1 app must be updated
                    "force_android_update" => 0,
                ],
                "image" => "icon",
                /**
                 * Optional. Notification action buttons
                 * 
                 * Note: This key is auto_deleted locally
                 * (within this codebase) if not provided                 *
                 */
                "actions" => [

                    [
                        /**
                         * Optional. The name of a drawable resource to use as the small-icon. The name should not include the extension.
                         */
                        "icon" => "snooze",
                        /**
                         * Required. The label to display for the action button.
                         */
                        "title" => "SNOOZE",
                        /**
                         * Required. The function to be executed when the action button is pressed.
                         * The function must be accessible from the global namespace.
                         * If you provide myCallback then it amounts to calling window.myCallback.
                         * If you provide app.myCallback then there needs to be an object call app,
                         * with a function called myCallback accessible from the global namespace,
                         * i.e. window.app.myCallback.
                         */
                        "callback" => "app.snooze",
                        /**
                         * Optional. Whether or not to bring the app to the foreground when the action button is pressed.
                         */
                        "foreground" => true
                    ]
                ]
            ]
        ]
    ]
];
