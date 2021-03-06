<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func\object\property;

use umi\orm\collection\ICollectionFactory;
use umi\orm\collection\IHierarchicCollection;
use umi\orm\object\IHierarchicObject;
use utest\orm\ORMDbTestCase;

/**
 * Тесты свойства DateTime
 */
class DateTimePropertiesTest extends ORMDbTestCase
{
    /**
     * @var IHierarchicObject $blog
     */
    private $blog;
    /**
     * @var string $blogGuid
     */
    private $blogGuid;
    /**
     * @var string $time
     */
    private $time = '2014-02-20 17:25:00';
    /**
     * @var IHierarchicCollection $blogCollection
     */
    private $blogCollection;

    /**
     * {@inheritdoc}
     */
    protected function getCollectionConfig()
    {
        return [
            self::METADATA_DIR . '/mock/collections',
            [
                self::SYSTEM_HIERARCHY       => [
                    'type' => ICollectionFactory::TYPE_COMMON_HIERARCHY
                ],
                self::BLOGS_BLOG             => [
                    'type'      => ICollectionFactory::TYPE_LINKED_HIERARCHIC,
                    'class'     => 'utest\orm\mock\collections\BlogsCollection',
                    'hierarchy' => self::SYSTEM_HIERARCHY
                ],
                self::USERS_USER             => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ],
                self::USERS_GROUP            => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ]
            ],
            true
        ];
    }

    protected function setUpFixtures()
    {
        $this->blogCollection = $this->getCollectionManager()->getCollection(self::BLOGS_BLOG);
        $this->blog = $this->blogCollection->add('blog');
        $this->blogGuid = $this->blog->getGUID();
    }

    public function testWrongSetValue()
    {
        $e = null;
        try {
            $this->blog->setValue('publishTime', 'wrongValue');
        } catch (\Exception $e) {}

        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение при попытке выставить в качестве значения не DateTime'
        );
    }

    public function testSetValue()
    {

        $this->assertNull($this->blog->getValue('publishTime'));

        $dateTimeValue = new \DateTime($this->time, new \DateTimeZone('Europe/Moscow'));
        $this->blog->setValue('publishTime', $dateTimeValue);
        $this->getObjectPersister()->commit();
        $this->getObjectManager()->unloadObjects();

        $blog = $this->blogCollection->get($this->blogGuid);
        /**
         * @var \DateTime $dateTime
         */
        $dateTime = $blog->getValue('publishTime');
        $this->assertInstanceOf('DateTime', $dateTime);
        $this->assertEquals($this->time, $dateTime->format('Y-m-d H:i:s'));

        $blog->setValue('publishTime', null);
        $this->getObjectPersister()->commit();
        $this->getObjectManager()->unloadObjects();

        $blog = $this->blogCollection->get($this->blogGuid);
        $this->assertNull($blog->getValue('publishTime'));
    }

    public function testModifyingObject()
    {
        $dateTimeValue = new \DateTime($this->time, new \DateTimeZone('Europe/Moscow'));
        $this->blog->setValue('publishTime', $dateTimeValue);
        $this->getObjectPersister()->commit();
        $this->getObjectManager()->unloadObjects();

        $blog = $this->blogCollection->get($this->blogGuid);
        $blog->setValue('publishTime', new \DateTime($this->time, new \DateTimeZone('Europe/Moscow')));

        $this->assertFalse($blog->getIsModified());

        $blog->setValue('publishTime', new \DateTime($this->time, new \DateTimeZone('Pacific/Nauru')));
        $this->assertTrue($blog->getIsModified());

    }

}
 