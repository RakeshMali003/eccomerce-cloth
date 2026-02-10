<?php
namespace Core;

class RateLimiter
{
    private $redis;
    private $keyPrefix = 'rate_limit:';

    public function __construct()
    {
        if (class_exists('Redis')) {
            try {
                $this->redis = new \Redis();
                $this->redis->connect('127.0.0.1', 6379);
            } catch (\Exception $e) {
                // Fallback or fail open
                $this->redis = null;
            }
        }
    }

    /**
     * Check if request should be limited
     * @param string $ip User IP
     * @param int $limit Max requests
     * @param int $period Time window in seconds
     * @return bool True if allowed, False if limited
     */
    public function check($ip, $limit = 100, $period = 60)
    {
        if (!$this->redis)
            return true; // Fail open if Redis down

        $key = $this->keyPrefix . $ip;
        $current = $this->redis->incr($key);

        if ($current === 1) {
            $this->redis->expire($key, $period);
        }

        if ($current > $limit) {
            return false;
        }

        return true;
    }
}
?>