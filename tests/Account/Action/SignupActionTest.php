<?php

namespace Tests\App\Account\Action;

use App\Account\Action\SignUpAction;
use App\Auth\DatabaseAuth;
use App\Auth\User;
use App\Auth\UserTable;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use PDO;
use PDOStatement;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Tests\ActionTestCase;

class SignupActionTest extends ActionTestCase
{
    use ArraySubsetAsserts;

    use ProphecyTrait;
    /**
     * renderer
     *
     * @var ObjectProphecy|RendererInterface
     */
    private $renderer;

    /**
     * renderer
     *
     * @var ObjectProphecy|RendererInterface
     */
    private $userTable;

    /**
     * renderer
     *
     * @var ObjectProphecy|DatabaseAuth
     */
    private $auth;
    
    /**
     * flashService
     *
     * @var ObjectProphecy|FlashService
     */
    private $flashService;
    /**
     * action
     *
     * @var SignUpAction
     */
    private $action;

    protected function setUp(): void
    {
        //usertable
        $this->userTable = $this->prophesize(UserTable::class);
        $pdo = $this->prophesize(PDO::class);
        $statement = $this->getMockBuilder(PDOStatement::class)->getMock();
        $statement->expects($this->any())->method('fetchColumn')->willReturn(false);
        $pdo->prepare(Argument::any())->willReturn($statement);
        $pdo->lastInsertId()->WillReturn(3);
        $this->userTable->getTable()->willReturn('fake');
        $this->userTable->getPdo()->willReturn($pdo->reveal());
        //Renderer
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->renderer->render(Argument::any(), Argument::any())->willReturn('');
        //Router
        $this->router = $this->prophesize(Router::class);
        $this->router->generateUri(Argument::any())->will(function ($agrs) {
            return $agrs[0];
        });
        //Auth
        $this->auth = $this->prophesize(DatabaseAuth::class);
        //flashService
        $this->flashService = $this->prophesize(FlashService::class);

        $this->action = new SignUpAction(
            $this->renderer->reveal(),
            $this->userTable->reveal(),
            $this->router->reveal(),
            $this->auth->reveal(),
            $this->flashService->reveal()
        );
    }

    public function testGet()
    {
        call_user_func($this->action, $this->makeRequest());
        $this->renderer->render('@account/signup')->shouldHaveBeenCalled();
    }

    public function testPostNotPassword()
    {
        call_user_func($this->action, $this->makeRequest('/demo', [
            'username' => 'Jean marc',
            'email' => 'azeazeaze',
            'password' => '',
            'password' => ''
        ]));
        $this->renderer->render('@account/signup', Argument::that(function ($params) {
            $this->assertArrayHasKey('errors', $params);
            $this->assertEquals(['password_confirm','email', 'password'], array_keys($params['errors']));
            return true;
        }))->shouldHaveBeenCalled();
    }

    public function testPostInvalid()
    {
        call_user_func($this->action, $this->makeRequest('/demo', [
            'username' => 'Jean marc',
            'email' => 'azeazeaze',
            'password' => '0000',
            'password' => '000'
        ]));
        $this->renderer->render('@account/signup', Argument::that(function ($params) {
            $this->assertArrayHasKey('errors', $params);
            $this->assertEquals(['password_confirm','email', 'password'], array_keys($params['errors']));
            return true;
        }))->shouldHaveBeenCalled();
    }

    public function testPostValid()
    {
        $this->userTable->insert(Argument::that(function ($userParams) {
            $this->assertArraySubset([
                'username' => 'Jean marc',
                'email' => 'demo@demo.com'
            ], $userParams);
            $this->assertTrue(password_verify('0000', $userParams['password']));
            return true;
        }))->shouldBeCalled();
        $this->auth->setUser(Argument::that(function (User $user) {
            $this->assertEquals('Jean marc', $user->username);
            $this->assertEquals('demo@demo.com', $user->email);
            $this->assertEquals(3, $user->id);
            return true;
        }))->shouldBeCalled();
        $this->renderer->render()->shouldNotBeCalled();
        $this->flashService->success(Argument::type('string'))->shouldBeCalled();
        $response = call_user_func($this->action, $this->makeRequest('/demo', [
            'username' => 'Jean marc',
            'email' => 'demo@demo.com',
            'password' => '0000',
            'password_confirm' => '0000'
        ]));
        $this->assertRedirect($response, 'account');
    }
}
