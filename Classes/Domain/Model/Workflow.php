<?php
declare(strict_types=1);
namespace In2code\Lux\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class Workflow
 */
class Workflow extends AbstractEntity
{
    const TABLE_NAME = 'tx_lux_domain_model_workflow';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var \DateTime
     */
    protected $crdate = null;

    /**
     * @var \In2code\Lux\Domain\Model\User
     */
    protected $cruserId = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\Lux\Domain\Model\Trigger>
     */
    protected $triggers = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\Lux\Domain\Model\Action>
     */
    protected $actions = null;

    /**
     * Workflow constructor.
     */
    public function __construct()
    {
        $this->triggers = new ObjectStorage();
        $this->actions = new ObjectStorage();
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Workflow
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Workflow
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCrdate()
    {
        return $this->crdate;
    }

    /**
     * @param \DateTime $crdate
     * @return Workflow
     */
    public function setCrdate(\DateTime $crdate)
    {
        $this->crdate = $crdate;
        return $this;
    }

    /**
     * @return User
     */
    public function getCruserId()
    {
        return $this->cruserId;
    }

    /**
     * @param User $cruserId
     * @return Workflow
     */
    public function setCruserId(User $cruserId)
    {
        $this->cruserId = $cruserId;
        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getTriggers(): ObjectStorage
    {
        return $this->triggers;
    }

    /**
     * @param ObjectStorage $triggers
     * @return Workflow
     */
    public function setTriggers(ObjectStorage $triggers)
    {
        $this->triggers = $triggers;
        return $this;
    }

    /**
     * @param Trigger $trigger
     * @return $this
     */
    public function addTrigger(Trigger $trigger)
    {
        $this->triggers->attach($trigger);
        return $this;
    }

    /**
     * @param Trigger $trigger
     * @return $this
     */
    public function removeTrigger(Trigger $trigger)
    {
        $this->triggers->detach($trigger);
        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getActions(): ObjectStorage
    {
        return $this->actions;
    }

    /**
     * @param ObjectStorage $actions
     * @return Workflow
     */
    public function setActions(ObjectStorage $actions)
    {
        $this->actions = $actions;
        return $this;
    }

    /**
     * @param Action $action
     * @return $this
     */
    public function addPagevisit(Action $action)
    {
        $this->actions->attach($action);
        return $this;
    }

    /**
     * @param Action $action
     * @return $this
     */
    public function removePagevisit(Action $action)
    {
        $this->actions->detach($action);
        return $this;
    }
}
