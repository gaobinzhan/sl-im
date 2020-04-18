<?php declare(strict_types=1);
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace App\Http\Controller;


use Swoft\Http\Message\Request;
use Swoft\Http\Message\Response;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;
use function view;

/**
 * Class StaticController
 * @package App\Http\Controller
 * @Controller(prefix="static")
 */
class StaticController
{
    /**
     * @RequestMapping(route="login",method={RequestMethod::GET})
     */
    public function login()
    {
        return view('user/login');
    }

    /**
     * @RequestMapping(route="register",method={RequestMethod::GET})
     */
    public function register()
    {
        return view('user/register');
    }

    /**
     * @RequestMapping(route="createFriendGroup",method={RequestMethod::GET})
     */
    public function createFriendGroup(Request $request, Response $response)
    {
        return view('friend/createGroup');
    }

    /**
     * @RequestMapping(route="createGroup",method={RequestMethod::GET})
     */
    public function createGroup(Request $request, Response $response)
    {
        return view('group/create');
    }

    /**
     * @RequestMapping(route="findUser",method={RequestMethod::GET})
     */
    public function findUser(Request $request, Response $response)
    {
        return view('friend/find');
    }

    /**
     * @RequestMapping(route="findGroup",method={RequestMethod::GET})
     */
    public function findGroup(Request $request, Response $response)
    {
        return view('group/find');
    }

    /**
     * @RequestMapping(route="userInfo",method={RequestMethod::GET})
     */
    public function userInfo(Request $request, Response $response)
    {
        return view('user/info');
    }

    /**
     * @RequestMapping(route="friendInfo",method={RequestMethod::GET})
     */
    public function friendInfo(Request $request, Response $response)
    {
        return view('friend/info');
    }


    /**
     * @RequestMapping(route="groupInfo",method={RequestMethod::GET})
     */
    public function groupInfo(Request $request, Response $response)
    {
        return view('group/info');
    }

    /**
     * @RequestMapping(route="application",method={RequestMethod::GET})
     */
    public function msgBox()
    {
        return view('user/application');
    }

    /**
     * @RequestMapping(route="history",method={RequestMethod::GET})
     */
    public function chatLog()
    {
        return view('user/history');
    }

    /**
     * @RequestMapping(route="about",method={RequestMethod::GET})
     */
    public function about(){
        return view('chat/about');
    }

    /**
     * @RequestMapping(route="friendRoom",method={RequestMethod::GET})
     */
    public function friendRoom(){
        return view('friend/room');
    }
}
