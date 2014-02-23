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

class Apc
{
    protected $str_id = null;
    protected $int_ttl = 0;


    public function __set($name, $mix_value)
    {
        if($name == 'value')
        {
            $this->set($mix_value);
        }
    }


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

    public function __isset($name)
    {
        if($name == 'value')
        {
            return $this->exists();
        }
    }



    public function __unset($name)
    {
        if($name == 'value')
        {
            $this->delete();
        }
    }


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



    public function set($mix_value)
    {
        apc_add($this->str_id, $mix_value, $this->int_ttl);
    }



    public function exists()
    {
        return apc_exists($this->str_id);
    }



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


    public function delete()
    {
        $bool_success = true;

        $bool_success = apc_delete($this->str_id);

        if(!$bool_success)
        {
            throw new \RuntimeException('Cannot delete stored value.');
        }
    }

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
