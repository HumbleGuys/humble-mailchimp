<?php

namespace HumbleMailchimp;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Mailchimp
{
    protected string $apiKey;

    protected string $apiServer;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->apiServer = Str::afterLast($apiKey, '-');
    }

    public function ping(): Response
    {
        return $this->request()->get($this->apiUrl('/ping'));
    }

    public function getLists(): Response
    {
        return $this->request()->get($this->apiUrl('/lists'));
    }

    public function subscribe(string $email, string $listId): Response
    {
        $email = strtolower($email);
        $memberId = md5($email);

        return $this->request()->put($this->apiUrl("/lists/{$listId}/members/{$memberId}"), [
            'email_address' => $email,
            'status_if_new' => 'subscribed',
            'status' => 'subscribed',
        ]);
    }

    protected function request(): PendingRequest
    {
        return Http::withBasicAuth('mcuser', $this->apiKey);
    }

    protected function apiUrl(string $endpoint): string
    {
        return "https://{$this->apiServer}.api.mailchimp.com/3.0/{$endpoint}";
    }
}
