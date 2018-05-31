<?php
declare(strict_types=1);

namespace KamiYang\ProjectVersion\Service;

use KamiYang\ProjectVersion\Configuration\ExtensionConfiguration;
use KamiYang\ProjectVersion\Enumeration\ProjectVersionModeEnumeration;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\CommandUtility;
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

                if ($projectVersion->getVersion() !== ProjectVersion::UNKNOWN_VERSION) {
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
     * @param \KamiYang\ProjectVersion\Service\ProjectVersion $projectVersion
     */
    private function setVersionFromGit(ProjectVersion $projectVersion)
    {
        if ($this->isGitAvailable() === false) {
            return;
        }
    }

    /**
     * @return bool
     */
    private function isGitAvailable(): bool
    {
        return SystemEnvironmentBuilder::isFunctionDisabled('exec') === false &&
            // check if git exists
            CommandUtility::exec('git --version', $_, $returnCode) &&
            $returnCode === 0;
    }
}
