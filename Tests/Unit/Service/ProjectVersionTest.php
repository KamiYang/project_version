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
        $subject = new ProjectVersion();

        self::assertSame(ProjectVersion::DEFAULT_TITLE, $subject->getTitle());
        self::assertSame(ProjectVersion::UNKNOWN_VERSION, $subject->getVersion());
        self::assertSame('information-project-version', $subject->getIconIdentifier());
        self::assertTrue($subject->isUnknown());
    }

    #[Test]
    public function settersChangeState(): void
    {
        $subject = new ProjectVersion();
        $subject->setTitle('Title');
        $subject->setVersion('1.0.0');
        $subject->setIconIdentifier('information-git');

        self::assertSame('Title', $subject->getTitle());
        self::assertSame('1.0.0', $subject->getVersion());
        self::assertSame('information-git', $subject->getIconIdentifier());
        self::assertFalse($subject->isUnknown());
    }
}
