<?php
/**
 * Created by PhpStorm.
 * User: liweitang
 * Date: 2018/3/10
 * Time: 10:13
 */

/**
 * 引入工具类
 */
include "IdGenerator.php";

/**
 * 初始化工具类
 */
IdGenerator::init(1);


printf("%s\n", IdGenerator::nextId());
printf("%s\n", IdGenerator::nextId());
printf("%s\n", IdGenerator::nextId());
printf("%s\n", IdGenerator::nextId());
printf("%s\n", IdGenerator::nextId());