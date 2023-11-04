<?php

namespace App\Enums;

use Spatie\Enum\Enum;

/** //=================================================

 * //==================================================
 * ENGINES
 * @method static self parmanences_create()
 * @method static self parmanences_read()
 * @method static self parmanences_update()
 * @method static self parmanences_delete()
 * //==================================================
 * REPARATIONS
 * @method static self utilisateurs_create()
 * @method static self utilisateurs_read()
 * @method static self utilisateurs_update()
 * @method static self utilisateurs_delete()
 * // =================================================
 * DEPARTEMENTS
 * @method static self Departements_create()
 * @method static self Departements_read()
 * @method static self Departements_update()
 * @method static self Departements_delete()
 * // =================================================
 * MARQUES  
 * @method static self services_create()
 * @method static self services_read()
 * @method static self services_update()
 * @method static self services_delete()
 * // =================================================
 * PERMISSIONS
 * @method static self Permissions_read()
 * // =================================================
 * USERS
 * @method static self Roles_create()
 * @method static self Roles_read()
 * @method static self Roles_update()
 * @method static self Roles_delete()
 * // =================================================
 */

class PermissionsClass extends Enum
{

    protected static function values()
    {
        return function(string $name): string|int {

            $traductions = array(
                "create" => "ajouter",
                "read" => "voir",
                "update" => "modifier",
                "delete" => "supprimer",
                "services" => "Services",
                "parmanences" => "Permanence",
                "Departements" => "Départements",
                "utilisateurs" => "Utilisateurs",
            );
            return strtr(str_replace("_", ": ", str($name)), $traductions);;
        };
    }
}