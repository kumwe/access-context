<?php

declare(strict_types=1);

namespace Kumwe\Context\Value;

/**
 * Delivery boundary through which an execution context authenticated.
 *
 * The surface is a fact about how the unit of work entered the host, recorded so a policy can confine an action
 * to the boundary it was designed for and an audit reviewer can separate browser, token and unattended traffic.
 * A host maps its own delivery adapters onto these cases; the package neither infers a surface from a request
 * nor lets one surface stand in for another.
 *
 * @since  0.1.0
 */
enum AuthenticatedSurface: string
{
    /**
     * Administrator browser session, never accepted by portal-only work.
     *
     * @since  0.1.0
     */
    case Administrator = 'administrator';

    /**
     * Ordinary-user portal browser session, never accepted by administrator or recovery work.
     *
     * @since  0.1.0
     */
    case Portal = 'portal';

    /**
     * Public REST application programming interface, credentialed by a bearer token.
     *
     * @since  0.1.0
     */
    case Api = 'api';

    /**
     * Model Context Protocol tool surface, credentialed by a bearer token.
     *
     * @since  0.1.0
     */
    case Mcp = 'mcp';

    /**
     * Command-line management surface operated by a person.
     *
     * @since  0.1.0
     */
    case Cli = 'cli';

    /**
     * Constrained unattended worker or schedule; the only surface a system context may use.
     *
     * @since  0.1.0
     */
    case Background = 'background';

    /**
     * Installation recovery surface, isolated from normal authentication.
     *
     * @since  0.1.0
     */
    case Recovery = 'recovery';
}
