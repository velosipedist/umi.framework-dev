<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\objectset;

use umi\orm\collection\ICollectionFactory;
use umi\orm\object\IObject;
use umi\orm\objectset\IObjectSet;
use utest\orm\ORMDbTestCase;

/**
 * Тест класса ObjectSet

 */
class ObjectSetTest extends ORMDbTestCase
{

    /**
     * @var IObjectSet $objectsSet
     */
    protected $objectsSet;
    /**
     * @var int $counterId
     */
    protected $counterId = 1;

    /**
     * {@inheritdoc}
     */
    protected function getCollectionConfig()
    {
        return [
            self::METADATA_DIR . '/mock/collections',
            [
                self::USERS_USER             => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ]
            ],
            false
        ];
    }

    protected function setUpFixtures()
    {

        $this->objectsSet = $this->getMock('umi\orm\objectset\ObjectSet', ['getQueryResultRow']);
        $this->objectsSet->expects($this->any())
            ->method('getQueryResultRow')
            ->will($this->returnCallback([$this, 'mockGetQueryResultRow']));

        $selector = $this->getMock('umi\orm\selector\Selector', [], [], '', false);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->objectsSet->setSelector($selector);

        $this->resolveOptionalDependencies($this->objectsSet);
        $this->counterId = 1;
    }

    /**
     * Заглушка для ObjectSet::getQueryResultRow
     * @return bool
     */
    public function mockGetQueryResultRow()
    {
        if ($this->counterId > 5) {
            return false;
        }

        return [
            'users_user:id'       => $this->counterId++,
            'users_user:type'     => "users_user.base",
            'users_user:guid'     => "9ee6745f-f40d-46d8-8043-d959594628c" . $this->counterId,
            'users_user:isActive' => "1",
            'users_user:login'    => "test_login"
        ];
    }

    public function testInstance()
    {
        $this->assertInstanceOf('umi\orm\objectset\IObjectSet', $this->objectsSet, 'Неверно создан мок-объект');
    }

    public function testFetchOne()
    {
        $this->assertEquals(
            1,
            $this->objectsSet->fetch()
                ->getId(),
            'Неверный id у первого объекта'
        );
        $this->assertEquals(
            2,
            $this->objectsSet->fetch()
                ->getId(),
            'Неверный id у второго объекта'
        );

        $this->assertEquals(
            3,
            $this->objectsSet->fetch()
                ->getId(),
            'Неверный id у третьего объекта'
        );
        $this->assertEquals(
            4,
            $this->objectsSet->fetch()
                ->getId(),
            'Неверный id у четвертого объекта'
        );
        $this->assertEquals(
            5,
            $this->objectsSet->fetch()
                ->getId(),
            'Неверный id у пятого объекта'
        );
        $this->assertNull($this->objectsSet->fetch(), 'Ожидается null при достижении конца ObjectSet');

    }

    public function testFetchAll()
    {
        $result = $this->objectsSet->fetchAll();
        $this->assertCount(5, $result, 'Ожидается что в ObjectSet всего 5 объектов');
        $this->assertTrue(is_array($result), 'Ожидается, что IObjectSet::fetchAll вернет массив');
        $this->assertNull($this->objectsSet->fetch(), 'Ожидается null при достижении конца ObjectSet');
    }

    public function testFetchAllAfterFetch()
    {
        $this->assertEquals(
            1,
            $this->objectsSet->fetch()
                ->getId(),
            'Неверный id у первого объекта'
        );
        $this->assertEquals(
            2,
            $this->objectsSet->fetch()
                ->getId(),
            'Неверный id у второго объекта'
        );

        $result = $this->objectsSet->fetchAll();
        $this->assertCount(5, $result, 'Ожидается что в ObjectSet всего 5 объектов');

        /**
         * @var IObject $object
         */
        foreach ($result as $key => $object) {
            $this->assertEquals(
                $key + 1,
                $object->getId(),
                'Неверная загрузка всех объектов разом после частичной загрузки'
            );
        }
    }

    public function testIterator()
    {

        $i = 0;
        /**
         * @var IObject $value
         */
        foreach ($this->objectsSet as $key => $value) {
            $this->assertEquals($i, $key, 'Неверное значение ключа при итерировании ObjectSet');
            $this->assertEquals($i + 1, $value->getId(), 'Неверное значение при итерировании ObjectSet');
            $i++;
        }

        $i = 0;
        foreach ($this->objectsSet as $key => $value) {
            $this->assertEquals($i, $key, 'Неверное значение ключа при повторном итерировании ObjectSet');
            $this->assertEquals($i + 1, $value->getId(), 'Неверное значение при повторном итерировании ObjectSet');
            $i++;
        }
        $this->assertEquals(5, $i, 'Неверно работает итератор (ожидается, что инкремент возрос до 5)');
    }

    public function testFetchAfterCount()
    {
        $this->assertEquals(5, $this->objectsSet->count(), 'Ожидается что в ObjectSet всего 5 объектов');
        $this->assertInstanceOf(
            'umi\orm\object\IObject',
            $this->objectsSet->fetch()
        );
    }

    public function testArrayAccess()
    {
        $this->assertTrue(isset($this->objectsSet[1]));

        $this->assertFalse(isset($this->objectsSet[100]));

        $this->assertInstanceOf('umi\orm\object\IObject', $this->objectsSet[1]);

        @$this->objectsSet[100];
        $this->assertTrue(is_array(error_get_last()) && error_get_last()['message'] === 'Undefined offset: 100');

        $this->objectsSet[1] = 0;
        $this->assertInstanceOf('umi\orm\object\IObject', $this->objectsSet[1]);

        unset($this->objectsSet[1]);
        $this->assertInstanceOf('umi\orm\object\IObject', $this->objectsSet[1]);
    }
}
