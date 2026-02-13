<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\Http\RequestInterface;
use CodeIgniter\Http\ResponseInterface;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!auth()->loggedIn()) {
            return redirect()->route('user.login.index');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        if(count($request->getUri()->getSegments()) > 0) {
            if($request->getUri()->getSegments()[0] == 'home') {

            } else {
                if(auth()->user()->inGroup('admin') && $request->getUri()->getSegments()[0] == "users") {
                } else if(!auth()->user()->inGroup('superadmin')) {
                    $response->setBody(view('404'));    
                }
            }
        }
       
        // Do something after request if needed        
        // if (! auth()->user()->can('users.create')) {
        //     $response->setBody(view('404'));
        // }

    }
}
