<?php

namespace Lyz\Common\Pojo;

use Lyz\Common\BaseObject;

/**
 * 输出模型
 */
class OutputModel extends BaseObject
{
    /**
     * 成功
     */
    const STATUS_SUCCESS = 1;

    /**
     * 失败
     */
    const STATUS_FAIL = 0;

    /**
     * 成功
     */
    const CODE_SUCCESS = 1;

    /**
     * 失败
     */
    const CODE_FAIL = 0;

    /**
     * @var integer 状态
     */
    protected $_status;

    /**
     * @var integer 状态码
     */
    protected $_code;

    /**
     * @var string 错误信息
     */
    protected $_errorMessage;

    /**
     * 初始化输出对象，默认失败
     *
     * @param int    $status
     * @param int    $code
     * @param string $errorMessage
     * @return static
     */
    public static function init($status = null, $code = null, $errorMessage = null)
    {
        $cls = new static();
        $cls->_status = is_null($status) ? self::STATUS_FAIL : $status;
        $cls->_code = is_null($code) ? self::CODE_FAIL : $code;
        !is_null($errorMessage) && $cls->_errorMessage = $errorMessage;

        return $cls;
    }

    /**
     * 设置状态码
     *
     * @param integer $code
     * @return void
     */
    public function setCode(int $code)
    {
        $this->_code = $code;
    }

    /**
     * 获取状态码
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * 设置错误信息
     *
     * @param string $errorMessage
     * @return void
     */
    public function setErrorMessage(string $errorMessage)
    {
        $this->_errorMessage = $errorMessage;
    }

    /**
     * 获取错误信息
     *
     * @return string errorMessage
     */
    public function getErrorMessage()
    {
        return $this->_errorMessage;
    }

    /**
     * 设置状态为成功
     */
    public function setSuccess()
    {
        $this->_status = static::STATUS_SUCCESS;
        if ($this->_code == self::CODE_FAIL) {
            $this->_code = self::CODE_SUCCESS;
        }
        if (is_null($this->_errorMessage)) {
            $this->_errorMessage = '操作成功';
        }
    }

    /**
     * 设置状态为失败
     */
    public function setFail()
    {
        $this->_status = static::STATUS_FAIL;
        if ($this->_code == self::CODE_SUCCESS) {
            $this->_code = self::CODE_FAIL;
        }
        if (is_null($this->_errorMessage)) {
            $this->_errorMessage = '操作失败';
        }
    }

    /**
     * 是否成功
     *
     * @return boolean
     */
    public function isSucceed()
    {
        return $this->_status === self::STATUS_SUCCESS;
    }

    /**
     * 创建输出
     *
     * @return array
     */
    public function createOut()
    {
        return [];
    }

    /**
     * 创建错误状态输出
     *
     * @return array
     */
    public function createErrorOut()
    {
        return [];
    }
}
