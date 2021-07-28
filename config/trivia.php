<?php

return [
  'bonus' => [
    'enabled' => env('BONUS', false),
    'signup' => [
      'enabled' => env('SIGNUP_BONUS', false),
      'amount' => env('SIGNUP_BONUS_AMOUNT', 0),
    ],
  ],
  'tournament' => [
    'enabled' => env('IS_ON_TOURNAMENT', false),
    'start_time' => env('TOURNAMENT_START_TIME',"00:00:00"),
    'end_time' => env('TOURNAMENT_END_TIME', "00:00:00"),
    'categories' => [env('TOURNAMENT_CATEGORY1',''), env('TOURNAMENT_CATEGORY2', '')]
  ],
  'payment_key' => env('PAYSTACK_KEY', null),
  'use_lite_client' => env('USE_LITE_FRONTEND', true),
  'set_claims_active' => env('SET_CLAIMS_ACTIVE', true),
  'admin_withdrawal_request_email'=>env('ADMIN_MAIL_ADDRESS','hello@cashingames.com' ),
  'can_play' => env('CAN_PLAY', true)
];