<?php

namespace UberScheduleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\VirtualProperty;
/**
 * User
 * @ORM\Table(name="request")
 * @ORM\Entity(repositoryClass="UberScheduleBundle\Entity\RequestRepository")
 */
class Request
{
	 /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string
     * @ORM\Column(name="tz", type="string", length=25, nullable=true)
     *
     */
    private $tz;

     /**
     * @ORM\ManyToOne(targetEntity="UberScheduleBundle\Entity\User")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     **/
    private $user;

     /**
     * @var string
     * @ORM\Column(name="mode", type="string", length=25)
     *
     */
    private $mode;

    /**
     * @var string
     * @ORM\Column(name="status", type="string", length=25)
     *
     */
    private $status;

    /**
     * @var string
     * @ORM\Column(name="requestTimeUTC", type="datetime", length=25, nullable=true)
     *
     */
 	private $requestTimeUTC;


    /**
     * @var string
     * @ORM\Column(name="productId", type="string", length=50)
     *
     */
    private $product_id;

    /**
     * @var string
     * @ORM\Column(name="startLat", type="string", length=50)
     *
     */
    private $start_lat;
    
    /**
     * @var string
     * @ORM\Column(name="startLon", type="string", length=50)
     *
     */
    private $start_lon;

    /**
     * @var string
     * @ORM\Column(name="endLat", type="string", length=50)
     *
     */
    private $end_lat;

    /**
     * @var string
     * @ORM\Column(name="endLon", type="string", length=50)
     *
     */
    private $end_lon;

    /**
     * @var string
     * @ORM\Column(name="requestId", type="string", length=50, nullable=true)
     *
     */
    private $request_id;

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
     * Set tz
     *
     * @param string $tz
     * @return Request
     */
    public function setTz($tz)
    {
        $this->tz = $tz;

        return $this;
    }

    /**
     * Get tz
     *
     * @return string 
     */
    public function getTz()
    {
        return $this->tz;
    }

    /**
     * Set mode
     *
     * @param string $mode
     * @return Request
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * Get mode
     *
     * @return string 
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Request
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set requestTimeUTC
     *
     * @param \DateTime $requestTimeUTC
     * @return Request
     */
    public function setRequestTimeUTC($requestTimeUTC)
    {
        $this->requestTimeUTC = $requestTimeUTC;

        return $this;
    }

    /**
     * Get requestTime
     *
     * @return \DateTime 
     */
    public function getRequestTimeUTC()
    {
        return $this->requestTimeUTC;
    }



    /**
     * @VirtualProperty 
     * @return \DateTime 
     */
    public function getRequestTimeLocal()
    {    
        if(isset($this->tz)){
            $date = $this->requestTimeUTC;
            $date->setTimezone(new \DateTimeZone($this->tz));
            var_dump($date);
            return $date;
        } else {
            return null;
        }
        
    }

    /**
     * Set product_id
     *
     * @param string $productId
     * @return Request
     */
    public function setProductId($productId)
    {
        $this->product_id = $productId;

        return $this;
    }

    /**
     * Get product_id
     *
     * @return string 
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * Set start_lat
     *
     * @param string $startLat
     * @return Request
     */
    public function setStartLat($startLat)
    {
        $this->start_lat = $startLat;

        return $this;
    }

    /**
     * Get start_lat
     *
     * @return string 
     */
    public function getStartLat()
    {
        return $this->start_lat;
    }

    /**
     * Set start_lon
     *
     * @param string $startLon
     * @return Request
     */
    public function setStartLon($startLon)
    {
        $this->start_lon = $startLon;

        return $this;
    }

    /**
     * Get start_lon
     *
     * @return string 
     */
    public function getStartLon()
    {
        return $this->start_lon;
    }

    /**
     * Set end_lat
     *
     * @param string $endLat
     * @return Request
     */
    public function setEndLat($endLat)
    {
        $this->end_lat = $endLat;

        return $this;
    }

    /**
     * Get end_lat
     *
     * @return string 
     */
    public function getEndLat()
    {
        return $this->end_lat;
    }

    /**
     * Set end_lon
     *
     * @param string $endLon
     * @return Request
     */
    public function setEndLon($endLon)
    {
        $this->end_lon = $endLon;

        return $this;
    }

    /**
     * Get end_lon
     *
     * @return string 
     */
    public function getEndLon()
    {
        return $this->end_lon;
    }

    /**
     * Set user
     *
     * @param \UberScheduleBundle\Entity\User $user
     * @return Request
     */
    public function setUser(\UberScheduleBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UberScheduleBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set request_id
     *
     * @param string $requestId
     * @return Request
     */
    public function setRequestId($requestId)
    {
        $this->request_id = $requestId;

        return $this;
    }

    /**
     * Get request_id
     *
     * @return string 
     */
    public function getRequestId()
    {
        return $this->request_id;
    }

}
