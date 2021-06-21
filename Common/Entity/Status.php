<?php
namespace Common\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a practioner Status.
 * @ORM\Entity(repositoryClass="\Common\Repository\StatusRepository")
 * @ORM\Table(name="status")
 */
class Status
{

    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="status")
     */
    protected $status;

    /**
     * @ORM\Column(name="status_label")
     */
    protected $status_label;

    /**
     * @ORM\Column(name="color")
     */
    protected $color;

    /**
     * @ORM\Column(name="date_created")  
     */
    protected $dateCreated;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }
    public function getStatusLabel()
    {
        return $this->status_label;
    }

    public function setStatusLabel($status)
    {
        $this->status_label = $status;
    }

    public function setColor($color)
    {
        $this->color = $color;
    }

    public function getColor()
    {
        return $this->color;
    }

     public function getDateCreated()
    {
        return $this->dateCreated;
    }
    
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }
    
}