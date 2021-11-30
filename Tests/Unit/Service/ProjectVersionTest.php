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
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class ProjectVersionTest
 */
class ProjectVersionTest extends UnitTestCase
{
    /**
     * @var \KamiYang\ProjectVersion\Service\ProjectVersion
     */
    private $subject;

    protected function setUp(): void
    {
        $this->subject = new ProjectVersion();
    }

    /**
     * @test
     */
    public function getTitleShouldReturnInitialValue(): void
    {
        static::assertSame(
            'LLL:EXT:project_version/Resources/Private/Language/Backend.xlf:toolbarItems.sysinfo.project-version',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleShouldSetPropertyTitle(): void
    {
        $newValue = 'Project Version is awesome!';

        $this->subject->setTitle($newValue);

        static::assertSame(
            $newValue,
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function initialVersionValueShouldBeLLLString(): void
    {
        static::assertSame(
            'LLL:EXT:project_version/Resources/Private/Language/Backend.xlf:toolbarItems.sysinfo.project-version.unknown',
            $this->subject->getVersion()
        );
    }

    /**
     * @test
     */
    public function setVersionShouldSetPropertyVersion(): void
    {
        $newValue = 'Project Version is awesome!';

        $this->subject->setVersion($newValue);

        static::assertSame(
            $newValue,
            $this->subject->getVersion()
        );
    }

    /**
     * @test
     */
    public function getIconIdentifierShouldReturnInitialValue(): void
    {
        static::assertSame('information-project-version', $this->subject->getIconIdentifier());
    }

    /**
     * @test
     */
    public function setIconIdentifierShouldSetPropertyIconIdentifier(): void
    {
        $newValue = 'Project Version is awesome!';

        $this->subject->setIconIdentifier($newValue);

        static::assertSame(
            $newValue,
            $this->subject->getIconIdentifier()
        );
    }
}
