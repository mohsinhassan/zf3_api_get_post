<?php

namespace CommonPlugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Common\Entity\Userssometa;
use Common\Entity\Usersso;

// Plugin class
class MetaPlugin extends AbstractPlugin {

// This method checks whether user is allowed
// to visit the page
    //private $orderManager;
    private $entityManager;
    private $metaManager;
    public function __construct($entityManager, $metaManager) {
        $this->entityManager = $entityManager;
        $this->metaManager = $metaManager;
    }

    /*
     * adds/update meta of order
     * @params 
     * $order_object == object of order
     * $key
     * $value
     */

    public function addUpdateMeta($metaData)
    {
        $res = array();
        if ($meta = $this->checkMetaExixsts($metaData)) {
            $usermeta = $this->entityManager->getRepository(Userssometa::class)
                ->find($meta[0]->getId());
            if(is_object($usermeta))
            {
                $this->metaManager->updateSsometa($usermeta, $metaData );
                $res[] = $metaData['meta_key'];
            }
        } else {
            $user = $this->entityManager->getRepository(Usersso::class)
                ->find($metaData['user_sso_id']);
            if(is_object($user)) {
                $res[] = $metaData['meta_key'];
                $this->metaManager->addSsometa($user, $metaData);
            }
        }
    }

    public function checkMetaExixsts($data) {
        $meta = $this->entityManager->getRepository(Userssometa::class)
            ->checkExists($data);
        if (!empty($meta)) {
            return $meta;
        }
        return false;
    }

    /*
     * adds/update bulk meta of order
     * @params 
     * $order_object == object of order
     * $key
     * $value
     */

    public function addUpdateMetaBulk($Data)
    {
        $res = array();
        foreach($Data as $metaData) {
            
            if ($meta = $this->checkMetaExixsts($metaData)) {
                $usermeta = $this->entityManager->getRepository(Userssometa::class)
                    ->find($meta[0]->getId());
                if(is_object($usermeta))
                {
                    $this->metaManager->updateSsometa($usermeta, $metaData );
                    $res[] = $metaData['meta_key'];
                }
            } else {
                $user = $this->entityManager->getRepository(Usersso::class)
                    ->find($metaData['user_sso_id']);
                if(is_object($user)) {
                    $res[] = $metaData['meta_key'];
                    $this->metaManager->addSsometa($user, $metaData);
                }
            }
       }
    }

}
