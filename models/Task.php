<?php

namespace ApiClient\Model;

use ApiClient\App\Status;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumns;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\JoinTable;

/**
 * Task
 *
 * @Table(name="ApiClientTask", indexes={@Index(name="action", columns={"action"}), @Index(name="transfer", columns={"transfer"})})
 * @Entity(repositoryClass="ApiClient\Repository\TaskRepository")
 */
class Task
{
    /**
     * @var int
     *
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @Column(name="parameters", type="json", nullable=false, options={"comment"="Параметры задачи в формате JSON"})
     */
    private $parameters;

    /**
     * @var integer
     *
     * @Column(name="creditId", type="integer", nullable=false, options={"comment"="Идентификатор кредита"})
     */
    private $creditId;

    /**
     * @var DateTime
     *
     * @Column(name="createDatetime", type="datetime", nullable=false, options={"comment"="Время создания"})
     */
    private $createDatetime;

    /**
     * @var string
     *
     * @Column(name="status", type="string", nullable=false, options={"comment"="Статусы выполнения задачи"})
     */
    private $status;

    /**
     * @var integer
     *
     * @Column(name="attempt", type="integer", nullable=false, options={"comment"="Идентификатор кредита"})
     */
    private $attempt;

    /**
     * @var boolean
     *
     * @Column(name="inWork", type="boolean", nullable=false, options={"comment"="Идентификатор кредита"})
     */
    private $inWork;

    /**
     * @var string
     *
     * @Column(name="description", type="string", nullable=true, options={"comment"="Комментарий к задаче от сервера"})
     */
    private $description;

    /**
     * @var Action
     *
     * @ManyToOne(targetEntity="ApiClient\Model\Action")
     * @JoinColumns({
     *   @JoinColumn(name="action", referencedColumnName="id"),
     * })
     */
    private $action;

    /**
     * @var Task
     *
     * @ManyToMany(targetEntity="ApiClient\Model\Transfer", inversedBy="tasks")
     * @JoinTable(name="ApiClientTaskTransfer")
     */
    private $transfers;

    public function __construct()
    {
        $this->createDatetime = new DateTime();
        $this->status = Status::OPEN;
        $this->inWork = false;
        $this->attempt = 0;
        $this->parameters = json_encode([]);
        $this->transfers = new ArrayCollection();
    }

    /**
     * Set id.
     *
     * @return int
     */
    public function setId(int $id): Task
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set parameters.
     *
     * @param array $parameters
     *
     * @return Task
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set creditId.
     *
     * @param int $creditId
     *
     * @return Task
     */
    public function setCreditId($creditId)
    {
        $this->creditId = $creditId;

        return $this;
    }

    /**
     * Get creditId.
     *
     * @return int
     */
    public function getCreditId()
    {
        return $this->creditId;
    }

    /**
     * Set createDatetime.
     *
     * @param DateTime $createDatetime
     *
     * @return Task
     */
    public function setCreateDatetime($createDatetime)
    {
        $this->createDatetime = $createDatetime;

        return $this;
    }

    /**
     * Get createDatetime.
     *
     * @return DateTime
     */
    public function getCreateDatetime()
    {
        return $this->createDatetime;
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return Task
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set attempt.
     *
     * @param int $attempt
     *
     * @return Task
     */
    public function setAttempt($attempt)
    {
        $this->attempt = $attempt;

        return $this;
    }

    /**
     * Get attempt.
     *
     * @return int
     */
    public function getAttempt()
    {
        return $this->attempt;
    }

    /**
     * Set inWork.
     *
     * @param bool $inWork
     *
     * @return Task
     */
    public function setInWork($inWork)
    {
        $this->inWork = $inWork;

        return $this;
    }

    /**
     * Get inWork.
     *
     * @return bool
     */
    public function getInWork()
    {
        return $this->inWork;
    }

    /**
     * Set action.
     *
     * @param Action|null $action
     *
     * @return Task
     */
    public function setAction(Action $action = null)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action.
     *
     * @return Action|null
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Add transfer.
     *
     * @param Transfer $transfer
     *
     * @return Task
     */
    public function addTransfer(Transfer $transfer)
    {
        $this->transfers[] = $transfer;

        return $this;
    }

    /**
     * Remove transfer.
     *
     * @param Transfer $transfer
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTransfer(Transfer $transfer)
    {
        return $this->transfers->removeElement($transfer);
    }

    /**
     * Get transfers.
     *
     * @return Collection
     */
    public function getTransfers()
    {
        return $this->transfers;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Task
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
