<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    use \App\Traits\ApiResponse;

    public function getContact()
    {
        return $this->success([
            'phone' => Setting::get('contact_phone', '777123456'),
            'whatsapp' => Setting::get('contact_whatsapp', '967777123456'),
            'email' => Setting::get('contact_email', 'info@mojawharati.com'),
        ]);
    }

    public function getExchangeRates()
    {
        return $this->success([
            'USD' => (double) Setting::get('usd_to_yer', 530),
            'SAR' => (double) Setting::get('sar_to_yer', 140),
            'USD_TO_SAR' => (double) Setting::get('usd_to_sar', 3.75),
        ]);
    }
}
