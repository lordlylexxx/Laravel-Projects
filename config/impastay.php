<?php

return [

    /*
    | When a tenant owner/admin messages "ImpaStay (Central Admin)", an in-app thread is
    | stored in the tenant database. Optionally notify this address so central staff see it.
    */
    'central_support_notify_email' => env('IMPASTAY_CENTRAL_SUPPORT_NOTIFY_EMAIL'),

    /*
    | Central onboarding payment details shown on the owner onboarding page.
    */
    'onboarding_gcash_account_name' => env('IMPASTAY_ONBOARDING_GCASH_ACCOUNT_NAME', 'ImpaStay'),
    'onboarding_gcash_number' => env('IMPASTAY_ONBOARDING_GCASH_NUMBER'),

];
