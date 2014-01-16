<?php
/**
 * PHP version 5.4
 *
 * Copyright (c) 2014 GOTO Hidenori <hidenorigoto@gmail.com>,
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    PHPMentors_Ginq_Example
 * @copyright  2014 GOTO Hidenori <hidenorigoto@gmail.com>
 * @license    http://opensource.org/licenses/BSD-2-Clause  The BSD 2-Clause License
 */

namespace PHPMentors\GinqExample\All;

use Ginq\Ginq;
use Stringy\StaticStringy as String;

/**
 * .txt で終わる要素が1つでもあるかどうか調べる
 */
class Any
{
    public function normal($data)
    {
        foreach ($data as $element)
        {
            $endStr = mb_substr($element, mb_strlen($element) - 4);
            if ($endStr == '.txt') {
                return true;
            }
        }

        return false;
    }

    public function normal2($data)
    {
        // 計算量の無駄はある・・・
        return array_reduce($data, function ($temp, $v) {
            return $temp | String::endsWith($v, '.txt');
        }, false);
    }

    public function ginq($data)
    {
        return $data->any(function ($v) {
            return String::endsWith($v, '.txt');
        });
    }
}