<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0.1
 * @copyright 2015-2019
 * @context: Origin框架Mysql封装类
 */
namespace Origin\Package;

class DB
{
    /**
     * @access public
     * @param string|null $connect_name 链接名
     * @return object
     * @context Mysql数据库操作方法
     */
    static function mysql(?string $connect_name=null)
    {
        $_dao = new Database($connect_name,Database::RESOURCE_TYPE_MYSQL);
        $_dao->__setSQL($_dao);
        return $_dao;
    }

    /**
     * @access public
     * @param string|null $connect_name 链接名
     * @return object
     * @context PostgreSQL数据库操作方法
     */
    static function pgsql(?string $connect_name=null)
    {
        $_dao = new Database($connect_name,Database::RESOURCE_TYPE_PGSQL);
        $_dao->__setSQL($_dao);
        return $_dao;
    }

    /**
     * @access public
     * @param string|null $connect_name 链接名
     * @return object
     * @context SQL server数据库操作方法
     */
    static function mssql(?string $connect_name=null)
    {
        $_dao = new Database($connect_name,Database::RESOURCE_TYPE_MSSQL);
        $_dao->__setSQL($_dao);
        return $_dao;
    }

    /**
     * @access public
     * @param string|null $connect_name 链接名
     * @return object
     * @context sqlite数据库操作方法
     */
    static function sqlite(?string $connect_name=null)
    {
        $_dao = new Database($connect_name,Database::RESOURCE_TYPE_SQLITE);
        $_dao->__setSQL($_dao);
        return $_dao;
    }

    /**
     * @access public
     * @param string|null $connect_name 链接名
     * @return object
     * @context SQL server数据库操作方法
     */
    static function oracle(?string $connect_name=null)
    {
        $_dao = new Database($connect_name,Database::RESOURCE_TYPE_ORACLE);
        $_dao->__setSQL($_dao);
        return $_dao;
    }

    /**
     * @access public
     * @param string|null $connect_name 链接名
     * @return object
     * @context Redis数据库操作方法
     */
    static function redis(?string $connect_name=null)
    {
        # 调用Redis数据库核心包
        return new Redis($connect_name);
    }

    /**
     * @access public
     * @param string|null $connect_name 链接名
     * @return object
     * @context MongoDB数据库操作方法
     */
    static function mongodb(?string $connect_name=null)
    {
        $_dao = new Mongodb($connect_name);
        $_dao->__setSQL($_dao);
        return $_dao;
    }
}