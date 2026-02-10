<?php

namespace Core;

class Cache
{
    private $redis = null;
    private $useFileCache = false;
    private $cacheDir;

    public function __construct()
    {
        // Try to connect to Redis
        if (class_exists('Redis')) {
            try {
                $this->redis = new \Redis();
                // Short timeout to fallback quickly if Redis is down
                if (!$this->redis->connect('127.0.0.1', 6379, 0.5)) {
                    $this->useFileCache = true;
                }
            } catch (\Exception $e) {
                $this->useFileCache = true;
            }
        } else {
            $this->useFileCache = true;
        }

        if ($this->useFileCache) {
            $this->cacheDir = __DIR__ . '/../cache/';
            if (!is_dir($this->cacheDir)) {
                mkdir($this->cacheDir, 0777, true);
            }
        }
    }

    public function get($key)
    {
        if ($this->redis && !$this->useFileCache) {
            $val = $this->redis->get($key);
            return $val ? json_decode($val, true) : null;
        } else {
            $file = $this->cacheDir . md5($key) . '.cache';
            if (file_exists($file)) {
                $data = json_decode(file_get_contents($file), true);
                // Check TTL
                if ($data['expire'] > time()) {
                    return $data['content'];
                }
                unlink($file); // Expired
            }
            return null;
        }
    }

    public function set($key, $value, $ttl = 3600)
    {
        if ($this->redis && !$this->useFileCache) {
            return $this->redis->setEx($key, $ttl, json_encode($value));
        } else {
            $file = $this->cacheDir . md5($key) . '.cache';
            $data = [
                'expire' => time() + $ttl,
                'content' => $value
            ];
            return file_put_contents($file, json_encode($data));
        }
    }

    public function delete($key)
    {
        if ($this->redis && !$this->useFileCache) {
            return $this->redis->del($key);
        } else {
            $file = $this->cacheDir . md5($key) . '.cache';
            if (file_exists($file))
                unlink($file);
        }
    }
}
?>