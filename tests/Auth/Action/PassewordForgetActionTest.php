<?php

namespace Tests\App\Auth\Action;

use App\Auth\Action\PasswordForgetAction;
use App\Auth\Mailer\PasswordResetMailer;
use App\Auth\User;
use App\Auth\UserTable;
use Framework\Database\NoRecordException;
use Framework\Renderer\RendererInterface;
use Framework\Session\FlashService;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Tests\ActionTestCase;

class PassewordForgetActionTest extends ActionTestCase
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
    private $mailer;

    /**
     * action
     *
     * @var PasswordForgetAction
     */
    private $action;

    protected function setUp(): void
    {
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->userTable = $this->prophesize(UserTable::class);
        $this->mailer = $this->prophesize(PasswordResetMailer::class);
        $this->action = new PasswordForgetAction(
            $this->renderer->reveal(),
            $this->userTable->reveal(),
            $this->mailer->reveal(),
            $this->prophesize(FlashService::class)->reveal()
        );
    }

    public function testEmailInvalid()
    {
        $request = $this->makeRequest('/demo', ['email' => 'azeraer']);
        $this->renderer
            ->render(Argument::type('string'), Argument::withEntry('errors', Argument::withKey('email')))
            ->shouldBeCalled()
            ->WillReturnArgument();
        $response = call_user_func($this->action, $request);
        $this->assertEquals('@auth/password', $response);
    }

    public function testEmailvalidDontExist()
    {
        $request = $this->makeRequest('/demo', ['email' => 'jogn@doe.fr']);
        $this->userTable->findBy('email', 'jogn@doe.fr')->willThrow(new NoRecordException());
        $this->renderer
            ->render(Argument::type('string'), Argument::withEntry('errors', Argument::withKey('email')))
            ->shouldBeCalled()
            ->WillReturnArgument();
        $response = call_user_func($this->action, $request);
        $this->assertEquals('@auth/password', $response);
    }

    public function testGoodEmail()
    {
        $user = new User;
        $user->id = 3;
        $user->email = 'john@doe.fr';
        $token = 'fake';
        $request = $this->makeRequest('/demo', ['email' => $user->email]);
        $this->userTable->findBy('email', 'john@doe.fr')->WillReturn($user);
        $this->userTable->resetPassword(3)->willReturn($token);
        $this->mailer->send($user->email, [
            'id' => $user->id,
            'token' => $token
        ])->shouldBeCalled();
        $this->renderer->render()->shouldNotBeCalled();
        $response = call_user_func($this->action, $request);
        $this->assertRedirect($response, '/demo');
    }
}
