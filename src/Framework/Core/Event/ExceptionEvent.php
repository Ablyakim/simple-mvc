<?php

namespace Framework\Core\Event;

use Framework\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ExceptionEvent
 */
class ExceptionEvent extends Event
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @var Response
     */
    protected $response;

    /**
     * ExceptionEvent constructor.
     * @param Request $request
     * @param \Exception $exception
     */
    public function __construct(Request $request, \Exception $exception)
    {
        $this->request = $request;
        $this->exception = $exception;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }
}