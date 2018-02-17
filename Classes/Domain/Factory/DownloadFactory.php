<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Factory;

use In2code\Lux\Domain\Model\Download;
use In2code\Lux\Domain\Model\Visitor;
use In2code\Lux\Domain\Repository\DownloadRepository;
use In2code\Lux\Domain\Repository\VisitorRepository;
use In2code\Lux\Domain\Service\ConfigurationService;
use In2code\Lux\Signal\SignalTrait;
use In2code\Lux\Utility\ObjectUtility;

/**
 * Class DownloadFactory to get an existing visitor and add another download
 */
class DownloadFactory
{
    use SignalTrait;

    /**
     * @var string
     */
    protected $idCookie = '';

    /**
     * @var VisitorRepository|null
     */
    protected $visitorRepository = null;

    /**
     * AttributeFactory constructor.
     *
     * @param string $idCookie
     */
    public function __construct(string $idCookie)
    {
        $this->idCookie = $idCookie;
        $this->visitorRepository = ObjectUtility::getObjectManager()->get(VisitorRepository::class);
    }

    /**
     * @param string $href
     * @return Visitor
     */
    public function getVisitorAndAddDownload(string $href): Visitor
    {
        $visitor = $this->getVisitorFromDatabase();
        if (!empty($href) && $this->isEnabledDownloadTracking()) {
            $download = $this->getAndPersistNewDownload($href);
            $visitor->addDownload($download);
            $download->setVisitor($visitor);
            $this->visitorRepository->update($visitor);
            $this->visitorRepository->persistAll();
            $this->signalDispatch(__CLASS__, 'addDownload', [$download, $visitor]);
        }
        return $visitor;
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
     * @return Visitor|null
     */
    protected function getVisitorFromDatabase()
    {
        return $this->visitorRepository->findOneByIdCookie($this->idCookie);
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
