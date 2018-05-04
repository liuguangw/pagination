<?php
use PHPUnit\Framework\TestCase;
use liuguang\pagination\Paginator;

class CommonTest extends TestCase
{

    public function testArray()
    {
        $paginator = new Paginator(2, 1);
        $arr = $paginator->toArray();
        $this->assertEquals([
            'previous' => null,
            'first_page' => null,
            'left_blank' => false,
            'items' => [],
            'right_blank' => false,
            'last_page' => '/page/2',
            'next' => '/page/2'
        ], $arr);
        //
        $paginator = new Paginator(10, 3);
        $arr = $paginator->toArray();
        
        $this->assertEquals([
            'previous' => '/page/2',
            'first_page' => '/page/1',
            'left_blank' => false,
            'items' => [
                0 => [
                    'page' => 2,
                    'url' => '/page/2',
                    'active' => false
                ],
                1 => [
                    'page' => 3,
                    'url' => '/page/3',
                    'active' => true
                ],
                2 => [
                    'page' => 4,
                    'url' => '/page/4',
                    'active' => false
                ],
                3 => [
                    'page' => 5,
                    'url' => '/page/5',
                    'active' => false
                ],
                4 => [
                    'page' => 6,
                    'url' => '/page/6',
                    'active' => false
                ]
            ],
            'right_blank' => true,
            'last_page' => '/page/10',
            'next' => '/page/4'
        ], $arr);
    }

    private function doCompare(Paginator $paginator, $name)
    {
        $actualPath = __DIR__ . '/../actual/' . $name . '.html';
        $expectedPath = __DIR__ . '/../expected/' . $name . '.html';
        $actualDir = __DIR__ . '/../actual';
        if (! is_dir($actualDir)) {
            mkdir($actualDir);
        }
        $html = $paginator->render();
        file_put_contents($actualPath, $html);
        $this->assertFileEquals($expectedPath, $actualPath);
    }

    public function testHtml()
    {
        $paginator = new Paginator(2, 1);
        $this->doCompare($paginator, '1');
        $paginator = new Paginator(10, 3);
        $this->doCompare($paginator, '2');
    }
}

