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
 * Task
 *
 * @Table(name="ApiClientTask", indexes={@Index(name="action", columns={"action"}), @Index(name="transfer", columns={"transfer"})})
 * @Entity
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
     * @var \DateTime
     *
     * @Column(name="createDatetime", type="datetime", nullable=false, options={"comment"="Время создания"})
     */
    private $createDatetime;

    /**
     * @var string
     *
     * @Column(name="status", type="string", length=0, nullable=false, options={"comment"="Статусы выполнения задачи"})
     */
    private $status;

    /**
     * @var Action
     *
     * @ManyToOne(targetEntity="ApiClient\Model\Action")
     * @JoinColumns({
     *   @JoinColumn(name="action", referencedColumnName="id")
     * })
     */
    private $action;

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
     * Set createDatetime.
     *
     * @param \DateTime $createDatetime
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
     * @return \DateTime
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
     * Set transfer.
     *
     * @param Transfer|null $transfer
     *
     * @return Task
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

    /**
     * Set parameters.
     * @param array $parameters
     * @return Task
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = json_encode($parameters);

        return $this;
    }

    /**
     * Get parameters.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return json_decode($this->parameters);
    }
}
