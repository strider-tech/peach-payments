<?php

namespace StriderTech\PeachPayments;

/**
 * Class Configuration
 * @package StriderTech\PeachPayments
 */
class Configuration
{
    const EXCEPTION_ARGUMENT_EMPTY = 201;

    /**
     * User id.
     *
     * @var null|string
     */
    private $userId;

    /**
     * Password.
     *
     * @var null|string
     */
    private $password;

    /**
     * Entity Id.
     *
     * @var null|string
     */
    private $entityId;

    /**
     * Configuration constructor.
     * @param string $userId
     * @param string $password
     * @param string $entityId
     * @throws \Exception
     */
    public function __construct($userId = null, $password = null, $entityId = null)
    {
        $this->userId = $userId;
        $this->password = $password;
        $this->entityId = $entityId;
        // check if anything is null or empty
        if (empty($userId) || empty($password) || empty($entityId)) {
           throw new \Exception("Argument missing in configuration", self::EXCEPTION_ARGUMENT_EMPTY);
        }
    }
    
    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get Entity Id
     *
     * @return string
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set Entity Id.
     *
     * @param string $entityId
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
    }
}
