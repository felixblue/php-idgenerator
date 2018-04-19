<?php

/**
 * Id生成器，参考Twitter的snowflake算法
 * 生成的Id是一个64bit的长整型数字，
 * 其中1位符号位，41位毫秒数，10位实例Id，12位流水号
 * Created by PhpStorm.
 * User: liweitang
 * Date: 2018/3/9
 * Time: 18:33
 */
class IdGenerator
{
    /**
     * 实例Id，及其占位数和最大值
     */
    private static $instanceId = -1;
    const instanceIdBits = 10;
    private static $maxInstanceId = (1 << self::instanceIdBits) - 1;

    /**
     * 流水号，及其占位数和最大值
     */
    private static $sequence = 0;
    const sequenceBits = 12;
    private static $maxSequence = (1 << self::sequenceBits) - 1;

    /**
     * 标识是否已经初始化
     */
    private static $inited = false;

    /**
     * 实例Id偏移位，毫秒数偏移位
     */
    private static $instanceIdShift = self::sequenceBits;
    private static $timestampLeftShift = self::sequenceBits + self::instanceIdBits;

    /**
     * 时间基线 2010-01-01
     */
    private static $baseline = 1262275200000;

    /**
     * 最后一次获取Id值的时间戳
     */
    private static $lastTimestamp = -1;

    /**
     * 初始化
     *
     * @param $instanceId
     * @throws Exception
     */
    public static function init($instanceId)
    {
        if ($instanceId > self::$maxInstanceId || $instanceId < 0) {
            throw new RuntimeException(sprintf("instanceId can't be greater than %d or less than 0", self::$maxInstanceId));
        }
        self::$instanceId = $instanceId;
        self::$inited = true;
    }

    /**
     * 获取Id值
     *
     * @return string Id值
     */
    public static function nextId()
    {
        return (string)self::generateNumber();
    }

    /**
     * 生成序列
     *
     * @return float 64位数字
     */
    private static function generateNumber()
    {
        if (!self::$inited) {
            throw new RuntimeException("it's uninitialized");
        }

        $timestamp = self::currentMillis();
        if ($timestamp < self::$lastTimestamp) {
            throw new RuntimeException(sprintf("clock moved backwards, refusing to generate id for %d milliseconds", self::$lastTimestamp - $timestamp));
        }

        if ($timestamp == self::$lastTimestamp) {
            self::$sequence = (self::$sequence + 1) & self::$maxSequence;
            if (self::$sequence == 0) {
                $timestamp = self::tilNextMillis(self::$lastTimestamp);
            }
        } else {
            self::$sequence = 0;
        }

        self::$lastTimestamp = $timestamp;

        return (($timestamp - self::$baseline) << self::$timestampLeftShift) | (self::$instanceId << self::$instanceIdShift) | self::$sequence;
    }

    /**
     * 获取下一个时间戳
     *
     * @param $lastTimestamp 时间戳
     * @return float 时间戳
     */
    private static function tilNextMillis($lastTimestamp)
    {
        $timestamp = self::currentMillis();
        while ($timestamp <= $lastTimestamp) {
            $timestamp = self::currentMillis();
        }
        return $timestamp;
    }

    /**
     * 获取时间戳
     *
     * @return float 时间戳
     */
    private static function currentMillis()
    {
        return round(microtime(true) * 1000);
    }
}