<?php
return [
    "company_token" => env("DPO_COMPANY_TOKEN", "9F416C11-127B-4DE2-AC7F-D5710E4C5E0A"),
    "service_type" => env("DPO_SERVICE_TYPE", "5525"),
    "service_description" => env("DPO_SERVICE_DESCRIPTION", "Test Service"),
    "back_url" => env("DPO_BACK_URL"),
    "redirect_url" => env("DPO_REDIRECT_URL"),
    "live_mode" => env("DPO_LIVE_MODE", true),
    "default_currency" => env("DPO_DEFAULT_CURRENCY"),
    "default_country" => env("DPO_DEFAULT_COUNTRY"),
];
