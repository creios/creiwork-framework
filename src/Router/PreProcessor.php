<?php

namespace Creios\Creiwork\Framework\Router;

use Creios\Creiwork\Framework\BaseRestController;
use Creios\Creiwork\Framework\Exception\DeserializeException;
use JMS\Serializer\Serializer;
use Psr\Http\Message\ServerRequestInterface;
use TimTegeler\Routerunner\Controller\ControllerInterface;
use TimTegeler\Routerunner\Processor\PreProcessorInterface;

class PreProcessor implements PreProcessorInterface
{

    /** @var Serializer */
    private $serializer;

    /**
     * PreProcessor constructor.
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ControllerInterface $controller
     * @return ServerRequestInterface
     * @throws DeserializeException
     */
    public function process(ServerRequestInterface $request, ControllerInterface $controller)
    {
        if ($controller instanceof BaseRestController) {
            if ($controller->getModel() === null) {
                throw new DeserializeException('You need to set the model property');
            }
            // Todo: Perhaps disable parsing by endpoints without body (e.g. GET, DELETE)
            $data = $this->serializer->deserialize(
                $request->getBody(),
                $controller->getModel(),
                'json');
            $request = $request->withParsedBody($data);
        }
        return $request;
    }

}