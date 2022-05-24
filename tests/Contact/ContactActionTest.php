<?php

namespace Tests\App\Contact;

use App\Contact\ContactAction;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Tests\ActionTestCase;

class ContactActionTest extends ActionTestCase
{
    /**
     * renderer
     *
     * @var RendererInterface|MockObject
     */
    private $renderer;

    /**
     * contact
     *
     * @var ContactAction|MockObject
     */
    private $action;

    /**
     *
     *
     * @var FlashService|MockObject
     */
    private $flash;

    /**
     *
     *
     * @var MailerInterface|MockObject
     */
    private $mailer;

    private $to = 'contact@demo.com';


    protected function setUp(): void
    {
        $this->renderer = $this->getMockBuilder(RendererInterface::class)->getMock();
        $this->flash = $this->getMockBuilder(FlashService::class)->disableOriginalConstructor()->getMock();
        $this->mailer = $this->getMockBuilder(MailerInterface::class)->disableOriginalConstructor()->getMock();
        $this->action  = new ContactAction($this->to, $this->renderer, $this->flash, $this->mailer);
    }

    public function testGet()
    {
        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with('@contact/contact')
            ->WillReturn('');
        call_user_func($this->action, $this->makeRequest('/contact'));
    }

    public function testPostInvalid()
    {
        $request = $this->makeRequest('/contact', [
            'name' => 'Jean Marc',
            'email' => 'zretfsf',
            'content' => 'azererzearf'
        ]);

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with(
                '@contact/contact',
                $this->callback(function ($params) {
                    $this->assertArrayHasKey('errors', $params);
                    $this->assertArrayHasKey('email', $params['errors']);
                    return true;
                })
            )
            ->WillReturn('');
        $this->flash->expects($this->once())->method('error');
        call_user_func($this->action, $request);
    }

    public function testPostValid()
    {
        $request = $this->makeRequest('/contact', [
            'name' => 'Jean Marc',
            'email' => 'marc@demo.com',
            'content' => 'Lorem lorem lorem lorem lorem lorem'
        ]);
        $this->flash->expects($this->once())->method('success');
        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Email $message) {
                $this->assertEquals($this->to, $message->getTo()[0]->getAddress());
                $this->assertEquals('marc@demo.com', $message->getFrom()[0]->getAddress());
                return true;
            }));
            $this->renderer->expects($this->any())
                    ->method('render')
                    ->willReturn('texte', 'htmle');
        $response = call_user_func($this->action, $request);
        $this->assertInstanceOf(RedirectResponse::class, $response);
    }
}
