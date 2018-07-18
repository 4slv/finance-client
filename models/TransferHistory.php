<?php

namespace ApiClient\Model;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumns;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * TransferHistory
 *
 * @Table(name="ApiClientTransferHistory", indexes={@Index(name="task", columns={"task"}), @Index(name="transfer", columns={"transfer"})})
 * @Entity
 */
class TransferHistory
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
     * @var \DateTime
     *
     * @Column(name="datetime", type="datetime", nullable=false, options={"comment"="Время выполнения передачи"})
     */
    private $datetime;

    /**
     * @var string
     *
     * @Column(name="status", type="enum", length=0, nullable=false, options={"comment"="Статусы выполнения задачи"})
     */
    private $status;

    /**
     * @var string
     *
     * @Column(name="description", type="string", length=255, nullable=false, options={"comment"="Текст ошибки, если задача выполнена с ошибкой"})
     */
    private $description;

    /**
     * @var Task
     *
     * @ManyToOne(targetEntity="ApiClient\Model\Task")
     * @JoinColumns({
     *   @JoinColumn(name="task", referencedColumnName="id")
     * })
     */
    private $task;

    /**
     * @var Transfer
     *
     * @ManyToOne(targetEntity="ApiClient\Model\Transfer")
     * @JoinColumns({
     *   @JoinColumn(name="transfer", referencedColumnName="id")
     * })
     */
    private $transfer;

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
     * Set datetime.
     *
     * @param \DateTime $datetime
     *
     * @return TransferHistory
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime.
     *
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set status.
     *
     * @param $status
     *
     * @return TransferHistory
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
     * Set description.
     *
     * @param string $description
     *
     * @return TransferHistory
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

    /**
     * Set task.
     *
     * @param Task|null $task
     *
     * @return TransferHistory
     */
    public function setTask(Task $task = null)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task.
     *
     * @return Task|null
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Set transfer.
     *
     * @param Transfer|null $transfer
     *
     * @return TransferHistory
     */
    public function setTransfer(Transfer $transfer = null)
    {
        $this->transfer = $transfer;

        return $this;
    }

    /**
     * Get transfer.
     *
     * @return Transfer|null
     */
    public function getTransfer()
    {
        return $this->transfer;
    }
}
