<?php
use umi\orm\metadata\field\IField;
use umi\orm\object\IHierarchicObject;
use umi\orm\object\IObject;

return [
    'dataSource' => [
        'sourceName' => 'umi_mock_menu'
    ],
    'fields'     => [
        IObject::FIELD_IDENTIFY                  => [
            'type'       => IField::TYPE_IDENTIFY,
            'columnName' => 'id',
            'accessor'   => 'getId'
        ],
        IObject::FIELD_GUID                      => [
            'type'       => IField::TYPE_GUID,
            'columnName' => 'guid',
            'accessor'   => 'getGuid',
            'readOnly'    => true
        ],
        IObject::FIELD_TYPE                      => [
            'type'       => IField::TYPE_STRING,
            'columnName' => 'type',
            'accessor'   => 'getType',
            'readOnly'   => true
        ],
        IObject::FIELD_VERSION                   => [
            'type'         => IField::TYPE_VERSION,
            'columnName'   => 'version',
            'accessor'     => 'getVersion',
            'mutator'      => 'setVersion',
            'defaultValue' => 1
        ],
        IHierarchicObject::FIELD_PARENT          => [
            'type'       => IField::TYPE_BELONGS_TO,
            'columnName' => 'pid',
            'accessor'   => 'getParent',
            'readOnly'   => true
        ],
        IHierarchicObject::FIELD_MPATH           => [
            'type'       => IField::TYPE_MPATH,
            'columnName' => 'mpath',
            'accessor'   => 'getMaterializedPath',
            'readOnly'   => true
        ],
        IHierarchicObject::FIELD_SLUG            => [
            'type'       => IField::TYPE_SLUG,
            'columnName' => 'slug',
            'accessor'   => 'getSlug',
            'readOnly'   => true
        ],
        IHierarchicObject::FIELD_URI             => [
            'type'       => IField::TYPE_URI,
            'columnName' => 'uri',
            'accessor'   => 'getURI',
            'readOnly'   => true
        ],
        IHierarchicObject::FIELD_ORDER           => [
            'type'       => IField::TYPE_ORDER,
            'columnName' => 'order',
            'accessor'   => 'getOrder',
            'readOnly'   => true
        ],
        IHierarchicObject::FIELD_HIERARCHY_LEVEL => [
            'type'       => IField::TYPE_LEVEL,
            'columnName' => 'level',
            'accessor'   => 'getLevel',
            'readOnly'   => true
        ],
        'title'                                  => [
            'type'          => IField::TYPE_STRING,
            'columnName'    => 'title',
            'localizations' => [
                'ru-RU' => ['columnName' => 'title'],
                'en-US' => ['columnName' => 'title_en']
            ]
        ],
        'link'                                   => ['type' => IField::TYPE_STRING, 'columnName' => 'link'],

    ],
    'types'      => [
        'base' => [
            'fields' => [
                IObject::FIELD_IDENTIFY => [],
                IObject::FIELD_GUID => [],
                IObject::FIELD_TYPE => [],
                IObject::FIELD_VERSION => [],
                IHierarchicObject::FIELD_PARENT => [],
                IHierarchicObject::FIELD_MPATH => [],
                IHierarchicObject::FIELD_SLUG => [],
                IHierarchicObject::FIELD_URI => [],
                IHierarchicObject::FIELD_ORDER => [],
                IHierarchicObject::FIELD_HIERARCHY_LEVEL => [],
                'title' => [],
                'link' => []
            ]
        ]
    ]
];
