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

(@include_once __DIR__ . '/vendor/autoload.php') || @include_once __DIR__ . '/../../autoload.php';

use \Malenki\Apc;

// sets and tests existing values…
$apc1 = new Apc('some_key', 40);
var_dump($apc1->exists());
$apc1->value = 'foo';

$apc2 = new Apc('some_other_key', 40);
var_dump($apc2->exists());
$apc2->value = 'bar';

var_dump($apc1->exists());
var_dump($apc2->exists());


// get values…
var_dump($apc1->get());
var_dump($apc2->get());

// or
var_dump($apc1->value);
var_dump($apc2->value);

// or
echo $apc1;
echo "\n";
echo $apc2;
echo "\n";
