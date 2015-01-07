<?php

namespace Sabre\DAV\Auth\Backend;

use Sabre\DAV;
use Sabre\HTTP;

require_once 'Sabre/HTTP/ResponseMock.php';

class AbstractBasicTest extends \PHPUnit_Framework_TestCase {

    function testCheckNoHeaders() {

        $request = new HTTP\Request();
        $response = new HTTP\Response();

        $backend = new AbstractBasicMock();

        $this->assertFalse(
            $backend->check($request, $response)[0]
        );

    }

    function testCheckUnknownUser() {

        $request = HTTP\Sapi::createFromServerArray(array(
            'PHP_AUTH_USER' => 'username',
            'PHP_AUTH_PW' => 'wrongpassword',
        ));
        $response = new HTTP\Response();

        $backend = new AbstractBasicMock();

        $this->assertFalse(
            $backend->check($request, $response)[0]
        );

    }

    function testCheckSuccess() {

        $request = HTTP\Sapi::createFromServerArray(array(
            'PHP_AUTH_USER' => 'username',
            'PHP_AUTH_PW' => 'password',
        ));
        $response = new HTTP\Response();

        $backend = new AbstractBasicMock();
        $this->assertEquals(
            [true, 'principals/username'],
            $backend->check($request, $response)
        );

    }

    function testRequireAuth() {

        $request = new HTTP\Request();
        $response = new HTTP\Response();

        $backend = new AbstractBasicMock();
        $backend->setRealm('writing unittests on a saturday night');
        $backend->challenge($request, $response);

        $this->assertEquals(
            'Basic realm="writing unittests on a saturday night"',
            $response->getHeader('WWW-Authenticate')
        );

    }

    function testInitializeWhiteList() {

        $backend = new AbstractBasicMock();

        $this->assertEquals(
            $backend->getWhiteList(new HTTP\Request()),
            []
        );

    }

    function testWhiteList() {

        $backend = new AbstractBasicMock();
        $list    = ['/signin'];
        $backend->setWhiteList($list);

        $this->assertEquals(
            $backend->getWhiteList(new HTTP\Request()),
            $list
        );

    }

}


class AbstractBasicMock extends AbstractBasic {

    /**
     * Validates a username and password
     *
     * This method should return true or false depending on if login
     * succeeded.
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    function validateUserPass($username, $password) {

        return ($username == 'username' && $password == 'password');

    }

}
