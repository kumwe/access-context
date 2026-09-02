<?php

declare(strict_types=1);

namespace Kumwe\Context\Value;

use Kumwe\Context\Exception\InvalidContext;

/**
 * Optional server-resolved workspace nested inside an authenticated organization.
 *
 * A workspace narrows organization-scoped work; it never widens it and never exists without the organization
 * that contains it, which is why a `MembershipContext` carries the pair together and a context exposes the
 * workspace only through that membership.
 *
 * @since  0.1.0
 */
final readonly class WorkspaceContext
{
    /**
     * Grammar every identifier satisfies once normalised: lowercase, at most 191 characters, query-safe.
     *
     * @var    string
     * @since  0.1.0
     */
    private const GRAMMAR = '/^[a-z0-9][a-z0-9._:-]{0,190}$/D';

    /**
     * Hold a normalised workspace identifier.
     *
     * @param  string  $identifier  Validated lowercase workspace identifier.
     *
     * @since  0.1.0
     */
    private function __construct(private string $identifier)
    {
    }

    /**
     * Normalise and validate a workspace identifier read from trusted storage.
     *
     * @param   string  $identifier  Raw workspace identifier to normalise and validate.
     *
     * @return  self  Validated workspace context.
     *
     * @throws  InvalidContext  When the normalised value is empty, runs past 191 characters, starts with
     *          something other than a lowercase letter or digit, or holds a character outside `a-z`, `0-9`,
     *          `.`, `_`, `:` and `-`.
     *
     * @since   0.1.0
     */
    public static function fromString(string $identifier): self
    {
        $identifier = strtolower(trim($identifier));

        if (preg_match(self::GRAMMAR, $identifier) !== 1) {
            throw new InvalidContext('A workspace context must be a valid non-empty identifier.');
        }

        return new self($identifier);
    }

    /**
     * Expose the normalised workspace identifier.
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
     * Whether another workspace context names the same workspace.
     *
     * @param   self  $other  Workspace context to compare against.
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
