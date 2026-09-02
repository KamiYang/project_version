<?php

declare(strict_types=1);

/*
 * This file is part of the ProjectVersion project.
 *
 * It is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * For the full copyright and license information, please read
 * LICENSE file that was distributed with this source code.
 */

namespace KamiYang\ProjectVersion\Enumeration;

/**
 * Output format of the git based project version. Values match the options in ext_conf_template.txt.
 */
enum GitFormat: string
{
    case Revision = '0';
    case RevisionBranch = '1';
    case RevisionTag = '2';
    case Branch = '3';
    case Tag = '4';

    /**
     * Render the given git information according to this format.
     */
    public function format(string $revision, string $branch, string $tag): string
    {
        return match ($this) {
            self::Revision => $revision,
            self::RevisionBranch => sprintf('[%s] %s', $revision, $branch),
            self::RevisionTag => sprintf('[%s] %s', $revision, $tag),
            self::Branch => $branch,
            self::Tag => $tag,
        };
    }
}
