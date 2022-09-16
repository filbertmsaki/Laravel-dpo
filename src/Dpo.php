<?php

namespace Femlabs\Dpo;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;

class Dpo
{
    const DPO_TEST_URL = 'https://secure1.sandbox.directpay.online';
    const DPO_LIVE_URL = 'https://secure.3gdirectpay.com';
    private $liveMode = false;
    private $baseUrl;
    private $dpoUrl;
    private $company_token;
    private $service_type;
    private $service_description;
    private $dpo_redirect_url;
    private $dpo_back_url;
    private $dpo_default_currency;
    private $dpo_default_country;

    public function __construct()
    {
        $this->liveMode = config("laravel-dpo.live_mode");
        $this->company_token = config("laravel-dpo.company_token");
        $this->service_type = config("laravel-dpo.service_type");
        $this->service_description = config("laravel-dpo.service_description");
        $this->dpo_back_url = config("laravel-dpo.back_url");
        $this->dpo_redirect_url = config("laravel-dpo.redirect_url");
        $this->dpo_default_currency = config("laravel-dpo.default_currency");
        $this->dpo_default_country = config("laravel-dpo.default_country");
        if ($this->liveMode == false) {
            $this->baseUrl = self::DPO_TEST_URL;
        } else {
            $this->baseUrl = self::DPO_LIVE_URL;
        }
        $this->dpoUrl = $this->baseUrl . '/payv2.php?ID=';
    }
    public function gatewayUrl()
    {
        return $this->dpoUrl;
    }
    public function createToken($data)
    {
        //Payment Details
        $companyToken      = $this->company_token;
        $backUrl           = $this->dpo_back_url;
        $redirectUrl       = $this->dpo_redirect_url;
        $paymentCurrency   = $data['paymentCurrency'] ?? $this->dpo_default_currency;
        $paymentAmount     = $data['paymentAmount'];
        $companyRef         = $data['companyRef'];
        //Customer Details
        $customerFirstName = $data['customerFirstName'];
        $customerLastName  = $data['customerLastName'];
        $customerPhone     = $data['customerPhone'];
        $customerEmail     = $data['customerEmail'];
        $customerCountry   = $data['customerCountry']; //ISO 2 letter code
        $customerCity     = $data['customerCity'];
        $customerAddress     = $data['customerAddress'];
        $customerZip     = $data['customerZip'];
        //Service Details
        $serviceType       = $this->service_type;
        $serviceDescription   = $data['serviceDescription'] ?? $this->service_description;
        $serviceDate   = date('Y/m/d H:i');

        $xml = '<?xml version="1.0" encoding="utf-8"?>
        <API3G>
        <CompanyToken>' . $companyToken . '</CompanyToken>
        <Request>createToken</Request>
        <Transaction>
        <PaymentAmount>' . $paymentAmount . '</PaymentAmount>
        <PaymentCurrency>' . $paymentCurrency . '</PaymentCurrency>
        <CompanyRef>' . $companyRef . '</CompanyRef>
        <CompanyRefUnique>0</CompanyRefUnique>
        <RedirectURL>' . $redirectUrl . '</RedirectURL>
        <BackURL>' . $backUrl . ' </BackURL>
        <customerCountry>' . $customerCountry . '</customerCountry>
        <customerFirstName>' . $customerFirstName . '</customerFirstName>
        <customerLastName>' . $customerLastName . '</customerLastName>
        <customerPhone>' . $customerPhone . '</customerPhone>
        <customerEmail>' . $customerEmail . '</customerEmail>
        <customerCity>' . $customerCity . '</customerCity>
        <customerAddress>' . $customerAddress . '</customerAddress>
        <customerZip>' . $customerZip . '</customerZip>
        <TransactionSource>whmcs</TransactionSource>
        <PTL>5</PTL>
        </Transaction>
        <Services>
          <Service>
            <ServiceType>' . $serviceType . '</ServiceType>
            <ServiceDescription>' . $serviceDescription . '</ServiceDescription>
            <ServiceDate>' . $serviceDate . '</ServiceDate>
          </Service>
        </Services>
        </API3G>';

        $client = new Client([
            'base_uri' => $this->baseUrl,
        ]);

        $response = $client->post('/API/v6/', [
            'debug' => FALSE,
            'body' => $xml,
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF8',
            ]
        ]);
        $body = $response->getBody();
        if ($body != '') {
            $xml = simplexml_load_string($body);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);

            if ($array['Result'] != '000') {
                $response = Arr::prepend($array, false, 'success');
            } else if ($array['Result'] == '000') {
                $response = Arr::prepend($array, true, 'success');
            }
            return $response;
        } else {
            return [
                'success'           => false,
                'result'            => 'Unknown error occurred in token creation',
                'resultExplanation' => 'Unknown error occurred in token creation',
            ];
        }
    }
    public function verifyToken(array $data)
    {
        $companyToken = $this->company_token;
        $transToken   = $data['TransToken'];
        $xml = '<?xml version="1.0" encoding="utf-8"?>
          <API3G>
            <CompanyToken>' . $companyToken . '</CompanyToken>
            <Request>verifyToken</Request>
            <TransactionToken>' . $transToken . '</TransactionToken>
          </API3G>';

        $client = new Client([
            'base_uri' => $this->baseUrl,
        ]);

        $response = $client->post('/API/v6/', [
            'debug' => FALSE,
            'body' => $xml,
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF8',
            ]
        ]);
        $body = $response->getBody();
        if ($body != '') {
            $xml = simplexml_load_string($body);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);

            if ($array['Result'] != 900) {
                $response = Arr::prepend($array, false, 'success');
                return $response;
            } else {
                $response = Arr::prepend($array, true, 'success');
                return $response;
            }
        } else {
            return [
                'success'           => false,
                'result'            => 'Unknown error occurred in token creation',
                'resultExplanation' => 'Unknown error occurred in token creation',
            ];
        }
    }
    public function getPaymentUrl($data)
    {
        if ($data['success'] === true) {

            $verifyToken   = $this->verifyToken(['TransToken' => $data['TransToken']]);

            if (!empty($verifyToken) && $verifyToken != '') {

                $dpo_payment_url = $this->gatewayUrl() . $data['TransToken'];
                return $dpo_payment_url;
            } else {
                return [
                    'success'           => false,
                    'result'            => 'Unknown error occurred in token creation',
                    'resultExplanation' => 'Unknown error occurred in token creation',
                ];
            }
        } else {
            return [
                'success'           => false,
                'result'            => 'Unknown error occurred in token creation',
                'resultExplanation' => 'Unknown error occurred in token creation',
            ];
        }
    }
    public function directPaymentPage($data)
    {
        $token = $this->createToken($data);
        $get_payment_url = $this->getPaymentUrl($token);
        return redirect()->to($get_payment_url);
    }
}
