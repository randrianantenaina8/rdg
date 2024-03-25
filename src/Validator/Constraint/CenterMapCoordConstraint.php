<?php                                      
                                                     
namespace App\Validator\Constraint;

use App\Validator\CenterMapCoordValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CenterMapCoordConstraint extends Constraint
{
    public const NOT_NUMERIC = 'centermapcoord.coordinates.notnumeric';
    public const NOT_BOTH = 'centermapcoord.centers.notboth';
    public const NOT_BOTH_EMPTY = 'centermapcoord.centers.notboth.empty';

    /**
     * @return string|string[]
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * @return string
     */
    public function validatedBy()
    {
        return CenterMapCoordValidator::class;
    }
}
