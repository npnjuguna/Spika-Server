<?php
namespace Spika\Controller;

use Silex\Application;
use Silex\WebTestCase;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

class MessageControllerTest extends WebTestCase
{

    const FIXTURE_TOKEN   = 'some_token';

    public function createApplication()
    {
        $pimple = new \Pimple;
        
        $dependencies = array(
            'beforeTokenChecker' => $pimple->protect(function () {
            }),
            'currentUser' => array("_id"=>"testid","token"=>"testtoken")
        );
        
        require realpath(__DIR__ . '/../../../') . '/etc/app.php';
 
        $spikadb = $this->getMock('\Spika\Db\DbInterface');
        
        $spikadb->expects($this->any())
            ->method('getEmoticons')
            ->will($this->returnValue(array('rows'=>array())));
                        
        $spikadb->expects($this->any())
            ->method('getEmoticonImage')
            ->will($this->returnValue('OK'));
                        
        $spikadb->expects($this->any())
            ->method('addNewUserMessage')
            ->will($this->returnValue('OK'));
                        
        $spikadb->expects($this->any())
            ->method('getUserMessages')
            ->will($this->returnValue('OK'));

        $spikadb->expects($this->any())
            ->method('addNewGroupMessage')
            ->will($this->returnValue('OK'));
                        
        $spikadb->expects($this->any())
            ->method('getGroupMessages')
            ->will($this->returnValue('OK'));

        $spikadb->expects($this->any())
            ->method('addNewComment')
            ->will($this->returnValue('OK'));


                        
        $app['spikadb'] = $spikadb;
        
        return $app;
    }

    /** @test */
    public function loadEmoticonsTest()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/Emoticons');
        assertRegExp('/rows/', $client->getResponse()->getContent());
    }
    
    /** @test */
    public function loadEmoticonTest()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/Emoticon/test');
        assertRegExp('/OK/', $client->getResponse()->getContent());
    }


    /** @test */
    public function sendTextUserMessage()
    {
    
        $client = $this->createClient();
        
        $sendParams = array(
            'to_user_id' => 'test',
            'body' => 'hi',
        );
        
        $crawler = $client->request(
            'POST',
            '/api/sendMessageToUser',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode($sendParams)
        );

        assertRegExp('/OK/', $client->getResponse()->getContent());
    }
    
    /** @test */
    public function getUserTextMessage()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/userMessages/test/20/0');

        assertRegExp('/OK/', $client->getResponse()->getContent());
    }
    
    /** @test */
    public function sendTextGroupMessage()
    {
    
        $client = $this->createClient();
        
        $sendParams = array(
            'to_group_id' => 'test',
            'body' => 'hi',
        );
        
        $crawler = $client->request(
            'POST',
            '/api/sendMessageToGroup',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode($sendParams)
        );

        assertRegExp('/OK/', $client->getResponse()->getContent());
    }
    
    
    /** @test */
    public function getGroupTextMessage()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/api/groupMessages/test/20/0');

        assertRegExp('/OK/', $client->getResponse()->getContent());
    }
    
    /** @test */
    public function sendCommentTest()
    {
        
        $client = $this->createClient();
        
        $sendParams = array(
            'message_id' => 'test',
            'comment' => 'hi',
        );
        
        $crawler = $client->request(
            'POST',
            '/api/sendComment',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode($sendParams)
        );


        assertRegExp('/OK/', $client->getResponse()->getContent());
    }
}
