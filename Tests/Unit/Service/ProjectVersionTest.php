<?php

declare(strict_types=1);

namespace KamiYang\ProjectVersion\Tests\Unit\Service;

use KamiYang\ProjectVersion\Service\ProjectVersion;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class ProjectVersionTest.
 *
 * @author Jan Stockfisch <j.stockfisch@neusta.de>
 */
class ProjectVersionTest extends UnitTestCase
{
    /**
     * @var \KamiYang\ProjectVersion\Service\ProjectVersion
     */
    private $subject;

    protected function setUp()
    {
        $this->subject = new ProjectVersion();
    }

    /**
     * @test
     */
    public function getTitleShouldReturnInitialValue()
    {
        static::assertSame(
            'Project Version',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleShouldSetPropertyTitle()
    {
        $newValue = 'Project Version is awesome!';

        $this->subject->setTitle($newValue);

        static::assertAttributeSame($newValue, 'title', $this->subject);
    }

    /**
     * @test
     */
    public function getVersionShouldReturnInitialValue()
    {
        static::assertSame(
            'Unknown project version',
            $this->subject->getVersion()
        );
    }

    /**
     * @test
     */
    public function setVersionShouldSetPropertyVersion()
    {
        $newValue = 'Project Version is awesome!';

        $this->subject->setVersion($newValue);

        static::assertAttributeSame($newValue, 'version', $this->subject);
    }

    /**
     * @test
     */
    public function getIconIdentifierShouldReturnInitialValue()
    {
        static::assertSame('', $this->subject->getIconIdentifier());
    }

    /**
     * @test
     */
    public function setIconIdentifierShouldSetPropertyIconIdentifier()
    {
        $newValue = 'Project Version is awesome!';

        $this->subject->setIconIdentifier($newValue);

        static::assertAttributeSame($newValue, 'iconIdentifier', $this->subject);
    }
}
