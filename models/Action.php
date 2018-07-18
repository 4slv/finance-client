<?php

namespace ApiClient\Model;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Entity;

/**
 * Action
 *
 * @Table(name="ApiClientAction")
 * @Entity
 */
class Action
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
     * @return Action
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
