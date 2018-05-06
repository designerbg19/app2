<?php
/**
 * Created by PhpStorm.
 * User: designerbg19
 * Date: 22/04/2018
 * Time: 18:53
 */

namespace AppBundle\Entity;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_admin")
 */
class UserAdmin extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    public function __construct()
    {
        parent::__construct();
        // your own logic
        //$this->roles = array('ROLE_MACHIN');
    }
}