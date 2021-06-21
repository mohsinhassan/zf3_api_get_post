<?php

namespace Common\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a registered invoice.
 * @ORM\Entity(repositoryClass="\Common\Repository\MetaLabelsRepository")
 * @ORM\Table(name="meta_labels")
 */
class MetaLabels {

    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;
    /**
     * @ORM\Column(name="meta_label")
     */
    protected $metaLabel;
    /**
     * @ORM\Column(name="meta_key")
     */
    protected $metaKey;

    /**
     * @ORM\Column(name="category")
     */

    protected $category;

    /**
     * @ORM\Column(name="status")
     */

    protected $status;

    /**
     * @ORM\Column(name="date_created")
     */
    protected $dateCreated;

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function getMetaLabel() {
        return $this->metaLabel;
    }

    public function getMetaKey() {
        return $this->metaKey;
    }

    public function getCategory() {
        return $this->category;
    }


    public function setMetaLabel($meta) {
        $this->metaLabel = $meta;
    }

    public function setMetaKey($value) {
        $this->metaKey = $value;
    }

    public function setCategory($value) {
        $this->category = $value;
    }

    public function setStatus($value) {
        $this->status = $value;
    }

    public function getStatus() {
        return $this->status;
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
