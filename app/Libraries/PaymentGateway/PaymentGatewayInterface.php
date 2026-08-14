<?php
namespace App\Libraries\PaymentGateway;
interface PaymentGatewayInterface { public function createOrder(float $amount,string $receipt): array; public function verify(array $payload): bool; }
