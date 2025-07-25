<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\PhpUnit;

use PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitNoExpectationAnnotationFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\PhpUnit\PhpUnitNoExpectationAnnotationFixer>
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @phpstan-import-type _AutogeneratedInputConfiguration from \PhpCsFixer\Fixer\PhpUnit\PhpUnitNoExpectationAnnotationFixer
 */
final class PhpUnitNoExpectationAnnotationFixerTest extends AbstractFixerTestCase
{
    /**
     * @param _AutogeneratedInputConfiguration $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: null|string, 2?: _AutogeneratedInputConfiguration}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'empty exception message' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         */
        public function testFnc()
        {
            $this->setExpectedException(\FooException::class, \'\');

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedException FooException
         * @expectedExceptionMessage
         */
        public function testFnc()
        {
            aaa();
        }
    }',
        ];

        yield 'expecting exception' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         */
        public function testFnc()
        {
            $this->setExpectedException(\FooException::class);

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedException FooException
         */
        public function testFnc()
        {
            aaa();
        }
    }',
        ];

        yield 'expecting rooted exception' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         */
        public function testFnc()
        {
            $this->setExpectedException(\FooException::class);

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedException \FooException
         */
        public function testFnc()
        {
            aaa();
        }
    }',
        ];

        yield 'expecting exception with msg' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         */
        public function testFnc()
        {
            $this->setExpectedException(\FooException::class, \'foo@bar\');

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedException FooException
         * @expectedExceptionMessage foo@bar
         */
        public function testFnc()
        {
            aaa();
        }
    }',
        ];

        yield 'expecting exception with code' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         */
        public function testFnc()
        {
            $this->setExpectedException(\FooException::class, null, 123);

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedException FooException
         * @expectedExceptionCode 123
         */
        public function testFnc()
        {
            aaa();
        }
    }',
        ];

        yield 'expecting exception with msg and code' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         */
        public function testFnc()
        {
            $this->setExpectedException(\FooException::class, \'foo\', 123);

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedException FooException
         * @expectedExceptionMessage foo
         * @expectedExceptionCode 123
         */
        public function testFnc()
        {
            aaa();
        }
    }',
        ];

        yield 'expecting exception with msg regex [but too low target]' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedException FooException
         * @expectedExceptionMessageRegExp /foo.*$/
         */
        public function testFnc()
        {
            aaa();
        }
    }',
            null,
            ['target' => PhpUnitTargetVersion::VERSION_3_2],
        ];

        yield 'expecting exception with msg regex' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         */
        public function testFnc()
        {
            $this->setExpectedExceptionRegExp(\FooException::class, \'/foo.*$/\');

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedException FooException
         * @expectedExceptionMessageRegExp /foo.*$/
         */
        public function testFnc()
        {
            aaa();
        }
    }',
            ['target' => PhpUnitTargetVersion::VERSION_4_3],
        ];

        yield 'use_class_const=false' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         */
        public function testFnc()
        {
            $this->setExpectedException(\'FooException\');

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedException FooException
         */
        public function testFnc()
        {
            aaa();
        }
    }',
            ['use_class_const' => false],
        ];

        yield 'keep rest of docblock' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * Summary.
         *
         * @param int $param
         * @return void
         */
        public function testFnc($param)
        {
            $this->setExpectedException(\FooException::class);

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * Summary.
         *
         * @param int $param
         * @expectedException FooException
         * @return void
         */
        public function testFnc($param)
        {
            aaa();
        }
    }',
        ];

        yield 'fix method without visibility' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         */
        function testFnc($param)
        {
            $this->setExpectedException(\FooException::class);

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedException FooException
         */
        function testFnc($param)
        {
            aaa();
        }
    }',
        ];

        yield 'fix final method' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         */
        final function testFnc($param)
        {
            $this->setExpectedException(\FooException::class);

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedException FooException
         */
        final function testFnc($param)
        {
            aaa();
        }
    }',
        ];

        yield 'ignore when no docblock' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        final function testFoo($param)
        {
            aaa();
        }

        /**
         */
        final function testFnc($param)
        {
            $this->setExpectedException(\FooException::class);

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        final function testFoo($param)
        {
            aaa();
        }

        /**
         * @expectedException FooException
         */
        final function testFnc($param)
        {
            aaa();
        }
    }',
        ];

        yield 'valid docblock but for property, not method' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedException FooException
         * @expectedExceptionCode 123
         */
         public $foo;

         public function bar()
         {
             /**
              * @expectedException FooException
              * @expectedExceptionCode 123
              */
             $baz = 1;

             /**
              * @expectedException FooException
              * @expectedExceptionCode 123
              */
             while (false) {}
         }
    }',
        ];

        yield 'respect \' and " in expected msg' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * Summary.
         *
         */
        public function testFnc($param)
        {
            $this->setExpectedException(\FooException::class, \'Foo \\\' bar " baz\');

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * Summary.
         *
         * @expectedException FooException
         * @expectedExceptionMessage Foo \' bar " baz
         */
        public function testFnc($param)
        {
            aaa();
        }
    }',
        ];

        yield 'special \ handling' => [
            <<<'EOT'
                    <?php
                    final class MyTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         */
                        public function testElementNonExistentOne()
                        {
                            $this->setExpectedException(\Cake\View\Exception\MissingElementException::class, 'A backslash at the end \\');

                            $this->View->element('non_existent_element');
                        }

                        /**
                         */
                        public function testElementNonExistentTwo()
                        {
                            $this->setExpectedExceptionRegExp(\Cake\View\Exception\MissingElementException::class, '#^Element file "Element[\\\\/]non_existent_element\\.ctp" is missing\\.$#');

                            $this->View->element('non_existent_element');
                        }
                    }
                EOT,
            <<<'EOT'
                    <?php
                    final class MyTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         * @expectedException \Cake\View\Exception\MissingElementException
                         * @expectedExceptionMessage A backslash at the end \
                         */
                        public function testElementNonExistentOne()
                        {
                            $this->View->element('non_existent_element');
                        }

                        /**
                         * @expectedException \Cake\View\Exception\MissingElementException
                         * @expectedExceptionMessageRegExp #^Element file "Element[\\/]non_existent_element\.ctp" is missing\.$#
                         */
                        public function testElementNonExistentTwo()
                        {
                            $this->View->element('non_existent_element');
                        }
                    }
                EOT,
        ];

        yield 'message on newline' => [
            <<<'EOT'
                    <?php
                    final class MyTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         */
                        public function testMessageOnMultilines()
                        {
                            $this->setExpectedException(\RuntimeException::class, 'Message on multilines AAA €');

                            aaa();
                        }

                        /**
                         * @foo
                         */
                        public function testMessageOnMultilinesWithAnotherTag()
                        {
                            $this->setExpectedException(\RuntimeException::class, 'Message on multilines BBB è');

                            bbb();
                        }

                        /**
                         *
                         * @foo
                         */
                        public function testMessageOnMultilinesWithAnotherSpaceAndTag()
                        {
                            $this->setExpectedException(\RuntimeException::class, 'Message on multilines CCC ✔');

                            ccc();
                        }
                    }
                EOT,
            <<<'EOT'
                    <?php
                    final class MyTest extends \PHPUnit_Framework_TestCase
                    {
                        /**
                         * @expectedException \RuntimeException
                         * @expectedExceptionMessage Message
                         *                           on
                         *                           multilines AAA
                         *                           €
                         */
                        public function testMessageOnMultilines()
                        {
                            aaa();
                        }

                        /**
                         * @expectedException \RuntimeException
                         * @expectedExceptionMessage Message
                         *                           on
                         *                           multilines BBB
                         *                           è
                         * @foo
                         */
                        public function testMessageOnMultilinesWithAnotherTag()
                        {
                            bbb();
                        }

                        /**
                         * @expectedException \RuntimeException
                         * @expectedExceptionMessage Message
                         *                           on
                         *                           multilines CCC
                         *                           ✔
                         *
                         * @foo
                         */
                        public function testMessageOnMultilinesWithAnotherSpaceAndTag()
                        {
                            ccc();
                        }
                    }
                EOT,
        ];

        yield 'annotation with double @' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * Double "@" is/was below
         */
        public function testFnc()
        {
            $this->setExpectedException(\FooException::class);

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * Double "@" is/was below
         * @@expectedException FooException
         */
        public function testFnc()
        {
            aaa();
        }
    }',
        ];

        yield 'annotation with text before @' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * We are providing invalid input, for that we @expectedException FooException
         */
        public function testFnc()
        {
            aaa();
        }
    }',
        ];

        yield [
            '<?php
    abstract class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedException FooException
         * @expectedExceptionMessage
         */
        abstract public function testFnc();
    }',
        ];

        yield 'expecting exception in single line comment' => [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /** */
        public function testFnc()
        {
            $this->setExpectedException(\FooException::class);

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /** @expectedException FooException */
        public function testFnc()
        {
            aaa();
        }
    }',
        ];

        yield 'expecting exception with message below' => [
            '<?php
    class MyTest extends TestCase
    {
        /**
         */
        public function testSomething()
        {
            $this->setExpectedException(\Foo\Bar::class);

            $this->initialize();
        }
    }',
            '<?php
    class MyTest extends TestCase
    {
        /**
         * @expectedException Foo\Bar
         *
         * Testing stuff.
         */
        public function testSomething()
        {
            $this->initialize();
        }
    }',
        ];
    }

    /**
     * @dataProvider provideWithWhitespacesConfigCases
     */
    public function testWithWhitespacesConfig(string $expected, ?string $input = null): void
    {
        $expected = str_replace(['    ', "\n"], ["\t", "\r\n"], $expected);
        if (null !== $input) {
            $input = str_replace(['    ', "\n"], ["\t", "\r\n"], $input);
        }

        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideWithWhitespacesConfigCases(): iterable
    {
        yield [
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         */
        public function testFnc()
        {
            $this->setExpectedException(\FooException::class, \'foo\', 123);

            aaa();
        }
    }',
            '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @expectedException FooException
         * @expectedExceptionMessage foo
         * @expectedExceptionCode 123
         */
        public function testFnc()
        {
            aaa();
        }
    }',
        ];

        yield [
            '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
/**
*/
public function testFnc()
{
    $this->setExpectedException(\FooException::class, \'foo\', 123);

aaa();
}
}',
            '<?php
final class MyTest extends \PHPUnit_Framework_TestCase
{
/**
* @expectedException FooException
* @expectedExceptionMessage foo
* @expectedExceptionCode 123
*/
public function testFnc()
{
aaa();
}
}',
        ];
    }
}
