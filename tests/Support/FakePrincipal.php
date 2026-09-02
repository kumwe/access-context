<?php

/**
 * Test-scoped principal double; never a production implementation.
 *
 * @since  0.1.0
 */

declare(strict_types=1);

namespace Kumwe\Context\Tests\Support;

use Kumwe\Context\Contract\Principal;

/**
 * Principal whose every claim is a constructor input, so a test can shape any host-side fact it needs.
 *
 * @since  0.1.0
 */
final readonly class FakePrincipal implements Principal
{
    /**
     * Hold the claims the test chose.
     *
     * @param  object  $provenance     Authority the principal answers `hasProvenance()` for.
     * @param  string  $subject        Stable actor identifier.
     * @param  int     $securityEpoch  Authorization epoch.
     * @param  string  $authority      Seed for the two fingerprints; grants are simulated by this string.
     * @param  string  $credential     Seed that distinguishes the credential-bound fingerprint.
     *
     * @since  0.1.0
     */
    public function __construct(
        private object $provenance,
        private string $subject = '018f22e2-7c8b-7ab0-8f3a-88e8026bb301',
        private int $securityEpoch = 1,
        private string $authority = 'content.read:global',
        private string $credential = 'api-token:first',
    ) {
    }

    /**
     * Return the chosen subject.
     *
     * @return  string  Actor identifier as given.
     *
     * @since   0.1.0
     */
    public function subject(): string
    {
        return $this->subject;
    }

    /**
     * Return the chosen epoch.
     *
     * @return  int  Epoch as given.
     *
     * @since   0.1.0
     */
    public function securityEpoch(): int
    {
        return $this->securityEpoch;
    }

    /**
     * Compare by object identity, as a host implementation must.
     *
     * @param   object  $provenance  Authority to test.
     *
     * @return  bool  True for the very object given at construction.
     *
     * @since   0.1.0
     */
    public function hasProvenance(object $provenance): bool
    {
        return $this->provenance === $provenance;
    }

    /**
     * Digest subject, credential, epoch and simulated grants.
     *
     * @return  string  Lowercase hexadecimal SHA-256.
     *
     * @since   0.1.0
     */
    public function authorizationFingerprint(): string
    {
        return hash(
            'sha256',
            implode("\n", [$this->subject, $this->credential, $this->securityEpoch, $this->authority]),
        );
    }

    /**
     * Digest subject, epoch and simulated grants without the credential.
     *
     * @return  string  Lowercase hexadecimal SHA-256.
     *
     * @since   0.1.0
     */
    public function authorityFingerprint(): string
    {
        return hash('sha256', implode("\n", [$this->subject, $this->securityEpoch, $this->authority]));
    }
}
