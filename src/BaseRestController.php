<?php

namespace Creios\Creiwork\Framework;

use Aura\Session\SegmentInterface;
use Creios\Creiwork\Framework\Result\NoContentResult;
use Creios\Creiwork\Framework\Result\SerializableResult;
use JMS\Serializer\Serializer;
use Noodlehaus\Config;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseRestController
 * @package Creios\Creiwork\Framework
 */
abstract class BaseRestController extends BaseController
{

    /**
     * @var Serializer
     */
    protected $serializer;
//    protected $repository;
    /** @var string */
    protected $model;
    /** @var string */
    protected $mimeType = "application/json";

    /**
     * BaseRestController constructor.
     * @param ServerRequestInterface $serverRequest
     * @param LoggerInterface $logger
     * @param SegmentInterface $session
     * @param Config $config
     * @param Serializer $serializer
     */
    public function __construct(ServerRequestInterface $serverRequest,
                                LoggerInterface $logger,
                                SegmentInterface $session,
                                Config $config,
                                Serializer $serializer)
    {
        parent::__construct($serverRequest, $logger, $session, $config);
        $this->serializer = $serializer;
    }

    /**
     * @return SerializableResult
     */
    protected function standardCreate()
    {
        //$userModel = $this->serializer->deserialize(
        //  $this->request->getBody(),
        //  self::class,
        //  self::type);
        //$this->repository->insert($userModel);
        return (new SerializableResult([]))->withMimeType($this->mimeType);
    }

    /**
     * @return SerializableResult
     */
    protected function standardRetrieve()
    {
        //$id = $this->request->getQueryParams()['id'];
        //$model = $this->repository->get($id);
        return (new SerializableResult([]))->withMimeType($this->mimeType);
    }

    /**
     * @return SerializableResult
     */
    protected function standardUpdate()
    {
        //$userModel = $this->serializer->deserialize(
        //  $this->request->getBody(),
        //  self::class,
        //  self::type);
        //$this->repository->update($userModel);
        return (new SerializableResult([]))->withMimeType($this->mimeType);
    }

    /**
     * @return NoContentResult
     */
    protected function standardDelete()
    {
        //$id = $this->request->getQueryParams()['id'];
        //$model = $this->repository->delete($id);
        return new NoContentResult();
    }

    /**
     * @return SerializableResult
     */
    protected function standardList()
    {
        //$model = $this->repository->all();
        return (new SerializableResult([]))->withMimeType($this->mimeType);
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

}