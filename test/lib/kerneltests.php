<?php
namespace test;
/*
 * Copyright (C) 2012 Michael Saller
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions
 * of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO
 * THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */

class KernelTests extends \StackOSTest {
    public function testNoContextOnStack() {
        try {
            self::$kernel->getFile(new \stackos\User(self::$kernel, 'noname'), '/');
            $this->fail('Expecting Exception_MissingSecurityStrategy');
        }
        catch(\stackos\Exception_MissingSecurityStrategy $e) {
            // pass
        }
    }

    public function testPopEmptyStrategyStack() {
        try {
            self::$kernel->pullSecurityStrategy();
            $this->fail('Expecting Exception_MissingSecurityStrategy');
        }
        catch(\stackos\Exception_MissingSecurityStrategy $e) {
            // pass
        }
    }

    /** Assert that the security stack does not get corrupted here
     */
    public function testInitStrategyStack() {
        $kernel = self::$kernel;
        $mockStrategy = new KernelTests_Mock_Strategy($kernel);
        $kernel->pushSecurityStrategy($mockStrategy);
        $kernel->destroy();
        $this->assertTrue($kernel->currentStrategy() === $mockStrategy);
        $kernel->init();
        $this->assertTrue($kernel->currentStrategy() === $mockStrategy);
    }

    /** Assert that the security stack does not get corrupted when an exception occurs
     * @test whitebox
     */
    public function testInitStrategyStackException() {
        $kernel = new KernelTests_Mock_Kernel('http://root:root@127.0.0.1:5984', 'stackos');
        $mockStrategy = new KernelTests_Mock_Strategy($kernel);
        $kernel->pushSecurityStrategy($mockStrategy);
        $kernel->destroy();
        $this->assertTrue($kernel->currentStrategy() === $mockStrategy);
        try {
            // mock kernel will throw an exception
            $kernel->init();
            $this->fail('Expecting KernelTests_Mock_Kernel_Exception');
        } catch(KernelTests_Mock_Kernel_Exception $e) {
            // pass
        }
        $this->assertTrue($kernel->currentStrategy() === $mockStrategy);
    }
}

class KernelTests_Mock_Kernel extends \stackos\Kernel {
    protected function getAdapter() {
        throw new KernelTests_Mock_Kernel_Exception('Testing finally');
    }
}
class KernelTests_Mock_Kernel_Exception extends \stackos\Exception {

}
class KernelTests_Mock_Strategy extends \stackos\security\BaseStrategy {
}