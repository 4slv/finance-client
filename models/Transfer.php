<?php

namespace ApiClient\Model;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\JoinColumns;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;

/**
 * Transfer
 *
 * @Table(name="ApiClientTransfer")
 * @Entity
 */
class Transfer
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
     * @var integer
     *
     * @Column(name="code", type="integer", nullable=false, options={"comment"="Статус ответа сервера"})
     */
    private $code;

    /**
     * @var \DateTime
     *
     * @Column(name="datetime", type="datetime", nullable=false, options={"comment"="Время передачи"})
     */
    private $datetime;

    /**
     * @var Transfer
     *
     * @ManyToMany(targetEntity="ApiClient\Model\Task", inversedBy="transfers")
     * @JoinTable(name="ApiClientTaskTransfer")
     */
    private $tasks;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->datetime = new \DateTime();
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set code.
     *
     * @param int $code
     *
     * @return Transfer
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set datetime.
     *
     * @param \DateTime $datetime
     *
     * @return Transfer
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
     * Add task.
     *
     * @param \ApiClient\Model\Task $task
     *
     * @return Transfer
     */
    public function addTask(\ApiClient\Model\Task $task)
    {
        $this->tasks[] = $task;

        return $this;
    }

    /**
     * Remove task.
     *
     * @param \ApiClient\Model\Task $task
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTask(\ApiClient\Model\Task $task)
    {
        return $this->tasks->removeElement($task);
    }

    /**
     * Get tasks.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTasks()
    {
        return $this->tasks;
    }

}
