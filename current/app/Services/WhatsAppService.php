<?php

namespace App\Services;

use App\Models\Empresa;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * WhatsApp Service
 * 
 * Providers supported:
 * 1. 'link' - Opens wa.me link (manual, no cost)
 * 2. 'callmebot' - Free WhatsApp API via CallMeBot
 * 3. 'twilio' - Twilio WhatsApp Business API (paid)
 * 4. 'waha' - Self-hosted WhatsApp HTTP API (free)
 * 
 * Configure in empresa.settings or .env:
 * WHATSAPP_PROVIDER=callmebot
 * WHATSAPP_API_KEY=your_key (for callmebot/twilio)
 */
class WhatsAppService
{
    protected ?Empresa $empresa;
    protected string $provider;
    protected ?string $apiKey;

    public function __construct(?Empresa $empresa = null)
    {
        $this->empresa = $empresa;
        $this->provider = $this->getProvider();
        $this->apiKey = $this->getApiKey();
    }

    protected function getProvider(): string
    {
        // Priority: empresa settings > env > default
        if ($this->empresa) {
            $provider = $this->empresa->getSetting('whatsapp_provider');
            if ($provider) return $provider;
        }
        return env('WHATSAPP_PROVIDER', 'link');
    }

    protected function getApiKey(): ?string
    {
        if ($this->empresa) {
            $key = $this->empresa->getSetting('whatsapp_api_key');
            if ($key) return $key;
        }
        return env('WHATSAPP_API_KEY');
    }

    /**
     * Send text message via WhatsApp
     */
    public function sendText(string $phone, string $message): bool
    {
        $phone = $this->normalizePhone($phone);

        Log::info('WhatsAppService: Sending message', [
            'provider' => $this->provider,
            'phone' => $phone,
            'empresa_id' => $this->empresa?->id,
        ]);

        return match ($this->provider) {
            'callmebot' => $this->sendViaCallMeBot($phone, $message),
            'twilio' => $this->sendViaTwilio($phone, $message),
            'waha' => $this->sendViaWaha($phone, $message),
            'link' => $this->generateLink($phone, $message), // Returns true, link is logged
            default => $this->generateLink($phone, $message),
        };
    }

    /**
     * Normalize phone number to international format
     */
    protected function normalizePhone(string $phone): string
    {
        // Remove non-numeric characters
        $phone = preg_replace('/\D/', '', $phone);

        // Add Mexico country code if 10 digits
        if (strlen($phone) === 10) {
            $phone = '52' . $phone;
        }

        return $phone;
    }

    /**
     * CallMeBot - Free WhatsApp API
     * Setup: User must send "I allow callmebot to send me messages" to +34 644 52 74 88
     * Docs: https://www.callmebot.com/blog/free-api-whatsapp-messages/
     */
    protected function sendViaCallMeBot(string $phone, string $message): bool
    {
        if (!$this->apiKey) {
            Log::warning('WhatsAppService: CallMeBot API key not configured');
            return false;
        }

        try {
            $response = Http::timeout(30)->get('https://api.callmebot.com/whatsapp.php', [
                'phone' => $phone,
                'text' => $message,
                'apikey' => $this->apiKey,
            ]);

            if ($response->successful()) {
                Log::info('WhatsAppService: CallMeBot sent successfully', ['phone' => $phone]);
                return true;
            }

            Log::error('WhatsAppService: CallMeBot failed', [
                'phone' => $phone,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('WhatsAppService: CallMeBot exception', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Twilio WhatsApp Business API (paid)
     */
    protected function sendViaTwilio(string $phone, string $message): bool
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $from = env('TWILIO_WHATSAPP_FROM');

        if (!$sid || !$token || !$from) {
            Log::warning('WhatsAppService: Twilio credentials not configured');
            return false;
        }

        try {
            $response = Http::withBasicAuth($sid, $token)
                ->timeout(30)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'From' => "whatsapp:{$from}",
                    'To' => "whatsapp:+{$phone}",
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                Log::info('WhatsAppService: Twilio sent successfully', ['phone' => $phone]);
                return true;
            }

            Log::error('WhatsAppService: Twilio failed', [
                'phone' => $phone,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('WhatsAppService: Twilio exception', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * WAHA - WhatsApp HTTP API (self-hosted, free)
     * Docs: https://github.com/devlikeapro/whatsapp-http-api
     */
    protected function sendViaWaha(string $phone, string $message): bool
    {
        $baseUrl = env('WAHA_URL', 'http://localhost:3000');
        $session = env('WAHA_SESSION', 'default');

        try {
            $response = Http::timeout(30)->post("{$baseUrl}/api/sendText", [
                'chatId' => "{$phone}@c.us",
                'text' => $message,
                'session' => $session,
            ]);

            if ($response->successful()) {
                Log::info('WhatsAppService: WAHA sent successfully', ['phone' => $phone]);
                return true;
            }

            Log::error('WhatsAppService: WAHA failed', [
                'phone' => $phone,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('WhatsAppService: WAHA exception', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Generate wa.me link (fallback, manual sending)
     */
    protected function generateLink(string $phone, string $message): bool
    {
        $encodedMessage = urlencode($message);
        $link = "https://wa.me/{$phone}?text={$encodedMessage}";

        Log::info('WhatsAppService: Link generated', [
            'phone' => $phone,
            'link' => $link,
        ]);

        // For link mode, we consider it "sent" because the link is available
        return true;
    }

    /**
     * Get wa.me link for manual sending
     */
    public function getLink(string $phone, string $message): string
    {
        $phone = $this->normalizePhone($phone);
        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }

    /**
     * Test the connection/configuration
     */
    public function test(): array
    {
        return [
            'provider' => $this->provider,
            'has_api_key' => !empty($this->apiKey),
            'empresa_id' => $this->empresa?->id,
            'configured' => $this->isConfigured(),
        ];
    }

    /**
     * Check if WhatsApp is properly configured
     */
    public function isConfigured(): bool
    {
        if ($this->provider === 'link') return true;
        if ($this->provider === 'callmebot') return !empty($this->apiKey);
        if ($this->provider === 'twilio') return !empty(env('TWILIO_SID'));
        if ($this->provider === 'waha') return !empty(env('WAHA_URL'));
        return false;
    }
}
