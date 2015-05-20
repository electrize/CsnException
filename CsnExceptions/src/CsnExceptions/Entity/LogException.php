<?php


namespace CsnExceptions\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;


/**
 * LogException
 *
 * @ORM\Table(name="logs_ex")
 * @ORM\Entity
 */
class LogException
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Annotation\Exclude()
     */
    protected $id;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    protected $created;

    /**
     * 
     * @var string
     *
     * @ORM\Column(name="file", type="text", nullable=true)
     */
    protected $file;

    /**
     * 
     * @var integer
     *
     * @ORM\Column(name="row", type="integer", nullable=true)
     */
    protected $row;

    /**
     * 
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    protected $message;

    /**
     * 
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", nullable=true)
     */
    protected $code;

    /**
     * 
     * @var string
     *
     * @ORM\Column(name="class", type="string", nullable=true)
     */
    protected $class;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=17, nullable=true)
     */
    protected $ip;

    public function __construct()
    {
        $this->created = new \DateTime();
    }
	
    /**
     * Get Id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set Id
     *
     * @param  string $id
     * @return LogException
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get Created
     *
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set Created
     *
     * @param  string $created
     * @return LogException
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * Get File
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set File
     *
     * @param  string $file
     * @return LogException
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Get Row
     *
     * @return int
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Set Row
     *
     * @param  int  $row
     * @return LogException
     */
    public function setRow($row)
    {
        $this->row = $row;

        return $this;
    }

    /**
     * Get Message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set Message
     *
     * @param  string $message
     * @return LogException
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get Code
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set Code
     *
     * @param  int  $code
     * @return LogException
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get Class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set Class
     *
     * @param  string $class
     * @return LogException
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * Get Ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set Ip
     *
     * @param  string  $ip
     * @return LogException
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }
	
}
