<?php

namespace Lyz\Utils;

/**
 * 验证工具类
 * Class ValidateUtil
 * @package Lyz\Utils
 */
class ValidateUtil
{
    /**
     * 是否手机号
     * 
     * @param string $phone
     * @return bool
     */
    public static function checkPhone($phone)
    {
        if (preg_match("/^1[3456789]{1}\d{9}$/", $phone)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 是否邮箱
     * 
     * @param string $email
     * @return bool
     */
    public static function checkEmail($email)
    {
        $pattern = "/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/";
        if (preg_match($pattern, $email)) {
            return true;
        } else {
            return false;
        }
    }
}
