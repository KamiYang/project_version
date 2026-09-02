<?php

declare(strict_types=1);

namespace KamiYang\ProjectVersion\Tests\Unit\Service;

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

use KamiYang\ProjectVersion\Service\ProjectVersion;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ProjectVersionTest extends UnitTestCase
{
    #[Test]
    public function hasSensibleDefaults(): void
    {
        $projectVersion = new ProjectVersion();

        self::assertSame(ProjectVersion::DEFAULT_TITLE, $projectVersion->getTitle());
        self::assertSame(ProjectVersion::UNKNOWN_VERSION, $projectVersion->getVersion());
        self::assertSame('information-project-version', $projectVersion->getIconIdentifier());
        self::assertTrue($projectVersion->isUnknown());
    }

    #[Test]
    public function settersChangeState(): void
    {
        $projectVersion = new ProjectVersion();
        $projectVersion->setTitle('Title');
        $projectVersion->setVersion('1.0.0');
        $projectVersion->setIconIdentifier('information-git');

        self::assertSame('Title', $projectVersion->getTitle());
        self::assertSame('1.0.0', $projectVersion->getVersion());
        self::assertSame('information-git', $projectVersion->getIconIdentifier());
        self::assertFalse($projectVersion->isUnknown());
    }
}
