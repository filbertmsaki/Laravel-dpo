## DPO (Direct Pay Online) Laravel Package
Femlabs DPO package will help you add DPO Payment API to your Laravel Application in easy way and simple.

# How the package work 
    1. Create payment token
    2. Verify payment token
    3. Redirect to payment page
    4. Fetch response
## Installation

    composer require femlabs/laravel-dpo
## Publish the required file
    php artisan vendor:publish --provider="Femlabs\Dpo\DpoServiceProvider"
## Run Migration
    php artisan migrate

## Environment variables
    DPO_COMPANY_TOKEN ="9F416C11-127B-4DE2-AC7F-D5710E4C5E0A"
    DPO_SERVICE_TYPE = "3854"
    DPO_SERVICE_DESCRIPTION = "Test Product"
    DPO_LIVE_MODE = true
    DPO_DEFAULT_CURRENCY = "TZS"
    DPO_DEFAULT_COUNTRY = "Tanzania"
    DPO_BACK_URL ="/cancel"
    DPO_REDIRECT_URL =  "/callback"
## Usage
use the below dependancy in your controller

    use Femlabs\Dpo\Dpo;
## How the package work
=> Create array data of your order in the controller

    $data['companyRef'] = 'ORD' . '' . time(); //$params['invoiceid']; (On this line you can put uniq id of your service)
    $data['paymentAmount'] = 100;
    $data['customerFirstName'] = 'Filbert';
    $data['customerLastName'] = 'Msaki';
    $data['customerAddress'] = 'Ubungo Kibangu';
    $data['customerCity'] = 'Dar Es Salaam';
    $data['customerPhone'] = '07********';
    $data['customerEmail'] =  'filymsaki@gmail.com';
    $data['customerCountry'] = 'TZ'; //ISO 2 letter code
    $data['customerZip'] = '0000';
    $data['serviceDescription'] =  'Test Order';
=> Call the DPO function

    $dpo =new Dpo();
You can choose either to save the response in the database or to make direct payment in the DPO package

=> Go Direct to DPO payment page

    return $dpo->directPaymentPage($data);

=>  If you preffer to save details the follow the steps below.

1.Create payment token first and the redirect to payment page

    $token = $dpo->createToken($data);
2. Check token response and save to database

        if ($token['success'] === true) {
            $data['TransToken'] = $token['TransToken'];

            //Here you can save token details to database

            $verify = $dpo->verifyToken($data);
            if ($verify['success'] === true) {
                //Here you can save token details and verified data to database

                //Get payment url
                $payment_url = $dpo->getPaymentUrl($token);
                //redirect to payment page
                return redirect()->$payment_url;
            }
        }
## License
This project is licensed under the MIT license.
