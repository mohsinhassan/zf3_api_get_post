<?php

namespace Common\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a registered invoice.
 * @ORM\Entity(repositoryClass="\Common\Repository\SettingRepository")
 * @ORM\Table(name="setting")
 */
class Setting {

    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;
    /**
     * @ORM\Column(name="meta")
     */
    protected $meta;
    /**
     * @ORM\Column(name="value")
     */
    protected $value;

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function getMeta() {
        return $this->meta;
    }

    public function getValue() {
        return $this->value;
    }


    public function setMeta($meta) {
        $this->meta = $meta;
    }

    public function setValue($value) {
        $this->value = $value;
    }
}
