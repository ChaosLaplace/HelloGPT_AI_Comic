<?php

namespace App\Http\Controllers;

// An example of using php-webdriver.
// Do not forget to run composer install before and also have Selenium server started and listening on port 4444.
// namespace Facebook\WebDriver;
// require_once('../vendor/autoload.php');

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

use hg\apidoc\annotation as Apidoc;

use Illuminate\Http\Request;
use Throwable;

#[Apidoc\Title("Selenium")]
class SeleniumController extends Controller
{
    #[
        Apidoc\Title("伊莉討論區"),
        Apidoc\Tag("模擬用戶發文"),
        Apidoc\Author("Ernest"),
        Apidoc\Method("POST"),
        Apidoc\Group("bese"),
        Apidoc\Url("api/user/register"),
        Apidoc\Query(name: "name", type: "string", require: true, desc: "姓名"),
        Apidoc\Query(name: "email", type: "string", require: true, desc: "信箱"),
        Apidoc\Query(name: "username", type: "string", require: true, desc: "用戶帳號"),
        Apidoc\Query(name: "password", type: "string", require: true, desc: "用戶密碼"),
        Apidoc\ResponseSuccess(name: "code", type: "int", desc: "成功狀態碼 0", default: 0, require: true),
        Apidoc\ResponseSuccess(name: "state", type: "int", desc: "1 為成功", default: 1, require: true),
        Apidoc\ResponseSuccess(name: "msg", type: "string", desc: "返回消息", require: true),
        Apidoc\ResponseSuccess(name: "data", type: "array", desc: "返回資料", require: true),
        Apidoc\ResponseError(name: "code", type: "int", desc: "失敗狀態碼 -1", default: -1, require: true),
        Apidoc\ResponseError(name: "state", type: "int", desc: "0 為失敗", default: 0, require: true),
        Apidoc\ResponseError(name: "msg", type: "string", desc: "返回消息", require: true),
        Apidoc\ResponseError(name: "data", type: "array", desc: "返回資料", require: true),
    ]
    public function Eyny(Request $request) {
        try {
            // this is the default
            $host = 'http://localhost:4444/wd/hub';
            // Set Chrome options
            $options = new ChromeOptions();
            // Enable headless mode and disable GPU
            $options->addArguments(['--headless', '--disable-gpu']);
            // Set desired capabilities
            $capabilities = DesiredCapabilities::chrome();
            $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
            // Start the WebDriver
            $driver = RemoteWebDriver::create($host, $capabilities);
            $driver->get('https://www.youtube.com/channel/UC2fskEap4fde27rGId76Hig');
            // print the pagesource of the current page
            $html_selenium = $driver->getPageSource();
            self::infoLog($html_selenium);
            
            $driver->quit();
            return self::responseSuccess([]);
            
        } catch (Throwable $e) {
            return self::errorLog($e);
        }
        return self::responseFail();
    }
}
