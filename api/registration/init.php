<?php
namespace NinjasCL;
/*
 * Transbank One Click Api Rest.
 *
 * Copyright (c) 2017 Camilo Castro <camilo@ninjas.cl>
 * https://github.com/NinjasCL/oneclick-rest-api
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
define('kNJSAccessEnabled', true);

require_once __DIR__ . '/../../includes/app.php';

$user = Request::input('user');
$email = Request::input('email');
$url = Request::input('url');
$auth = Header::auth();

$params = [
    'user' => $user,
    'email' => $email,
    'url' => $url
];

$response = Errors::badRequest($params);

if(!Helpers::authIsValid($auth))
{
    $response = Errors::unauthorized($params);
    Response::render($response);
}


if(
   Helpers::stringIsValid($user) &&
   Helpers::stringIsValid($email) &&
   Helpers::stringIsValid($url)
   )
{
    try
    {
        $result =  OneClick::instance()->initInscription($user, $email, $url);
        $response = Response::new($params);
        $response->data->session = (string) $result->token;
        $response->data->url = (string) $result->urlWebpay;

        $response->status = Status::ok();
    } 
    catch (Exception $e)
    {
        $response = Errors::internal($e);
    }
}

Response::render($response);

