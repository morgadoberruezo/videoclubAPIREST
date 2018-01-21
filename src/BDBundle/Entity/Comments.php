<?php

namespace BDBundle\Entity;

/**
 * Comments
 */
class Comments
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $body;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \BDBundle\Entity\Users
     */
    private $user;

    /**
     * @var \BDBundle\Entity\Videos
     */
    private $video;


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
     * Set body
     *
     * @param string $body
     *
     * @return Comments
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Comments
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set user
     *
     * @param \BDBundle\Entity\Users $user
     *
     * @return Comments
     */
    public function setUser(\BDBundle\Entity\Users $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \BDBundle\Entity\Users
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set video
     *
     * @param \BDBundle\Entity\Videos $video
     *
     * @return Comments
     */
    public function setVideo(\BDBundle\Entity\Videos $video = null)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * Get video
     *
     * @return \BDBundle\Entity\Videos
     */
    public function getVideo()
    {
        return $this->video;
    }
}

