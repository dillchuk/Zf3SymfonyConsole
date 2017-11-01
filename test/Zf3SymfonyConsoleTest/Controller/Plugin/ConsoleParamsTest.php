<?php

namespace Zf3SymfonyConsoleTest\Controller\Plugin;

use Zf3SymfonyConsole\Controller\Plugin\ConsoleParams;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\ArgvInput;
use Faker\Factory as FakerFactory;

class ConsoleParamsTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider dataConstructor
     */
    public function testConstructor(ArgvInput $input) {
        $plugin1 = new ConsoleParams();
        $this->assertNull($plugin1->getInput());
        $plugin1->setInput($input);

        $plugin2 = new ConsoleParams($input);

        $this->assertSame($input, $plugin1->getInput());
        $this->assertSame($input, $plugin2->getInput());
    }

    public static function dataConstructor() {
        return [
            [new ArgvInput([])],
            [new ArgvInput(['value1' => 1])],
            [new ArgvInput(['value2' => 2])],
        ];
    }

    public function testInvokeDefault() {
        $plugin = new ConsoleParams();
        $this->assertSame($plugin, $plugin());
    }

    public function testFromConsoleDefault() {
        $plugin = new ConsoleParams();
        $faker = FakerFactory::create();
        $default = $faker->randomNumber(7);
        $this->assertSame($default, $plugin->fromConsole('junk', $default));
        $this->assertSame($default, $plugin->fromRoute('junk', $default));
        $this->assertSame($default, $plugin('junk', $default));
    }

    /**
     * @dataProvider dataInvokeSpecific
     */
    public function testInvokeSpecific(array $input, $paramName, $expected) {
        $inputInstance = $this->getMock(Input::class);
        $inputInstance->expects($this->any())
        ->method('getArguments')->willReturn($input);

        $plugin = new ConsoleParams($inputInstance);
        $this->assertSame($expected, $plugin->fromConsole($paramName));
        $this->assertSame($expected, $plugin->fromRoute($paramName));
        $this->assertSame($expected, $plugin($paramName));
    }

    public static function dataInvokeSpecific() {

        $needle1 = ['needle' => '1'];
        $other2 = ['other' => '2'];

        return [
            [[], 'needle', null],
            [$needle1, 'needle', '1'],
            [$needle1 + $other2, 'needle', '1'],
            [$other2, 'needle', null],
        ];
    }

    /**
     * @dataProvider dataInvokeSpecific
     */
    public function testGetAll(array $input) {
        $inputInstance = $this->getMock(Input::class);
        $inputInstance->expects($this->any())
        ->method('getArguments')->willReturn($input);

        $plugin = new ConsoleParams($inputInstance);
        $this->assertSame($input, $plugin->fromConsole());
        $this->assertSame($input, $plugin->fromRoute());
    }

    public function testUselessGetters() {
        $inputInstance = $this->getMock(Input::class);
        $inputInstance->expects($this->any())
        ->method('getArguments')->willReturn(['a' => 'a']);

        $faker = FakerFactory::create();

        $plugin = new ConsoleParams($inputInstance);
        foreach (['fromPost', 'fromFiles', 'fromQuery', 'fromHeader'] as $from) {
            $this->assertNull($plugin->$from('a'));
            $this->assertNull($plugin->$from());

            $default = $faker->randomNumber(7);
            $this->assertSame($default, $plugin->$from('a', $default));
            $this->assertSame($default, $plugin->$from(null, $default));
        }
    }

}
