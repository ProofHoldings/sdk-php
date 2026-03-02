<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Types\Project;
use ProofHoldings\Types\SuccessResponse;
use ProofHoldings\Types\Template;

class Projects
{
    public function __construct(private readonly HttpClient $http) {}

    public function list(): array
    {
        return $this->http->get('/api/v1/me/projects');
    }

    public function create(array $params): Project
    {
        $data = $this->http->post('/api/v1/me/projects', $params);
        return Project::fromArray($data);
    }

    public function retrieve(string $projectId): Project
    {
        $data = $this->http->get('/api/v1/me/projects/' . rawurlencode($projectId));
        return Project::fromArray($data);
    }

    public function update(string $projectId, array $params): Project
    {
        $data = $this->http->put('/api/v1/me/projects/' . rawurlencode($projectId), $params);
        return Project::fromArray($data);
    }

    public function delete(string $projectId): SuccessResponse
    {
        $data = $this->http->delete('/api/v1/me/projects/' . rawurlencode($projectId));
        return SuccessResponse::fromArray($data);
    }

    public function listTemplates(string $projectId): array
    {
        return $this->http->get('/api/v1/me/projects/' . rawurlencode($projectId) . '/templates');
    }

    public function updateTemplate(string $projectId, string $channel, string $messageType, array $params): Template
    {
        $data = $this->http->put(
            '/api/v1/me/projects/' . rawurlencode($projectId) . '/templates/' . rawurlencode($channel) . '/' . rawurlencode($messageType),
            $params,
        );
        return Template::fromArray($data);
    }

    public function deleteTemplate(string $projectId, string $channel, string $messageType): SuccessResponse
    {
        $data = $this->http->delete(
            '/api/v1/me/projects/' . rawurlencode($projectId) . '/templates/' . rawurlencode($channel) . '/' . rawurlencode($messageType),
        );
        return SuccessResponse::fromArray($data);
    }

    public function previewTemplate(string $projectId, array $params): array
    {
        return $this->http->post(
            '/api/v1/me/projects/' . rawurlencode($projectId) . '/templates/preview',
            $params,
        );
    }
}
