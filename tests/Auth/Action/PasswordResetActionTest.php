<?php

namespace Tests\App\Auth\Action;

use App\Auth\Action\PasswordForgetAction;
use App\Auth\Action\PasswordResetAction;
use App\Auth\Mailer\PasswordResetMailer;
use App\Auth\User;
use App\Auth\UserTable;
use DateInterval;
use DateTime;
use Framework\Database\NoRecordException;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Tests\ActionTestCase;

class PasswordResetActionTest extends ActionTestCase
{
    use ProphecyTrait;

    /**
     * renderer
     *
     * @var ObjectProphecy
     */
    private $renderer;

    /**
     * userTable
     *
     * @var ObjectProphecy
     */
    private $userTable;

    /**
     * mailer
     *
     * @var ObjectProphecy
     */
    private $router;

    /**
     * action
     *
     * @var PasswordResetAction
     */
    private $action;

    protected function setUp(): void
    {
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->userTable = $this->prophesize(UserTable::class);
        $this->router = $this->prophesize(Router::class);
        $this->router->generateUri(Argument::cetera())->willReturnArgument();
        $this->renderer->render(Argument::cetera())->willReturnArgument();
        $this->action = new PasswordResetAction(
            $this->renderer->reveal(),
            $this->userTable->reveal(),
            $this->prophesize(FlashService::class)->reveal(),
            $this->router->reveal()
        );
    }

    private function makeUser()
    {
        $user = new User;
        $user->setId(3);
        $user->setPasswordReset('fake');
        $user->setPasswordResetAT(new DateTime());
        return $user;
    }

    public function testWithBadToken()
    {
        $user = $this->makeUser();
        $request = $this->makeRequest('/da')
                ->withAttribute('id', $user->id)
                ->withAttribute('token', $user->getPasswordReset() . 'aerr');
        $this->userTable->find($user->id)->willReturn($user);
        $response = call_user_func($this->action, $request);
        $this->assertRedirect($response, 'auth.password');
    }

    public function testWithExpiredToken()
    {
        $user = $this->makeUser();
        $user->setPasswordResetAT((new DateTime())->sub(new DateInterval('PT15M')));
        $request = $this->makeRequest('/da')
                ->withAttribute('id', $user->id)
                ->withAttribute('token', $user->getPasswordReset());
        $this->userTable->find($user->id)->willReturn($user);
        $response = call_user_func($this->action, $request);
        $this->assertRedirect($response, 'auth.password');
    }
    public function testWithValidToken()
    {
        $user = $this->makeUser();
        $user->setPasswordResetAT((new DateTime())->sub(new DateInterval('PT5M')));
        $request = $this->makeRequest('/da')
                ->withAttribute('id', $user->id)
                ->withAttribute('token', $user->getPasswordReset());
        $this->userTable->find($user->id)->willReturn($user);
        $response = call_user_func($this->action, $request);
        $this->assertEquals($response, '@auth/reset');
    }

    public function testPostWithBadPassword()
    {
        $user = $this->makeUser();
        $user->setPasswordResetAT((new DateTime())->sub(new DateInterval('PT5M')));
        $request = $this->makeRequest('/da', ['password' => 'azer', 'password_confirm' => 'azerfsd'])
                ->withAttribute('id', $user->id)
                ->withAttribute('token', $user->getPasswordReset());
        $this->userTable->find($user->id)->willReturn($user);
        $this->renderer
                ->render(Argument::type('string'), Argument::withKey('errors'))
                ->shouldBeCalled()
                ->willReturnArgument();
        $response = call_user_func($this->action, $request);
        $this->assertEquals($response, '@auth/reset');
    }

    public function testPostWithGoodPassword()
    {
        $user = $this->makeUser();
        $user->setPasswordResetAT((new DateTime())->sub(new DateInterval('PT5M')));
        $request = $this->makeRequest('/da', ['password' => 'azer', 'password_confirm' => 'azer'])
                ->withAttribute('id', $user->id)
                ->withAttribute('token', $user->getPasswordReset());
        $this->userTable->find($user->id)->willReturn($user);
        $this->userTable->updatePassword($user->getId(), 'azer');
        $response = call_user_func($this->action, $request);
        $this->assertRedirect($response, 'auth.login');
    }
}
