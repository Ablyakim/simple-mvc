<?php

namespace Framework\Security\Exception;

/**
 * Class AccessDenyException
 */
class AccessDenyException extends \Exception
{
    /**
     * @var \Exception
     */
    protected $originException;

    /**
     * @param \Exception $e
     *
     * @return static
     */
    public static function createFromOriginalException(\Exception $e)
    {
        $exception = new static($e->getMessage(), $e->getCode());
        $exception->setOriginException($e);

        return $exception;
    }

    /**
     * @return \Exception
     */
    public function getOriginException()
    {
        return $this->originException;
    }

    /**
     * @param \Exception $originException
     */
    public function setOriginException($originException)
    {
        $this->originException = $originException;
    }
}