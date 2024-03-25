<?php                                      
                                                     
namespace Dipso\LockeditBundle\Exception;

use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Context\ExceptionContext;
use EasyCorp\Bundle\EasyAdminBundle\Exception\BaseException;

class NonAvailableResourceException extends BaseException
{
    public function __construct(AdminContext $context)
    {
        $parameters = [
            'resource' => null === $context->getEntity() ? null : $context->getEntity()->getName()." ".$context->getEntity()->getPrimaryKeyValueAsString(),
        ];

        $exceptionContext = new ExceptionContext(
            'exception.already_used',
            sprintf('You can\'t edit "%s". Resource already used', $parameters['resource']),
            $parameters,
            403
        );

        parent::__construct($exceptionContext);
    }
}
