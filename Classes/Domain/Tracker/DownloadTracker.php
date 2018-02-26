<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Tracker;

use In2code\Lux\Domain\Model\Download;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Repository\DownloadRepository;
use In2code\Lux\Domain\Repository\VisitorRepository;
use In2code\Lux\Domain\Service\ConfigurationService;
use In2code\Lux\Signal\SignalTrait;
use In2code\Lux\Utility\ObjectUtility;

/**
 * Class DownloadTracker add a download record to a visitor
 */
class DownloadTracker
{
    use SignalTrait;

    /**
     * @var Visitor|null
     */
    protected $visitor = null;

    /**
     * @var VisitorRepository|null
     */
    protected $visitorRepository = null;

    /**
     * DownloadTracker constructor.
     *
     * @param Visitor $visitor
     */
    public function __construct(Visitor $visitor)
    {
        $this->visitor = $visitor;
        $this->visitorRepository = ObjectUtility::getObjectManager()->get(VisitorRepository::class);
    }

    /**
     * @param string $href
     * @return void
     */
    public function addDownload(string $href)
    {
        if (!empty($href) && $this->isEnabledDownloadTracking()) {
            $download = $this->getAndPersistNewDownload($href);
            $this->visitor->addDownload($download);
            $download->setVisitor($this->visitor);
            $this->visitorRepository->update($this->visitor);
            $this->visitorRepository->persistAll();
            $this->signalDispatch(__CLASS__, 'addDownload', [$download, $this->visitor]);
        }
    }

    /**
     * @param string $href
     * @return Download
     */
    protected function getAndPersistNewDownload(string $href): Download
    {
        $downloadRepository = ObjectUtility::getObjectManager()->get(DownloadRepository::class);
        $download = ObjectUtility::getObjectManager()->get(Download::class)->setHref($href);
        $downloadRepository->add($download);
        $downloadRepository->persistAll();
        return $download;
    }

    /**
     * @return bool
     */
    protected function isEnabledDownloadTracking(): bool
    {
        $configurationService = ObjectUtility::getObjectManager()->get(ConfigurationService::class);
        $settings = $configurationService->getTypoScriptSettings();
        return !empty($settings['tracking']['assetDownloads']['_enable'])
            && $settings['tracking']['assetDownloads']['_enable'] === '1';
    }
}
