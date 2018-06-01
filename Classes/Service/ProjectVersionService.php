<?php
declare(strict_types=1);

namespace KamiYang\ProjectVersion\Service;

use KamiYang\ProjectVersion\Configuration\ExtensionConfiguration;
use KamiYang\ProjectVersion\Enumeration\GitCommandEnumeration;
use KamiYang\ProjectVersion\Enumeration\ProjectVersionModeEnumeration;
use KamiYang\ProjectVersion\Facade\CommandUtilityFacade;
use KamiYang\ProjectVersion\Facade\SystemEnvironmentBuilderFacade;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ProjectVersionService
 *
 * @package KamiYang\ProjectVersion\Service
 * @author Jan Stockfisch <jan.stockfisch@googlemail.com>
 */
class ProjectVersionService implements SingletonInterface
{
    /**
     * @var \KamiYang\ProjectVersion\Facade\CommandUtilityFacade
     */
    protected $commandUtilityFacade;

    /**
     * @var \KamiYang\ProjectVersion\Facade\SystemEnvironmentBuilderFacade
     */
    protected $systemEnvironmentBuilderFacade;

    /**
     * @api
     */
    public function getProjectVersion(): ProjectVersion
    {
        $projectVersion = GeneralUtility::makeInstance(ProjectVersion::class);

        switch (ExtensionConfiguration::getMode()) {
            case ProjectVersionModeEnumeration::GIT:
                $this->setVersionFromGit($projectVersion);
                break;
            case ProjectVersionModeEnumeration::GIT_FILE_FALLBACK:
                $this->setVersionFromGit($projectVersion);

                if ($projectVersion->getVersion() === ProjectVersion::UNKNOWN_VERSION) {
                    //if version is still unknown, try to resolve version by file
                    $this->setVersionFromFile($projectVersion);
                }
                break;
            case ProjectVersionModeEnumeration::FILE:
            default:
                $this->setVersionFromFile($projectVersion);
        }


        return $projectVersion;
    }

    /**
     * Resolve version by common VERSION-file.
     *
     * @param \KamiYang\ProjectVersion\Service\ProjectVersion $projectVersion
     */
    private function setVersionFromFile(ProjectVersion $projectVersion)
    {
        $versionFilePath = ExtensionConfiguration::getAbsVersionFilePath();
        if (\file_exists($versionFilePath)) {
            $versionFileContent = \file_get_contents($versionFilePath);
            $projectVersion->setVersion($versionFileContent);
        }
    }

    /**
     * ProjectVersionService constructor.
     *
     * @param \KamiYang\ProjectVersion\Facade\CommandUtilityFacade $commandUtilityFacade
     */
    public function __construct(
        CommandUtilityFacade $commandUtilityFacade,
        SystemEnvironmentBuilderFacade $systemEnvironmentBuilderFacade
    ) {
        $this->commandUtilityFacade = $commandUtilityFacade;
        $this->systemEnvironmentBuilderFacade = $systemEnvironmentBuilderFacade;
    }

    /**
     * @param \KamiYang\ProjectVersion\Service\ProjectVersion $projectVersion
     */
    private function setVersionFromGit(ProjectVersion $projectVersion)
    {
        if ($this->isGitAvailable() === false) {
            return;
        }

        $version = $this->getVersionByFormat();

        if (!empty($version)) {
            $projectVersion->setVersion($version);
            $projectVersion->setIconIdentifier('sysinfo-git');
        }
    }

    /**
     * @return bool
     */
    private function isGitAvailable(): bool
    {
        return $this->systemEnvironmentBuilderFacade->isFunctionDisabled('exec') === false &&
            // check if git exists
            $this->commandUtilityFacade->exec('git --version', $_, $returnCode) &&
            $returnCode === 0;
    }

    /**
     * @return string
     */
    private function getVersionByFormat(): string
    {
        $branch = \trim($this->commandUtilityFacade->exec(GitCommandEnumeration::CMD_BRANCH));
        $revision = \trim($this->commandUtilityFacade->exec(GitCommandEnumeration::CMD_REVISION));
        $tag = \trim($this->commandUtilityFacade->exec(GitCommandEnumeration::CMD_TAG));

        switch (ExtensionConfiguration::getGitFormat()) {
            case GitCommandEnumeration::FORMAT_REVISION:
                $format = $revision;
                break;
            case GitCommandEnumeration::FORMAT_REVISION_TAG:
                $format = \sprintf('[%s] %s', $revision, $tag);
                break;
            case GitCommandEnumeration::FORMAT_BRANCH:
                $format = $branch;
                break;
            case GitCommandEnumeration::FORMAT_TAG:
                $format = $tag;
                break;
            case GitCommandEnumeration::FORMAT_REVISION_BRANCH:
            default:
                $format = \sprintf('[%s] %s', $revision, $branch);
        }

        return $format;
    }
}
