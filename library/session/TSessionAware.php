<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\session;

use umi\session\exception\RequiredDependencyException;

/**
 * Трейт для внедрения поддержки сессии.
 */
trait TSessionAware
{
    /**
     * @var ISession $traitSessionService
     */
    private $traitSessionService;

    /**
     * @see ISessionAware::setSessionService()
     */
    public function setSessionService(ISession $sessionService)
    {
        $this->traitSessionService = $sessionService;
    }

    /**
     * Возвращает имя контейнера сессии.
     * @return string
     */
    protected function getSessionNamespacePath()
    {
        return get_class($this);
    }

    /**
     * Проверяет, существует ли переменная в сессии.
     * @param string $name имя переменной
     * @return boolean
     */
    protected function hasSessionVar($name)
    {
        return $this->getSession()->has($this->getSessionNamespace($name));
    }

    /**
     * Возвращает переменную из сессии.
     * @param string $name имя переменной
     * @param mixed $default значение переменной по умолчанию
     * @return mixed
     */
    protected function getSessionVar($name, $default = null)
    {
        return $this->getSession()->get($this->getSessionNamespace($name), $default);
    }

    /**
     * Устанавливает значение переменной в сессии.
     * @param string $name имя переменной
     * @param mixed $value значение переменной
     * @return $this
     */
    protected function setSessionVar($name, $value)
    {
        $this->getSession()->set($this->getSessionNamespace($name), $value);

        return $this;
    }

    /**
     * Возвращает все переменные из сессии.
     * @return array
     */
    protected function getSessionVars()
    {
        return $this->getSession()->get($this->getSessionNamespacePath());
    }

    /**
     * Удаляет все переменные из сессии.
     * @return $this
     */
    protected function clearSessionVars()
    {
        foreach ($this->getSessionVars() as $name => $value) {
            $this->removeSessionVar($name);
        }

        return $this;
    }


    /**
     * Заменяет все значения в сессии.
     * @param array $attributes переменные
     * @return $this
     */
    protected function replaceSessionVars(array $attributes)
    {

        $this->clearSessionVars();

        foreach ($attributes as $name => $value) {
            $this->setSessionVar($name, $value);
        }

        return $this;
    }

    /**
     * Удвляет переменную из сессии.
     * @param string $name имя переменной
     * @return mixed значение удаленной переменной или null, если переменной не было
     */
    protected function removeSessionVar($name)
    {
        return $this->getSession()->remove($this->getSessionNamespace($name));
    }

    /**
     * Возвращает неймспейс сессии.
     * @param string $name
     * @return string
     */
    private function getSessionNamespace($name)
    {
        return $this->getSessionNamespacePath() . '/' . $name;
    }

    /**
     * Возвращает сервис сессии.
     * @throws RequiredDependencyException если сервис не был внедрен.
     * @return ISession
     */
    private function getSession()
    {
        if (!$this->traitSessionService) {
            throw new RequiredDependencyException(sprintf(
                'Session service is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->traitSessionService;
    }
}