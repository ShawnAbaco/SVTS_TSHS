<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhilSMSService
{
    protected $apiUrl;
    protected $apiToken;
    protected $senderId;

    public function __construct()
    {
        $this->apiUrl = config('philsms.api_url');
        $this->apiToken = config('philsms.api_token');
        $this->senderId = config('philsms.sender_id');
        
        // Log configuration for debugging
        Log::info('PhilSMS Service Initialized', [
            'api_url' => $this->apiUrl,
            'sender_id' => $this->senderId,
            'token_length' => strlen($this->apiToken)
        ]);
    }

    /**
     * Send SMS
     */
    public function sendSMS($phoneNumber, $message)
    {
        try {
            Log::info('Attempting to send SMS', [
                'to' => $phoneNumber,
                'message_length' => strlen($message),
                'sender_id' => $this->senderId
            ]);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])->post($this->apiUrl . 'sms/send', [
                    'recipient' => $this->formatPhoneNumber($phoneNumber),
                    'sender_id' => $this->senderId,
                    'message' => $message,
                    'type' => 'plain'
                ]);

            $responseData = $response->json();
            $statusCode = $response->status();

            Log::info('SMS API Response', [
                'status_code' => $statusCode,
                'response' => $responseData,
                'success' => $response->successful()
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $responseData
                ];
            }

            return [
                'success' => false,
                'error' => $responseData,
                'status_code' => $statusCode
            ];

        } catch (\Exception $e) {
            Log::error('SMS service error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check account balance
     */
    public function checkBalance()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept' => 'application/json',
            ])->get($this->apiUrl . 'balance');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'balance' => $response->json()['data']['balance'] ?? 0
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number to international format
     */
    protected function formatPhoneNumber($phoneNumber)
    {
        // Remove any non-digit characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // If number starts with 0, replace with 63
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '+63' . substr($phoneNumber, 1);
        }
        
        // If number starts with 9, add 63
        if (substr($phoneNumber, 0, 1) === '9' && strlen($phoneNumber) === 10) {
            $phoneNumber = '+63' . $phoneNumber;
        }

        return $phoneNumber;
    }
}