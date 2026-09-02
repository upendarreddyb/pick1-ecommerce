<?php

namespace App\Libraries;

/**
 * Sends approved WhatsApp Business template messages through Meta Cloud API.
 *
 * Credentials are intentionally read from the server environment so secrets do
 * not need to be committed to the application repository.
 */
class WhatsAppCloudApi
{
    public function sendOrderConfirmation(array $order, array $address): bool
    {
        $phoneNumberId = trim((string) env('WHATSAPP_PHONE_NUMBER_ID'));
        $accessToken = trim((string) env('WHATSAPP_ACCESS_TOKEN'));
        $templateName = trim((string) env('WHATSAPP_TEMPLATE_NAME', 'pick1_order_confirmation'));
        $templateLanguage = trim((string) env('WHATSAPP_TEMPLATE_LANGUAGE', 'en'));

        if ($phoneNumberId === '' || $accessToken === '' || $templateName === '') {
            log_message('notice', 'WhatsApp order confirmation skipped: Cloud API credentials are not configured.');
            return false;
        }

        $recipient = $this->normalisePhone((string) ($address['phone'] ?? ''));
        if ($recipient === null) {
            log_message('warning', 'WhatsApp order confirmation skipped for order {orderId}: invalid customer mobile.', [
                'orderId' => $order['id'] ?? 'unknown',
            ]);
            return false;
        }

        $parameters = [
            ['type' => 'text', 'text' => trim((string) ($address['full_name'] ?? 'Customer')) ?: 'Customer'],
            ['type' => 'text', 'text' => order_number($order)],
            ['type' => 'text', 'text' => $this->paymentDescription($order)],
            ['type' => 'text', 'text' => number_format((float) ($order['total_amount'] ?? 0), 2, '.', '')],
            ['type' => 'text', 'text' => base_url('orders/' . (int) $order['id'])],
        ];

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $recipient,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => $templateLanguage],
                'components' => [[
                    'type' => 'body',
                    'parameters' => $parameters,
                ]],
            ],
        ];

        $graphVersion = trim((string) env('WHATSAPP_GRAPH_API_VERSION', 'v23.0')) ?: 'v23.0';
        $endpoint = 'https://graph.facebook.com/' . rawurlencode($graphVersion)
            . '/' . rawurlencode($phoneNumberId) . '/messages';

        try {
            $response = service('curlrequest')->post($endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
                'http_errors' => false,
                'timeout' => 8,
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode >= 200 && $statusCode < 300) {
                log_message('info', 'WhatsApp confirmation sent for order {orderId} to {recipient}.', [
                    'orderId' => $order['id'],
                    'recipient' => $this->maskPhone($recipient),
                ]);
                return true;
            }

            log_message('error', 'WhatsApp confirmation failed for order {orderId} with HTTP {status}: {response}', [
                'orderId' => $order['id'],
                'status' => $statusCode,
                'response' => mb_substr((string) $response->getBody(), 0, 1000),
            ]);
        } catch (\Throwable $exception) {
            log_message('error', 'WhatsApp confirmation failed for order {orderId}: {message}', [
                'orderId' => $order['id'] ?? 'unknown',
                'message' => $exception->getMessage(),
            ]);
        }

        return false;
    }

    private function normalisePhone(string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }
        if (strlen($digits) === 10) {
            $digits = '91' . $digits;
        }

        return preg_match('/^[1-9][0-9]{9,14}$/', $digits) ? $digits : null;
    }

    private function paymentDescription(array $order): string
    {
        $method = trim((string) ($order['payment_method'] ?? ''));
        $status = trim((string) ($order['payment_status'] ?? ''));
        $method = $method !== '' ? ucfirst($method) : 'Online payment';

        return $status !== '' ? $method . ' (' . ucfirst($status) . ')' : $method;
    }

    private function maskPhone(string $phone): string
    {
        return str_repeat('*', max(0, strlen($phone) - 4)) . substr($phone, -4);
    }
}
