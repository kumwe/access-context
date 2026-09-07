<?php

declare(strict_types=1);

namespace Kumwe\Context\Value;

use Kumwe\Context\Exception\InvalidContext;

/**
 * Server-resolved organization in which an authorized unit of work executes.
 *
 * Unlike an organization value a caller submits with a command, this object belongs to the authenticated
 * execution context: only a host adapter holding the issuing provenance places it there, through a
 * `MembershipContext`, so a submitted identifier can be compared with it but never establish it. An
 * organization is never a site: `SiteContext` shares the grammar and nothing else, and a context carries both
 * facts separately so neither can stand in for the other.
 *
 * @since  0.1.0
 */
final readonly class OrganizationContext
{
    /**
     * Grammar every identifier satisfies once normalised: lowercase, at most 191 characters, query-safe.
     *
     * @var    string
     * @since  0.1.0
     */
    private const GRAMMAR = '/^[a-z0-9][a-z0-9._:-]{0,190}$/D';

    /**
     * Hold a normalised organization identifier.
     *
     * @param  string  $identifier  Validated lowercase organization identifier.
     *
     * @since  0.1.0
     */
    private function __construct(private string $identifier)
    {
    }

    /**
     * Normalise and validate an organization identifier read from trusted membership storage.
     *
     * @param   string  $identifier  Raw organization identifier to normalise and validate.
     *
     * @return  self  Validated organization context.
     *
     * @throws  InvalidContext  When raw input contains control characters, or the normalized value is empty,
     *          runs past 191 characters, starts with
     *          something other than a lowercase letter or digit, or holds a character outside `a-z`, `0-9`,
     *          `.`, `_`, `:` and `-`.
     *
     * @since   0.1.0
     */
    public static function fromString(string $identifier): self
    {
        if (preg_match('/[\x00-\x1F\x7F]/', $identifier) === 1) {
            throw new InvalidContext('An organization context must be a valid non-empty identifier.');
        }
        $identifier = strtolower(trim($identifier));

        if (preg_match(self::GRAMMAR, $identifier) !== 1) {
            throw new InvalidContext('An organization context must be a valid non-empty identifier.');
        }

        return new self($identifier);
    }

    /**
     * Expose the normalised organization identifier.
     *
     * @return  string  Identifier safe for exact comparison and query binding.
     *
     * @since   0.1.0
     */
    public function identifier(): string
    {
        return $this->identifier;
    }

    /**
     * Whether another organization context names the same organization.
     *
     * @param   self  $other  Organization context to compare against.
     *
     * @return  bool  True when both carry the same normalised identifier.
     *
     * @since   0.1.0
     */
    public function equals(self $other): bool
    {
        return $this->identifier === $other->identifier;
    }
}
