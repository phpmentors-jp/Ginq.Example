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
use PHPMentors\GinqExample\TestCase;

class Emploee
{
    public $name;
    public $department;
    public $salary;

    function __construct($name, $department, $salary)
    {
        $this->name = $name;
        $this->department = $department;
        $this->salary = $salary;
    }
}

class ToLookupTest extends TestCase
{
    private $data;

    protected function setUp()
    {
        $employees = [];
        $employees[] = new Emploee('あいう', '部署1', 1000);
        $employees[] = new Emploee('かきく', '部署2', 1200);
        $employees[] = new Emploee('さしす', '部署1', 800);
        $employees[] = new Emploee('たちつ', '部署3', 900);
        $employees[] = new Emploee('なにぬ', '部署2', 1000);
        $employees[] = new Emploee('はひふ', '部署4', 700);
        $employees[] = new Emploee('まみむ', '部署2', 900);

        $this->data = Ginq::from($employees);
    }

    /**
     * 部署でグループ
     * @test
     */
    public function ToLookup()
    {
        $result = $this->data->toLookup(
            function ($v) {return $v->department;},
            function ($v) {return [$v->name, $v->salary];}
        )->toArrayRec();

        $this->assertThat($result, $this->equalTo([
            '部署1' => [
                ['あいう', 1000,],
                ['さしす', 800,],
            ],
            '部署2' => [
                ['かきく', 1200,],
                ['なにぬ', 1000,],
                ['まみむ', 900,],
            ],
            '部署3' => [
                ['たちつ', 900,],
            ],
            '部署4' => [
                ['はひふ', 700,],
            ],
        ]));
    }

    /**
     * 時給でグループ
     * @test
     */
    public function ToLookup2()
    {
        $result = $this->data
            ->orderBy('salary')
            ->thenBy('name')
            ->toLookup(
                function ($v) {return $v->salary;},
                function ($v) {return [$v->name];}
            )->toArrayRec();

        $this->assertThat($result, $this->equalTo([
            700 => [
                ['はひふ',],
            ],
            800 => [
                ['さしす',],
            ],
            900 => [
                ['たちつ',],
                ['まみむ',],
            ],
            1000 => [
                ['あいう',],
                ['なにぬ',],
            ],
            1200 => [
                ['かきく',],
            ],
        ]));
    }

    /**
     * グループと連想配列の再構成
     * @test
     */
    public function ToLookup3()
    {
        $result = $this->data
            ->orderBy('salary')
            ->thenBy('name')
            ->toLookup(
                function ($v) {return $v->salary;},
                function ($v) {return ['name' => $v->name];}
            )
            ->toArrayRec();

        $data2 = Ginq::from($result);

        $result2 = $data2->map(
                function ($v) {
                    $temp = [];
                    $temp['list'] = $v;
                    $temp['count'] = count($temp['list']);

                    return $temp;
                }
            )
            ->toArrayRec();

        $this->assertThat($result2, $this->equalTo([
            700 => [
                'list' => [
                    [ 'name' => 'はひふ',],
                ],
                'count' => 1,
            ],
            800 => [
                'list' => [
                    ['name' => 'さしす',],
                ],
                'count' => 1,
            ],
            900 => [
                'list' =>
                [
                    ['name' => 'たちつ',],
                    ['name' => 'まみむ',],
                ],
                'count' => 2,
            ],
            1000 => [
                'list' =>
                [
                    ['name' => 'あいう',],
                    ['name' => 'なにぬ',],
                ],
                'count' => 2,
            ],
            1200 => [
                'list' =>
                [
                    ['name' => 'かきく',],
                ],
                'count' => 1,
            ],
        ]));
    }
}