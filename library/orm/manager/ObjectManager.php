<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\manager;

use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\orm\collection\ICollection;
use umi\orm\collection\ICollectionManagerAware;
use umi\orm\collection\TCollectionManagerAware;
use umi\orm\metadata\IObjectType;
use umi\orm\object\IObject;
use umi\orm\object\IObjectFactory;
use umi\orm\persister\IObjectPersisterAware;
use umi\orm\persister\TObjectPersisterAware;

/**
 * Менеджер объектов (Identify Map).
 * @internal
 */
class ObjectManager implements IObjectManager, ILocalizable, IObjectPersisterAware, ICollectionManagerAware
{

    use TLocalizable;
    use TObjectPersisterAware;
    use TCollectionManagerAware;

    /**
     * @var IObjectFactory $objectFactory фабрика объектов
     */
    protected $objectFactory;
    /**
     * @var array $objectsById массив загруженных объектов в формате ['collectionName' => ['id' => IObject, ...], ...]
     */
    protected $objectsById = [];
    /**
     * @var IObject[] $objectsByGuid массив загруженных объектов в формате ['guid' => IObject, ...]
     */
    protected $objectsByGuid = [];

    /**
     * Конструктор
     * @param IObjectFactory $objectFactory фабрика объектов
     */
    public function __construct(IObjectFactory $objectFactory)
    {
        $this->objectFactory = $objectFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectInstanceById(ICollection $collection, $objectId)
    {
        $collectionName = $collection->getName();
        if (isset($this->objectsById[$collectionName][$objectId])) {
            return $this->objectsById[$collectionName][$objectId];
        } else {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectInstanceByGuid($guid)
    {
        if (isset($this->objectsByGuid[$guid])) {
            return $this->objectsByGuid[$guid];
        } else {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function registerNewObject(ICollection $collection, IObjectType $objectType, $guid = null)
    {
        $object = $this->objectFactory->createObject($collection, $objectType);
        $object->setIsNew(true);

        foreach ($object->getAllProperties() as $property) {
            $field = $property->getField();
            $property->setInitialValue($field->getDefaultValue($property->getLocaleId()));
        }

        if (!$guid) {
            $guidField = $collection->getGUIDField();
            $guid = $guidField->generateGUID();
        }
        $object->getProperty(IObject::FIELD_GUID)->setValue($guid);

        $this->objectsByGuid[$guid] = $object;

        $this->getObjectPersister()
            ->markAsNew($object);

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function registerLoadedObject(ICollection $collection, IObjectType $objectType, $objectId, $guid)
    {
        /**
         * @var IObject $object
         */
        if (!$object = $this->getObjectInstanceById($collection, $objectId)) {
            $object = $this->objectFactory->createObject($collection, $objectType);
            $this->objectsById[$collection->getName()][$objectId] = $object;
            $this->objectsByGuid[$guid] = $object;
        }

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function wakeUpObject(IObject $object)
    {

        $collectionName = $object->getCollectionName();

        $collection = $this->getCollectionManager()
            ->getCollection($collectionName);
        $type = $collection->getMetadata()
            ->getType($object->getTypeName());
        $this->objectFactory->wakeUpObject($object, $collection, $type);

        if (!isset($this->objectsById[$collectionName])) {
            $this->objectsById[$collectionName] = [];
        }
        $this->objectsById[$collectionName][$object->getId()] = $object;
        $this->objectsByGuid[$object->getGUID()] = $object;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function unloadObjects()
    {
        /**
         * @var IObject $object
         */
        foreach ($this->objectsById as $collectionObjects) {
            foreach ($collectionObjects as $object) {
                $object->unload();
            }
        }
        $this->objectsById = [];
        $this->objectsByGuid = [];
        $this->getObjectPersister()
            ->clearObjectsState();
    }

    /**
     * {@inheritdoc}
     */
    public function storeNewObject(IObject $object)
    {

        $collectionName = $object->getCollectionName();
        if (!isset($this->objectsById[$collectionName])) {
            $this->objectsById[$collectionName] = [];
        }
        $this->objectsById[$collectionName][$object->getId()] = $object;
        $this->objectsByGuid[$object->getGUID()] = $object;

        $object->setIsConsistent();
        $object->setIsNew(false);

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function unloadObject(IObject $object)
    {
        $collectionName = $object->getCollection()
            ->getName();
        if (isset($this->objectsById[$collectionName][$object->getId()])) {
            unset($this->objectsById[$collectionName][$object->getId()]);
        }
        if (isset($this->objectsByGuid[$object->getGUID()])) {
            unset($this->objectsByGuid[$object->getGUID()]);
        }

        $this->getObjectPersister()
            ->clearObjectState($object);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjects()
    {
        return $this->objectsByGuid;
    }
}
