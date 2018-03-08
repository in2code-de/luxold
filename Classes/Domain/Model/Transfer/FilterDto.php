<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Model\Transfer;

use In2code\Lux\Domain\Model\Category;
use In2code\Lux\Domain\Repository\CategoryRepository;
use In2code\Lux\Utility\ObjectUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FilterDto is a filter class with show only results from the current year per default
 */
class FilterDto
{
    const PERIOD_THISYEAR = 0;
    const PERIOD_THISMONTH = 1;
    const PERIOD_LASTMONTH = 2;

    /**
     * @var string
     */
    protected $searchterm = '';

    /**
     * @var string
     */
    protected $pid = '';

    /**
     * @var string
     */
    protected $timeFrom = '';

    /**
     * @var string
     */
    protected $timeTo = '';

    /**
     * @var int
     */
    protected $scoring = 0;

    /**
     * Filter by categoryscoring greater then 0
     *
     * @var \In2code\Lux\Domain\Model\Category
     */
    protected $categoryScoring = null;

    /**
     * @var int
     */
    protected $timePeriod = self::PERIOD_THISYEAR;

    /**
     * @return string
     */
    public function getSearchterm(): string
    {
        return $this->searchterm;
    }

    /**
     * @return array
     */
    public function getSearchterms(): array
    {
        return GeneralUtility::trimExplode(' ', $this->getSearchterm(), true);
    }

    /**
     * @param string $searchterm
     * @return FilterDto
     */
    public function setSearchterm(string $searchterm)
    {
        $this->searchterm = $searchterm;
        return $this;
    }

    /**
     * @return string
     */
    public function getPid(): string
    {
        return $this->pid;
    }

    /**
     * @param string $pid
     * @return $this
     */
    public function setPid(string $pid)
    {
        $this->pid = $pid;
        return $this;
    }

    /**
     * @return string
     */
    public function getTimeFrom(): string
    {
        return $this->timeFrom;
    }

    /**
     * @return \DateTime
     */
    public function getTimeFromDateTime(): \DateTime
    {
        return new \DateTime($this->getTimeFrom());
    }

    /**
     * @param string $timeFrom
     * @return FilterDto
     */
    public function setTimeFrom(string $timeFrom)
    {
        $this->timeFrom = $timeFrom;
        return $this;
    }

    /**
     * @return string
     */
    public function getTimeTo(): string
    {
        return $this->timeTo;
    }

    /**
     * @return \DateTime
     */
    public function getTimeToDateTime(): \DateTime
    {
        return new \DateTime($this->getTimeTo());
    }

    /**
     * @param string $timeTo
     * @return FilterDto
     */
    public function setTimeTo(string $timeTo)
    {
        $this->timeTo = $timeTo;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimePeriod(): int
    {
        return $this->timePeriod;
    }

    /**
     * @param int $timePeriod
     * @return FilterDto
     */
    public function setTimePeriod(int $timePeriod)
    {
        $this->timePeriod = $timePeriod;
        return $this;
    }

    /**
     * @return int
     */
    public function getScoring(): int
    {
        return $this->scoring;
    }

    /**
     * @param int $scoring
     * @return FilterDto
     */
    public function setScoring(int $scoring)
    {
        $this->scoring = $scoring;
        return $this;
    }

    /**
     * @return Category
     */
    public function getCategoryScoring()
    {
        return $this->categoryScoring;
    }

    /**
     * @param int $categoryUid
     * @return FilterDto
     */
    public function setCategoryScoring(int $categoryUid)
    {
        if ($categoryUid > 0) {
            $categoryRepository = ObjectUtility::getObjectManager()->get(CategoryRepository::class);
            $category = $categoryRepository->findByUid((int)$categoryUid);
            if ($category !== null) {
                $this->categoryScoring = $category;
            }
        }
        return $this;
    }

    /**
     * Calculated values
     */

    /**
     * @return bool
     */
    public function isSet(): bool
    {
        return $this->searchterm !== '' || $this->pid !== '' || $this->scoring > 0 || $this->categoryScoring !== null
            || $this->timeFrom !== '' || $this->timeTo !== '' || $this->timePeriod !== self::PERIOD_THISYEAR;
    }

    /**
     * Get a start datetime for period filter
     *
     * @return \DateTime
     */
    public function getStartTimeForFilter(): \DateTime
    {
        if ($this->isTimeFromAndTimeToGiven()) {
            $time = $this->getTimeFromDateTime();
        } else {
            $time = $this->getStartTimeFromTimePeriod();
        }
        return $time;
    }

    /**
     * Get a stop datetime for period filter
     *
     * @return \DateTime
     */
    public function getEndTimeForFilter(): \DateTime
    {
        if ($this->isTimeFromAndTimeToGiven()) {
            $time = $this->getTimeToDateTime();
        } else {
            $time = $this->getEndTimeFromTimePeriod();
        }
        return $time;
    }

    /**
     * @return \DateTime
     */
    protected function getStartTimeFromTimePeriod(): \DateTime
    {
        $time = new \DateTime();
        if ($this->getTimePeriod() === self::PERIOD_THISYEAR) {
            $time = new \DateTime();
            $time->setDate((int)$time->format('Y'), 1, 1);
            $time->setTime(0, 0, 0);
        }
        if ($this->getTimePeriod() === self::PERIOD_THISMONTH) {
            $time = new \DateTime('first day of this month');
            $time->setTime(0, 0, 0);
        }
        if ($this->getTimePeriod() === self::PERIOD_LASTMONTH) {
            $time = new \DateTime('first day of last month');
            $time->setTime(0, 0, 0);
        }
        return $time;
    }

    /**
     * @return \DateTime
     */
    protected function getEndTimeFromTimePeriod(): \DateTime
    {
        $time = new \DateTime();
        if ($this->getTimePeriod() === self::PERIOD_LASTMONTH) {
            $time = new \DateTime('last day of last month');
            $time->setTime(23, 59, 59);
        }
        return $time;
    }

    /**
     * @return bool
     */
    protected function isTimeFromAndTimeToGiven(): bool
    {
        return $this->getTimeFrom() && $this->getTimeTo();
    }
}
