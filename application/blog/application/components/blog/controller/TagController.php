<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace application\components\blog\controller;

use application\components\blog\model\TagModel;
use umi\hmvc\controller\BaseController;
use umi\hmvc\exception\http\HttpNotFound;
use umi\orm\exception\NonexistentEntityException;

/**
 * Контроллер отображения постов по тегам.
 */
class TagController extends BaseController
{
    /**
     * @var TagModel $tagModel
     */
    protected $tagModel;

    /**
     * Конструктор.
     * @param TagModel $model
     */
    public function __construct(TagModel $model)
    {
        $this->tagModel = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        $guid = $this->getRouteVar('id');

        try {
            $tag = $this->tagModel->getTag($guid);
        } catch (NonexistentEntityException $e) {
            throw new HttpNotFound('Tag not found.', 0, $e);
        }

        return $this->createViewResponse(
            'tag',
            [
                'tag'   => $tag,
                'posts' => $this->tagModel->getTagPosts($tag)
            ]
        );
    }
}
