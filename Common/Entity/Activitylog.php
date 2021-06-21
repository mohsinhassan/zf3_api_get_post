<?php
namespace Common\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a registered Activitylog.
 * @ORM\Entity(repositoryClass="\Common\Repository\ActivitylogRepository")
 * @ORM\Table(name="activitylog")
 */

class Activitylog
{

    protected $usersso;

    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="module")
     */
    protected $module;

    /**
     * @ORM\Column(name="action")
     */
    protected $action;


    /**
     * @ORM\Column(name="content_id")
     */
    protected $contentId;

    /**
     * @ORM\Column(name="logged_for")
     */
    protected $loggedFor;

    /**
     * @ORM\Column(name="logged_by")
     */
    protected $loggedBy;

    /**
     * @ORM\Column(name="state")
     */
    protected $state;


    /**
     * @ORM\Column(name="message")
     */
    protected $message;

    /**
     * @ORM\Column(name="data")
     */

    protected $data;

    /**
     * @ORM\Column(name="date_created")
     */
    protected $dateCreated;


    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets category ID.
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getState()
    {
        return $this->state;
    }

    /**
     * Sets category ID.
     * @param int $State
     */
    public function setState($State)
    {
        $this->state = $State;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($Date)
    {
        $this->data = $Date;
    }


    public function getLoggedBy()
    {
        return $this->loggedBy;
    }

    public function setLoggedBy($loggedBy)
    {
        $this->loggedBy = $loggedBy;
    }

    public function getLoggedFor()
    {
        return $this->loggedFor;
    }

    public function setLoggedFor($loggedFor)
    {
        $this->loggedFor = $loggedFor;
    }

    /**
     * Returns module.
     * @return string
     */

    public function getModule()
    {
        return $this->module;
    }

    /**
     * Sets module.
     * @param string $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * Returns action.
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Sets action.
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    public function getContentId()
    {
        return $this->contentId;
    }

    public function setContentId($Content_id)
    {
        $this->contentId = $Content_id;
    }

    /**
     * Returns the date of category creation.
     * @return string
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Sets the date when this category was created.
     * @param string $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * Sets activitylog Message.
     * @param int $message
     */

    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Returns the date of message.
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /*
     * Returns associated invoice.
     * @return \Common\Entity\Usersso
     */



}