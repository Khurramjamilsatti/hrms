<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class LocalizedPricingService
{
    public function localize(Collection $plans, Request $request): array
    {
        $country = $this->countryCode($request);
        $currency = $this->currencyForCountry($country);
        $rate = $this->rateFromPkr($currency);

        $localizedPlans = $plans->map(function ($plan) use ($currency, $rate) {
            $amountPkr = $this->parsePkrAmount($plan->price);
            $plan->localized_currency = $amountPkr && $rate ? $currency : null;
            $plan->localized_price_amount = $amountPkr && $rate
                ? round($amountPkr * $rate)
                : null;

            return $plan;
        });

        return [
            'plans' => $localizedPlans,
            'pricing_locale' => [
                'country' => $country,
                'currency' => $rate ? $currency : 'PKR',
                'localized' => $currency !== 'PKR' && $rate !== null,
            ],
        ];
    }

    private function countryCode(Request $request): string
    {
        foreach (['CF-IPCountry', 'X-Vercel-IP-Country', 'CloudFront-Viewer-Country'] as $header) {
            $country = strtoupper((string) $request->header($header));
            if (preg_match('/^[A-Z]{2}$/', $country) && $country !== 'XX') {
                return $country;
            }
        }

        $ip = $request->ip();
        if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return 'PK';
        }

        return Cache::remember('landing-country:' . hash('sha256', $ip), now()->addDay(), function () use ($ip) {
            try {
                $response = Http::timeout(2)->acceptJson()->get("https://ipapi.co/{$ip}/country/");
                $country = strtoupper(trim($response->body()));

                return $response->successful() && preg_match('/^[A-Z]{2}$/', $country)
                    ? $country
                    : 'PK';
            } catch (\Throwable) {
                return 'PK';
            }
        });
    }

    private function rateFromPkr(string $currency): ?float
    {
        if ($currency === 'PKR') {
            return 1.0;
        }

        $rates = Cache::remember('landing-currency-rates:pkr', now()->addHours(12), function () {
            try {
                $response = Http::timeout(3)->acceptJson()->get('https://open.er-api.com/v6/latest/PKR');
                if (!$response->successful() || $response->json('result') !== 'success') {
                    return [];
                }

                return $response->json('rates', []);
            } catch (\Throwable) {
                return [];
            }
        });

        $rate = $rates[$currency] ?? null;

        return is_numeric($rate) && (float) $rate > 0 ? (float) $rate : null;
    }

    private function parsePkrAmount(?string $price): ?float
    {
        if (!$price || strtolower($price) === 'custom') {
            return null;
        }

        $numeric = preg_replace('/[^\d.]/', '', $price);

        return is_numeric($numeric) ? (float) $numeric : null;
    }

    private function currencyForCountry(string $country): string
    {
        $currencies = [
            'PK' => 'PKR', 'US' => 'USD', 'GB' => 'GBP', 'CA' => 'CAD',
            'AU' => 'AUD', 'NZ' => 'NZD', 'AE' => 'AED', 'SA' => 'SAR',
            'QA' => 'QAR', 'OM' => 'OMR', 'KW' => 'KWD', 'BH' => 'BHD',
            'IN' => 'INR', 'BD' => 'BDT', 'LK' => 'LKR', 'NP' => 'NPR',
            'CN' => 'CNY', 'JP' => 'JPY', 'SG' => 'SGD', 'MY' => 'MYR',
            'ID' => 'IDR', 'TH' => 'THB', 'PH' => 'PHP', 'KR' => 'KRW',
            'ZA' => 'ZAR', 'NG' => 'NGN', 'KE' => 'KES', 'EG' => 'EGP',
            'TR' => 'TRY', 'BR' => 'BRL', 'MX' => 'MXN',
            'DE' => 'EUR', 'FR' => 'EUR', 'IT' => 'EUR', 'ES' => 'EUR',
            'NL' => 'EUR', 'BE' => 'EUR', 'IE' => 'EUR', 'PT' => 'EUR',
            'AT' => 'EUR', 'FI' => 'EUR', 'GR' => 'EUR', 'LU' => 'EUR',
        ];

        return $currencies[$country] ?? 'USD';
    }
}
