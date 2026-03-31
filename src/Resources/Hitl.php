<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Types\Hitl as HitlType;
use ProofHoldings\Types\HitlListResponse;

class Hitl
{
    public function __construct(private readonly HttpClient $http) {}

    public function create(array $params): HitlType
    {
        $data = $this->http->post('/api/v1/hitl', $params);
        return HitlType::fromArray($data);
    }

    public function retrieve(string $id): HitlType
    {
        $data = $this->http->get('/api/v1/hitl/' . rawurlencode($id));
        return HitlType::fromArray($data);
    }

    public function list(array $params = []): HitlListResponse
    {
        $data = $this->http->get('/api/v1/hitl', $params);
        return HitlListResponse::fromArray($data);
    }

    public function update(string $id, array $params): HitlType
    {
        $data = $this->http->patch('/api/v1/hitl/' . rawurlencode($id), $params);
        return HitlType::fromArray($data);
    }

    public function delete(string $id): HitlType
    {
        $data = $this->http->delete('/api/v1/hitl/' . rawurlencode($id));
        return HitlType::fromArray($data);
    }

    /** @return array{hitl_id: string, authorizations: array, message: string} */
    public function requestAuthorization(string $id): array
    {
        return $this->http->post('/api/v1/hitl/' . rawurlencode($id) . '/authorize', []);
    }

    /** @return array{token: string, deep_link: string, qr_code: string, expires_in: int} */
    public function createChatIdDiscovery(): array
    {
        return $this->http->post('/api/v1/hitl/chat-id-discovery', []);
    }

    /** @return array{status: string, chat_id?: string, user_id?: string, username?: string, first_name?: string} */
    public function pollChatIdDiscovery(string $token): array
    {
        return $this->http->get('/api/v1/hitl/chat-id-discovery/' . rawurlencode($token));
    }
}
