<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;

/**
 * Circle people-primitive: named groups of trusted contacts, their members, and
 * per-member channels. Member and channel responses expose reachability booleans
 * only — an identifier is NEVER returned (SEC-PM-006).
 *
 * Methods return plain arrays by design (like Me / PublicProfiles / multi-channel),
 * not `Types.php` DTOs. `Types.php` carries matching `Circle`/`CircleMember`/etc.
 * DTOs (generated from the OpenAPI spec), but wrapping this resource's 9 methods
 * in `::fromArray()` would change their return type and break every existing
 * caller — a cost this resource's plain-array convention was never meant to
 * incur. Decision recorded 2026-09-28 (giorgi): keep plain arrays permanently.
 */
class Circles
{
    public function __construct(private readonly HttpClient $http) {}

    /**
     * Create a new Circle (optionally with initial members).
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function create(array $params): array
    {
        return $this->http->post('/api/v1/circles', $params);
    }

    /**
     * List Circles with optional filters (archived excluded by default).
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function list(array $params = []): array
    {
        return $this->http->get('/api/v1/circles', $params);
    }

    /**
     * Get a Circle by ID.
     *
     * @return array<string, mixed>
     */
    public function get(string $id): array
    {
        return $this->http->get('/api/v1/circles/' . rawurlencode($id));
    }

    /**
     * Update a Circle's name (the profile binding is create-only).
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function update(string $id, array $params): array
    {
        return $this->http->patch('/api/v1/circles/' . rawurlencode($id), $params);
    }

    /**
     * Archive a Circle (soft delete).
     *
     * @return array<string, mixed>
     */
    public function archive(string $id): array
    {
        return $this->http->delete('/api/v1/circles/' . rawurlencode($id));
    }

    /**
     * Add a pending member by name (+ an optional pre-declared WhatsApp phone).
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function addMember(string $id, array $params): array
    {
        return $this->http->post('/api/v1/circles/' . rawurlencode($id) . '/members', $params);
    }

    /**
     * List a Circle's members (channel presence booleans and a masked enrollment phone only).
     *
     * @return array<string, mixed>
     */
    public function listMembers(string $id): array
    {
        return $this->http->get('/api/v1/circles/' . rawurlencode($id) . '/members');
    }

    /**
     * Mint a single-use enrollment deep link (both channels + QR) for a member.
     *
     * @return array<string, mixed>
     */
    public function inviteMember(string $id, string $memberId): array
    {
        return $this->http->post('/api/v1/circles/' . rawurlencode($id) . '/members/invite', ['member_id' => $memberId]);
    }

    /**
     * Fire a cross-channel Proof-Me identity challenge against an enrolled member.
     *
     * @return array<string, mixed>
     */
    public function triggerDrill(string $id, string $memberId): array
    {
        return $this->http->post('/api/v1/circles/' . rawurlencode($id) . '/members/' . rawurlencode($memberId) . '/drill');
    }

    /**
     * Remove a member (deletes its channels and revokes its live authorizations).
     *
     * @return array<string, mixed>
     */
    public function removeMember(string $id, string $memberId): array
    {
        return $this->http->delete('/api/v1/circles/' . rawurlencode($id) . '/members/' . rawurlencode($memberId));
    }

    /**
     * Declare a channel for a member (409 if that channel type already exists).
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function addMemberChannel(string $id, string $memberId, array $params): array
    {
        return $this->http->post('/api/v1/circles/' . rawurlencode($id) . '/members/' . rawurlencode($memberId) . '/channels', $params);
    }

    /**
     * List a member's channels (metadata only, never an identifier).
     *
     * @return array<string, mixed>
     */
    public function listMemberChannels(string $id, string $memberId): array
    {
        return $this->http->get('/api/v1/circles/' . rawurlencode($id) . '/members/' . rawurlencode($memberId) . '/channels');
    }

    /**
     * Remove a member channel (removing the last channel is allowed).
     *
     * @return array<string, mixed>
     */
    public function removeMemberChannel(string $id, string $memberId, string $channelId): array
    {
        return $this->http->delete('/api/v1/circles/' . rawurlencode($id) . '/members/' . rawurlencode($memberId) . '/channels/' . rawurlencode($channelId));
    }
}
