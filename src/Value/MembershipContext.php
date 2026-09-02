<?php

declare(strict_types=1);

namespace Kumwe\Context\Value;

use Kumwe\Context\Exception\InvalidContext;

/**
 * Versioned proof that a principal currently belongs to an organization and an optional workspace.
 *
 * The membership and policy generations are included in authorization fingerprints, cursors and delegations. A
 * host must re-read them from live state inside a mutation transaction; a stale snapshot therefore fails rather
 * than retaining authority after a membership or policy change. The snapshot is a credential, not authority: it
 * says what the host resolved, and the host decides on every use whether that is still true.
 *
 * @since  0.1.0
 */
final readonly class MembershipContext
{
    /**
     * Canonical lowercase UUID grammar the membership row identity must satisfy.
     *
     * @var    string
     * @since  0.1.0
     */
    private const UUID = '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/D';

    /**
     * Validate a server-resolved membership snapshot.
     *
     * @param   string               $membershipId       Stable lowercase UUID of the membership row.
     * @param   OrganizationContext  $organization       Organization conferred by the membership.
     * @param   ?WorkspaceContext    $workspace          Workspace selection, when the operation is narrower.
     * @param   int                  $membershipVersion  Positive optimistic version of the membership.
     * @param   int                  $policyGeneration   Positive organization policy generation.
     *
     * @throws  InvalidContext  When the row identity is not a canonical lowercase UUID or either generation is
     *          below one.
     *
     * @since   0.1.0
     */
    public function __construct(
        private string $membershipId,
        private OrganizationContext $organization,
        private ?WorkspaceContext $workspace,
        private int $membershipVersion,
        private int $policyGeneration,
    ) {
        if (preg_match(self::UUID, $membershipId) !== 1) {
            throw new InvalidContext('A membership context requires a valid UUID.');
        }
        if ($membershipVersion < 1 || $policyGeneration < 1) {
            throw new InvalidContext('Membership and policy generations must be positive.');
        }
    }

    /**
     * Return the stable membership row identity.
     *
     * @return  string  Canonical lowercase UUID.
     *
     * @since   0.1.0
     */
    public function membershipId(): string
    {
        return $this->membershipId;
    }

    /**
     * Return the organization the membership confers.
     *
     * @return  OrganizationContext  Selected organization.
     *
     * @since   0.1.0
     */
    public function organization(): OrganizationContext
    {
        return $this->organization;
    }

    /**
     * Return the optional workspace selected inside the organization.
     *
     * @return  ?WorkspaceContext  Selected workspace, or null for organization-wide work.
     *
     * @since   0.1.0
     */
    public function workspace(): ?WorkspaceContext
    {
        return $this->workspace;
    }

    /**
     * Return the optimistic version of the membership row the snapshot was read at.
     *
     * @return  int  Positive membership version.
     *
     * @since   0.1.0
     */
    public function membershipVersion(): int
    {
        return $this->membershipVersion;
    }

    /**
     * Return the organization policy generation the snapshot was read at.
     *
     * @return  int  Positive policy generation.
     *
     * @since   0.1.0
     */
    public function policyGeneration(): int
    {
        return $this->policyGeneration;
    }

    /**
     * Digest every membership value that may change an authorization decision.
     *
     * @return  string  Lowercase hexadecimal SHA-256 over the row identity, organization, workspace and both
     *          generations, in that order.
     *
     * @since   0.1.0
     */
    public function fingerprint(): string
    {
        return hash('sha256', implode("\n", [
            $this->membershipId,
            $this->organization->identifier(),
            $this->workspace?->identifier() ?? '-',
            (string) $this->membershipVersion,
            (string) $this->policyGeneration,
        ]));
    }

    /**
     * Whether another snapshot records exactly the same membership at the same generations.
     *
     * @param   self  $other  Snapshot to compare against.
     *
     * @return  bool  True when every fingerprinted value is the same.
     *
     * @since   0.1.0
     */
    public function equals(self $other): bool
    {
        return $this->fingerprint() === $other->fingerprint();
    }

    /**
     * Export the snapshot as plain values for logging, storage beside a decision or transport.
     *
     * @return  array{
     *              membership_id: string,
     *              organization: string,
     *              workspace: ?string,
     *              membership_version: int,
     *              policy_generation: int
     *          }  Every value the fingerprint covers; nothing here is secret.
     *
     * @since   0.1.0
     */
    public function toArray(): array
    {
        return [
            'membership_id' => $this->membershipId,
            'organization' => $this->organization->identifier(),
            'workspace' => $this->workspace?->identifier(),
            'membership_version' => $this->membershipVersion,
            'policy_generation' => $this->policyGeneration,
        ];
    }
}
