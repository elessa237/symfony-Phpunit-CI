<?php


namespace App\Tests\Security;


use App\Entity\User;
use App\Security\GithubUserProvider;
use GuzzleHttp\Client;
use JMS\Serializer\Exception\LogicException;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @author Elessa Maxime <elessamaxime@icloud.com>
 * @package App\Tests\Security
 */
class GithubUserProviderTest extends TestCase
{
    /**
     * @var MockObject|Client
     */
    private $client;

    /**
     * @var MockObject|SerializerInterface
     */
    private  $serializer;

    /**
     * @var MockObject|ResponseInterface
     */
    private $response;

    /**
     * @var StreamInterface|MockObject
     */
    private $streamResponse;

    public function setUp(): void
    {
        $this->client = $this->getMockBuilder("GuzzleHttp\Client")
            ->disableOriginalConstructor()
            ->getMock();
        $this->serializer = $this->getMockBuilder("JMS\Serializer\SerializerInterface")
            ->getMock();
        $this->response = $this->getMockBuilder("Psr\Http\Message\ResponseInterface")
            ->getMock();

        $this->client->method("get")->willReturn($this->response);
        $this->streamResponse = $this->getMockBuilder("Psr\Http\Message\StreamInterface")
            ->disableOriginalConstructor()
            ->getMock();
        $this->response->method("getBody")->willReturn($this->streamResponse);
        $this->streamResponse->method("getContents")->willReturn("foo");
        $this->client = $this->getMockBuilder("GuzzleHttp\Client")
            ->disableOriginalConstructor()
            ->getMock();
        $this->serializer = $this->getMockBuilder("JMS\Serializer\SerializerInterface")
            ->getMock();
        $this->response = $this->getMockBuilder("Psr\Http\Message\ResponseInterface")
            ->getMock();

        $this->client->method("get")->willReturn($this->response);
        $this->streamResponse = $this->getMockBuilder("Psr\Http\Message\StreamInterface")
            ->disableOriginalConstructor()
            ->getMock();
        $this->response->method("getBody")->willReturn($this->streamResponse);
        $this->streamResponse->method("getContents")->willReturn("foo");
    }

    public function tearDown(): void
    {
        $this->serializer = null;
        $this->response = null;
        $this->streamResponse = null;
        $this->client = null;
    }

    public function testLoadUserByUsernameReturnUserData()
    {
        $userData = [
            "login" => "login",
            "name" => "name",
            "email" => "email@email",
            "avatar_url" => "avatar_url",
            "html_url" => "html_url"
        ];
        $this->serializer->expects($this->once())
        ->method('deserialize')->willReturn($userData);

        $githubUserProvider = new GithubUserProvider($this->client, $this->serializer);

        $user = $githubUserProvider->loadUserByUsername("xxx-xx-x");
        $expectedUser = new User(
            $userData["login"],
            $userData["name"],
            $userData["email"],
            $userData["avatar_url"],
            $userData["html_url"],
        );
        $this->assertEquals($expectedUser, $user);
        $this->assertEquals("App\Entity\User", get_class($user));
    }

    public function testLoadUserByUsernameReturnNull(){
        $this->serializer->expects($this->once())
            ->method('deserialize')->willReturn(null);

        $githubUserProvider = new GithubUserProvider($this->client, $this->serializer);
        $this->expectException('LogicException');
        $user = $githubUserProvider->loadUserByUsername("xxx-xx-x");
    }
}