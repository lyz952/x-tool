<?php

namespace lyz\Common;

/**
 * Class BaseObject
 * 对象类
 * @package lyz\Common
 */
class BaseObject
{
    /**
     * 创建类
     *
     * @param array $data
     * @return static
     * @throws \Exception
     */
    public static function create($data = [])
    {
        $obj = new static();
        if (empty($data)) {
            $data = [];
        }
        $obj->setData($data);
        return $obj;
    }

    /**
     * 设置数据
     *
     * @param array $data
     * @return void
     * @throws \Exception
     */
    public function setData($data)
    {
        self::_setData($this, $data);
    }

    /**
     * 类赋值
     *
     * @param object $cls
     * @param array  $data
     * @return void
     * @throws \Exception
     */
    protected static function _setData($cls, $data)
    {
        // 反射类
        $reflection = new \ReflectionClass(get_class($cls));
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            // 属性名称
            $propertyName = $property->getName();
            try {
                // 属性是否是公共属性 && 数组中是否有此属性
                if ($property->isPublic() && array_key_exists($propertyName, $data)) {
                    // 属性对应的值
                    $propertyValue = $data[$propertyName];
                    if (is_null($propertyValue)) {
                        $cls->$propertyName = null;
                        continue;
                    }

                    // 解析注释，并返回类对象名称
                    $className = self::getClassNameByDoc($property);
                    // 判断名称是否为数组
                    if (is_array($className)) {
                        $list = [];
                        if (is_array($propertyValue)) {
                            foreach ($propertyValue as $propertyK => $propertyV) {
                                $obj = new $className[0];
                                if (method_exists($obj, 'setData')) {
                                    $obj->setData($propertyV);
                                } else {
                                    self::_setData($obj, $propertyV);
                                }
                                $list[$propertyK] = $obj;
                            }
                        }
                        $cls->$propertyName = $list;
                    } else {
                        $obj = null;
                        // 自定义类对象
                        if (strpos($className, '\\') !== false) {
                            if (!class_exists($className)) {
                                throw new \Exception("类($className)不存在");
                            }

                            $obj = new $className;
                        }

                        if (!is_null($obj)) {
                            if (!empty($propertyValue)) {
                                if (method_exists($obj, 'setData')) {
                                    $obj->setData($propertyValue);
                                } else {
                                    self::_setData($obj, $propertyValue);
                                }
                            }

                            $cls->$propertyName = $obj;
                        } else {
                            $cls->$propertyName = $propertyValue;
                        }
                    }
                }
            } catch (\Throwable | \Exception $ex) {
                throw new \Exception("解析属性{$propertyName}出现错误:" . $ex->getMessage());
            }
        }
    }

    /**
     * 返回数组数据
     *
     * @param boolean $excludeEmptyValues 是否排除 null
     * @return array
     */
    public function toArray(bool $excludeEmptyValues = true)
    {
        $keyValueMap = get_object_vars($this);

        $return = [];
        foreach ($keyValueMap as $property => $propertyValue) {
            if ($excludeEmptyValues && is_null($propertyValue)) {
                continue;
            }
            if (is_object($propertyValue)) {
                if (method_exists($propertyValue, 'toArray')) {
                    $return[$property] = $propertyValue->toArray();
                }
            } else if (is_array($propertyValue)) {
                $return[$property] = [];
                foreach ($propertyValue as $i => $value) {
                    if (is_object($value)) {
                        if (method_exists($value, 'toArray')) {
                            $return[$property][$i] = $value->toArray();
                        }
                    } else {
                        $return[$property][$i] = $value;
                    }
                }
            } else {
                $return[$property] = $propertyValue;
            }
        }

        return $return;
    }

    /**
     * 解析注解获取属性类型
     *
     * @param \ReflectionProperty $property
     * @return array|string
     * @throws \Exception
     */
    protected static function getClassNameByDoc(\ReflectionProperty $property)
    {
        $pattern = '/\Q@\Evar[\s]+([a-zA-Z0-9-\Q\\\E|]+)(\Q[]\E)?/';

        $doc = $property->getDocComment();
        preg_match($pattern, $doc, $result);
        $match_count = count($result);
        switch ($match_count) {
            case 3:
                return [$result[1]];
            case 2:
                return $result[1];
            default:
                throw new \Exception("分析注解失败");
        }
    }
}
