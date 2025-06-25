<?php
declare(strict_types=1);

namespace HauerHeinrich\HhVideoExtender\Upgrades;

use \Symfony\Component\Console\Output\OutputInterface;
// use \TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use \TYPO3\CMS\Install\Attribute\UpgradeWizard;
use \TYPO3\CMS\Install\Updates\UpgradeWizardInterface;
use \TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use \TYPO3\CMS\Install\Updates\ChattyInterface;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Database\ConnectionPool;

#[UpgradeWizard('hhVideoExtender_changeRelatedVideosFieldNameUpgradeWizard')]
final class ChangeRelatedVideosFieldNameUpgradeWizard implements UpgradeWizardInterface, ChattyInterface {

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * Setter injection for output into upgrade wizards
     */
    public function setOutput(OutputInterface $output): void {
        $this->output = $output;
    }

    /**
     * Return the speaking name of this wizard
     */
    public function getTitle(): string {
        return 'EXT:hh_video_extender - Migrate "relatedVideos" to "related_videos" in sys_file_reference"';
    }

    /**
     * Return the description for this wizard
     */
    public function getDescription(): string {
        return 'Update DB:field - This wizard migrates the contents of the deprecated column "relatedVideos" to the new "related_videos" column and drops the old one.';
    }

    /**
     * Execute the update
     *
     * Called when a wizard reports that an update is necessary
     */
    public function executeUpdate(): bool {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('sys_file_reference');
        $schemaManager = $connection->createSchemaManager();

        $columns = $schemaManager->listTableColumns('sys_file_reference');

        if (!isset($columns['relatedVideos'])) {
            return true; // Nichts zu tun
        }

        // Prüfen, ob neue Spalte vorhanden ist
        if (!isset($columns['related_videos'])) {
            $this->output->writeln('The column "related_videos" does not exist. Please add it first via ext_tables.sql or Doctrine migration.');
            return false;
            // throw new \RuntimeException('The column "related_videos" does not exist. Please add it first via ext_tables.sql or Doctrine migration.');
        }

        // Daten migrieren
        $connection->executeStatement(
            'UPDATE sys_file_reference SET related_videos = relatedVideos WHERE relatedVideos IS NOT NULL'
        );

        return true;
    }

    /**
     * Is an update necessary?
     *
     * Is used to determine whether a wizard needs to be run.
     * Check if data for migration exists.
     *
     * @return bool Whether an update is required (TRUE) or not (FALSE)
     */
    public function updateNecessary(): bool {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('sys_file_reference');

        $schemaManager = $connection->createSchemaManager();

        // Prüfen, ob Spalte existiert
        if ($schemaManager->tablesExist(['sys_file_reference'])) {
            $columns = $schemaManager->listTableColumns('sys_file_reference');

            if (isset($columns['relatedVideos'])) {
                return true;
            }
        }

        // Wenn das Feld nicht existiert, kein Update nötig
        return false;
    }

    /**
     * Returns an array of class names of prerequisite classes
     *
     * This way a wizard can define dependencies like "database up-to-date" or
     * "reference index updated"
     *
     * @return string[]
     */
    public function getPrerequisites(): array {
        // Add your logic here
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }
}
