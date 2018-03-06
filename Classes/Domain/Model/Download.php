<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class Download
 */
class Download extends AbstractEntity
{
    const TABLE_NAME = 'tx_lux_domain_model_download';

    /**
     * @var \In2code\Lux\Domain\Model\Visitor
     */
    protected $visitor = null;

    /**
     * @var \DateTime|null
     */
    protected $crdate = null;

    /**
     * @var string
     */
    protected $href = '';

    /**
     * @var \In2code\Lux\Domain\Model\File
     */
    protected $file = null;

    /**
     * @return Visitor
     */
    public function getVisitor()
    {
        return $this->visitor;
    }

    /**
     * @param Visitor $visitor
     * @return Download
     */
    public function setVisitor(Visitor $visitor): Download
    {
        $this->visitor = $visitor;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCrdate(): \DateTime
    {
        return $this->crdate;
    }

    /**
     * @param \DateTime $crdate
     * @return Download
     */
    public function setCrdate(\DateTime $crdate): Download
    {
        $this->crdate = $crdate;
        return $this;
    }

    /**
     * @return string
     */
    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * @param string $href
     * @return Download
     */
    public function setHref(string $href): Download
    {
        $this->href = $href;
        return $this;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @param File $file
     * @return Download
     */
    public function setFile(File $file)
    {
        $this->file = $file;
        return $this;
    }
}
