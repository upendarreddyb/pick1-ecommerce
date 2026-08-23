<?php

namespace App\Libraries\PaymentGateway;

use RuntimeException;

class RazorpayGateway implements PaymentGatewayInterface
{
    private string $key;
    private string $secret;

    public function __construct()
    {
        $this->key = trim((string) env('RAZORPAY_KEY_ID'));
        $this->secret = trim((string) env('RAZORPAY_KEY_SECRET'));
    }

    private function isConfigured(): bool
    {
        return $this->key !== ''
            && $this->secret !== ''
            && ! str_contains(strtolower($this->key), 'replace')
            && ! str_contains(strtolower($this->secret), 'replace')
            && str_starts_with($this->key, 'rzp_');
    }

    public function createOrder(float $amount, string $receipt): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Razorpay test keys are not configured. Add a valid Key ID and Key Secret to the .env file.');
        }

        $amountInPaise = (int) round($amount * 100);
        if ($amountInPaise < 100) {
            throw new RuntimeException('The minimum Razorpay payment amount is ₹1.00.', 400);
        }

        $curl = curl_init('https://api.razorpay.com/v1/orders');
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD        => $this->key . ':' . $this->secret,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode([
                'amount'   => $amountInPaise,
                'currency' => 'INR',
                'receipt'  => $receipt,
            ], JSON_THROW_ON_ERROR),
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        ]);

        $raw = curl_exec($curl);
        $curlError = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($raw === false) {
            throw new RuntimeException('Could not connect to Razorpay: ' . $curlError, 500);
        }

        $result = json_decode($raw, true);
        if ($httpCode < 200 || $httpCode >= 300 || ! is_array($result) || empty($result['id'])) {
            $message = $result['error']['description'] ?? 'Razorpay rejected the order request.';
            if ($httpCode === 401) {
                $message = 'Razorpay authentication failed. Verify the Key ID and Key Secret are from the same test-mode account.';
            }
            throw new RuntimeException($message, $httpCode === 401 ? 401 : 500);
        }

        return $result;
    }

    public function verify(array $payload, string $expectedOrderId = ''): bool
    {
        if (! $this->isConfigured()
            || $expectedOrderId === ''
            || empty($payload['razorpay_order_id'])
            || empty($payload['razorpay_payment_id'])
            || empty($payload['razorpay_signature'])) {
            return false;
        }

        if (! hash_equals($expectedOrderId, (string) $payload['razorpay_order_id'])) {
            return false;
        }

        $expected = hash_hmac(
            'sha256',
            $expectedOrderId . '|' . $payload['razorpay_payment_id'],
            $this->secret,
        );

        return hash_equals($expected, $payload['razorpay_signature']);
    }
}
