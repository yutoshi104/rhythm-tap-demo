<?php
class Cache {
	const READ_TIMEOUT    = 5000;
	const CACHE_TIMEOUT   = 600;
	private $prefix = "";
	public $redis = NULL;

	public function __construct() {
	}

	public function __destruct() {
		if ($this->redis !== NULL) {
			try {
				$this->redis->close();
			} catch (RedisException $e) {
			}
		}
	}

	public function init($host, $port, $prefix) {
		try {
			$redis = new Redis();
			if( !$redis->connect($host, $port) ) {
				return false;
			}
			if( !$redis->setOption(Redis::OPT_READ_TIMEOUT, self::READ_TIMEOUT) ) {
				return false;
			}
			if( !$redis->setOption(Redis::OPT_PREFIX, $prefix) ) {
				return false;
			}
		} catch (RedisException $e) {
			return false;
		}

		$this->redis = $redis;

		return true;
	}

	public function set($key, $value, $expire = 0) {
		$ret = FALSE;
		try {
			if ($expire > 0) {
				$ret = $this->redis->set($key, $value, $expire);
			} else {
				$ret = $this->redis->set($key, $value);
			}
		} catch (Exception $e) {
			return FALSE;
		} catch (RedisException $e) {
			return FALSE;
		}
		return $ret;
	}

	public function get($key) {
		try {
			$val = $this->redis->get($key);
		} catch (Exception $e) {
			return FALSE;
		} catch (RedisException $e) {
			return FALSE;
		}
		return $val;
	}

	public function delete($key) {
		$ret = FALSE;
		try {
			$ret = $this->redis->delete($key);
		} catch (Exception $e) {
			return FALSE;
		} catch (RedisException $e) {
			return FALSE;
		}
		return $ret;
	}
}
