<?php
namespace Common\Event;
use Common\Event\LogEvents;
use Common\Entity\Setting;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Log\Logger;


use Zend\EventManager\SharedEventManager;
class EventTargets implements EventManagerAwareInterface
{
    protected $events;
    protected $activityLog;
    protected $data;
    protected $entityManager;

    public function __construct($entityManager, $data)
    {
        $this->data = $data;
        $this->entityManager = $entityManager;

        $logger = new Logger();
        $logListener = new LogEvents($logger);
        $logListener->attach($this->getEventManager());

    }

    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers([
            __CLASS__,
            get_class($this),
        ]);
        $this->events = $events;
    }

    public function getEventManager()
    {
        if (! $this->events) {
            $sharedEvents = new SharedEventManager();
            $this->setEventManager(new EventManager($sharedEvents));
        }
        return $this->events;
    }

    public function updateUserSSOInfo()
    {
        //event triggers
        $settings = [];
        $setting = $this->entityManager->getRepository(Setting::class)->findOneBy(['meta' => 'qoctor_key']);
        $settings['qoctor_key'] = $setting->getValue();

        $setting = $this->entityManager->getRepository(Setting::class)->findOneBy(['meta' => 'qoctor_secret']);
        $settings['qoctor_secret'] = $setting->getValue();

        $setting = $this->entityManager->getRepository(Setting::class)->findOneBy(['meta' => 'qoctor_update_user_api']);
        $settings['qoctor_update_user_api'] = $setting->getValue();

        $setting = $this->entityManager->getRepository(Setting::class)->findOneBy(['meta' => 'host']);
        $settings['host'] = $setting->getValue();

        $params = ['data' => $this->data, 'settings' => $settings];

        $this->getEventManager()->trigger(__FUNCTION__, $this, $params);
    }

    public function updateUserMetaSSOInfo()
    {
        //event triggers
        $settings = [];
        $setting = $this->entityManager->getRepository(Setting::class)->findOneBy(['meta' => 'qoctor_key']);
        $settings['qoctor_key'] = $setting->getValue();

        $setting = $this->entityManager->getRepository(Setting::class)->findOneBy(['meta' => 'qoctor_secret']);
        $settings['qoctor_secret'] = $setting->getValue();

        $setting = $this->entityManager->getRepository(Setting::class)->findOneBy(['meta' => 'qoctor_update_user_meta_api']);
        $settings['qoctor_update_user_meta_api'] = $setting->getValue();
        //$settings['qoctor_update_user_meta_api'] = 'https://dev.qoctor.com.au/wp-json/test/test/';

        $setting = $this->entityManager->getRepository(Setting::class)->findOneBy(['meta' => 'host']);
        $settings['host'] = $setting->getValue();

        $params = ['data' => $this->data, 'settings' => $settings];

        $this->getEventManager()->trigger(__FUNCTION__, $this, $params);
    }

}
