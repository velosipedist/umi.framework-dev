<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\messages\unit;

use umi\messages\toolbox\MessagesTools;
use utest\TestCase;

/**
 * Общий класс для тестов службы сообщений
 */
class MessageTestCase extends TestCase
{

    /** @var  MessagesTools */
    protected $messagesTools;

    protected function setUpFixtures()
    {
        parent::setUpFixtures();
        $this->getTestToolkit()
            ->registerToolbox(require LIBRARY_PATH . '/messages/toolbox/config.php');
        $this->messagesTools = $this->getTestToolkit()->getToolbox(MessagesTools::NAME);
        $this->messagesTools->mailerOptions['sender_address'] = [['email' => 'test@from.localhost', 'name' =>'Indeets Joe']];
        $this->messagesTools->mailerOptions['delivery_address'] = [['email' => 'test@to.localhost', 'name' => 'Mark Twain']];
        $this->messagesTools->mailerOptions['transport'] = 'mail';
    }
}
