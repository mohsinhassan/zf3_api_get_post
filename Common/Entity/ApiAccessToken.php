<?php
namespace Common\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApiAccessToken
 *
 * @ORM\Table(name="api_access_token", indexes={@ORM\Index(name="FK_api_access_token", columns={"user_id"})})
 * @ORM\Entity
 */
class ApiAccessToken
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ip_address", type="string", length=50, nullable=true)
     */
    private $ipAddress;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=50, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(name="access_token")
     */
    private $accessToken;

    /**
     * @ORM\Column(name="token_expiry")
     */
    private $tokenExpiry;
    /**
     * @ORM\Column(name="refresh_token")
     */
    private $refreshToken;

    /**
     * @ORM\Column(name="refresh_token_expiry")
     */
    private $refreshTokenExpiry;

    /**
     * @var \Admin\\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Common\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;


    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getIpAddress()
    {
        return $this->ipAddress;
    }
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Returns the date of category creation.
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Sets the date when this category was created.
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Returns the date of category creation.
     * @return string
     */
    public function getTokenExpiry()
    {
        return $this->tokenExpiry;
    }

    /**
     * Sets the date when this category was created.
     * @param string $tokenExpiry
     */

    public function setTokenExpiry($tokenExpiry)
    {
        $this->tokenExpiry = $tokenExpiry;
    }


    /**
     * Returns the date of category creation.
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }
    /**
     * Sets the date when this category was created.
     * @param string $refreshToken
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * Returns the date of category creation.
     * @return string
     */
    public function getRefreshTokenExpiry()
    {
        return $this->refreshTokenExpiry;
    }

    /**
     * Sets the date when this category was created.
     * @param string $refreshTokenExpiry
     */
    public function setRefreshTokenExpiry($refreshTokenExpiry)
    {
        $this->refreshTokenExpiry = $refreshTokenExpiry;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }
}