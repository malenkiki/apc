<?php
/*
 * Copyright (c) 2014 Michel Petit <petit.michel@gmail.com>
 * 
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 * 
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */


namespace Malenki;

/**
 * Apc simple wrapper. 
 * 
 * You can set new value or update one by calling uniq method `set()`.
 *
 * You can get, delete or test if entry exists with respectively `get()`, 
 * `delete()`, and `exists()` methods.
 *
 * A static method `clean()` allow you to clean all cache or just some 
 * elements.
 * 
 * With magic attribute `value` you can use `isset()`, `unset()` functions, or 
 * set and read content directly.
 *
 * @copyright 2014 Michel PETIT
 * @author Michel Petit <petit.michel@gmail.com> 
 * @license MIT
 */
class Apc
{
    /**
     * The APC key 
     * 
     * @var string
     * @access protected
     */
    protected $str_id = null;

    /**
     * Time to live, in second. 
     * 
     * @var integer
     * @access protected
     */
    protected $int_ttl = 0;



    /**
     * Magic setter. 
     * 
     * Acts like Apc::set() method.
     *
     * @param string $name Must be `value`.
     * @param mixed $mix_value content to set.
     * @access public
     * @return Apc
     */
    public function __set($name, $mix_value)
    {
        if($name == 'value')
        {
            return $this->set($mix_value);
        }
    }



    /**
     * Magic getter.
     *
     * Acts like Apc::get() method.
     * 
     * @param string $name Must be `string`
     * @access public
     * @return mixed Entry content
     */
    public function __get($name)
    {
        if($name == 'value')
        {
            $bool_success = true;
            $mix_result = apc_fetch($this->str_id, $bool_success);

            if($bool_success)
            {
                return $mix_result;
            }
            else
            {
                return null;
            }
        }
    }



    /**
     * Magic isset.
     * 
     * Acts as Apc::exists() method.
     *
     * @param string $name Must be `value`.
     * @access public
     * @return boolean
     */
    public function __isset($name)
    {
        if($name == 'value')
        {
            return $this->exists();
        }
    }



    /**
     * Magic unset call.
     *
     * Acts like Apc::delete() method.
     *
     * @param string $name Must be `value`
     * @access public
     * @return void
     */
    public function __unset($name)
    {
        if($name == 'value')
        {
            $this->delete();
        }
    }


    /**
     * Clear all the cache, the user cahe type or the opcade cache type. 
     * 
     * @param string $type Cache type name
     * @static
     * @throw \InvalidArgumentException If given type is not valid.
     * @access public
     * @return void
     */
    public static function clear($type = 'all')
    {
        if(!in_array($type, array('all', 'user', 'opcode')))
        {
            throw new \InvalidArgumentException('To clean cache, you must use either "all", "user" or "opcode" value.');
        }

        if($type == 'all')
        {
            apc_clear_cache($type);
        }
        else
        {
            apc_clear_cache();
            apc_clear_cache('user');
            apc_clear_cache('opcode');
        }
    }



    /**
     * Constructor defines new cache entry by giving its name and optionnaly its time to live in seconds. 
     * 
     * @throw \RuntimeException If APC extension is not loaded
     * @throw \InvalidArgumentException If key is not valid string.
     * @throw \InvalidArgumentException If TTL is not numeric.
     * @throw \InvalidArgumentException If TTL is negative value.
     * @param string $str_key Key name that defines this entry.
     * @param int $int_ttl Optional time to live. If not given or equals to 0, then will be present untill cache is clean.
     * @access public
     * @return void
     */
    public function __construct($str_key, $int_ttl = 0)
    {
        if(!extension_loaded('apc'))
        {
            throw new \RuntimeException('APC extension must be available in order to use ' . __CLASS__);
        }

        if(!is_string($str_key) || empty($str_key))
        {
            throw new \InvalidArgumentException('Key must be a no null string.');
        }

        if(!is_numeric($int_ttl))
        {
            throw new \InvalidArgumentException('TTL must be numeric value.');
        }

        $int_ttl = (integer) $int_ttl;

        if($int_ttl < 0)
        {
            throw new \InvalidArgumentException('TTL must be positive or null integer.');
        }

        $this->str_id = md5($str_key);
        $this->int_ttl = $int_ttl;
    }



    /**
     * Sets value for current key.
     * 
     * @param mixed $mix_value The value content.
     * @access public
     * @return Apc
     */
    public function set($mix_value)
    {
        if($this->exists())
        {
            apc_store($this->str_id, $mix_value, $this->int_ttl);
        }
        else
        {
            apc_add($this->str_id, $mix_value, $this->int_ttl);
        }

        return $this;
    }



    /**
     * Tests whether the given entry exists.
     * 
     * @access public
     * @return boolean
     */
    public function exists()
    {
        return apc_exists($this->str_id);
    }



    /**
     * Gets the value for the current entry. 
     * 
     * @throw \RuntimeException If content cannot be fetched.
     * @access public
     * @return mixed The entry content.
     */
    public function get()
    {
        $bool_success = true;
        $mix_result = apc_fetch($this->str_id, $bool_success);

        if(!$bool_success)
        {
            throw new \RuntimeException('Cannot get stored value.');
        }

        return $mix_result;
    }



    /**
     * Deletes current entry.
     * 
     * @throw \RuntimeException If it cannot delete current entry.
     * @access public
     * @return Apc
     */
    public function delete()
    {
        $bool_success = true;

        $bool_success = apc_delete($this->str_id);

        if(!$bool_success)
        {
            throw new \RuntimeException('Cannot delete stored value.');
        }

        return $this;
    }



    /**
     * Return entry's content as string in string context.
     *
     * If used into string context, the current object will return the scalar 
     * content as a string or, if it is not a scalar, does a `print_r()` 
     * rendering result.
     * 
     * @access public
     * @return string
     */
    public function __toString()
    {
        $result = $this->get();

        if(is_scalar($result))
        {
            return (string) $result; 
        }
        else
        {
            return print_r($result, true);
        }
    }
}
