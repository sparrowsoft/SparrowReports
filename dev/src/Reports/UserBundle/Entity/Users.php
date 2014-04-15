<?php

namespace Reports\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="spr_users")
 * @ORM\Entity(repositoryClass="Reports\UserBundle\Entity\UsersRepository")
 */
class Users
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    public $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    public $password;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    public $first_name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    public $last_name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=255)
     */
    public $role;
    
    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255)
     */
    public $image;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function hashPassword($password) {
        $salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
	$hash = hash('sha256', $salt . $password);
		
	return $salt . $hash;
    }
    
    public function validatePassword($data, $user) {
        $salt = substr($user->password, 0, 64);
	$hash = substr($user->password, 64, 64);
        
        $check_pass = hash('sha256', $salt . $data['password']);
        
        return $check_pass == $hash;
    }
}
