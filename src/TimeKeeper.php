<?php


namespace hamburgscleanest\GuzzleAdvancedThrottle;

use DateInterval;
use DateTime;
use hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\TimerNotStartedException;


/**
 * Class TimeKeeper
 * @package hamburgscleanest\GuzzleAdvancedThrottle
 */
class TimeKeeper
{

    /** @var int */
    private $_expirationIntervalSeconds;

    /** @var DateTime */
    private $_expiresAt;

    /**
     * TimeKeeper constructor.
     * @param int $intervalInSeconds
     */
    public function __construct(int $intervalInSeconds)
    {
        $this->_expirationIntervalSeconds = $intervalInSeconds;
    }

    /**
     * @param int $intervalInSeconds
     * @return TimeKeeper
     */
    public static function create(int $intervalInSeconds) : self
    {
        return new static($intervalInSeconds);
    }

    /**
     * @return DateTime
     */
    public function getExpiration() : ? DateTime
    {
        return $this->_expiresAt;
    }

    /**
     * @param DateTime $expiresAt
     * @return TimeKeeper
     */
    public function setExpiration(DateTime $expiresAt) : self
    {
        $this->_expiresAt = $expiresAt;

        return $this;
    }

    /**
     * @return int
     * @throws \hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\TimerNotStartedException
     */
    public function getRemainingSeconds() : int
    {
        if ($this->isExpired())
        {
            $this->reset();

            return $this->_expirationIntervalSeconds;
        }

        return $this->_expiresAt->getTimestamp() - \time();
    }

    /**
     * @return bool
     * @throws \hamburgscleanest\GuzzleAdvancedThrottle\Exceptions\TimerNotStartedException
     */
    public function isExpired() : bool
    {
        if ($this->_expiresAt === null)
        {
            throw new TimerNotStartedException();
        }

        return $this->_expiresAt <= new DateTime();
    }

    /**
     *  Reset the request timer.
     */
    public function reset() : void
    {
        $this->_expiresAt = null;
    }

    /**
     * Initialize the expiration date for the request timer.
     * @throws \Exception
     */
    public function start() : void
    {
        $this->_expiresAt = (new DateTime())->add(new DateInterval('PT' . $this->_expirationIntervalSeconds . 'S'));
    }
}