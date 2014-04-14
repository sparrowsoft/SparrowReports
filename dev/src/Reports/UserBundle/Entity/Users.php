<?php

namespace Reports\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table()
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

    /**
     * Set email
     *
     * @param string $email
     * @return Users
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Users
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * Set first_name
     *
     * @param string $first_name
     * @return Users
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;

        return $this;
    }
    
    /**
     * Set last_name
     *
     * @param string $last_name
     * @return Users
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;

        return $this;
    }
    
    /**
     * Set role
     *
     * @param string $role
     * @return Users
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }
    
    /**
     * Set image
     *
     * @param string $image
     * @return Users
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
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
