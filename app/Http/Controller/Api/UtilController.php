<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace App\Http\Controller\Api;

use Swoft\Http\Message\Request;
use Swoft\Http\Message\Upload\UploadedFile;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\Middleware;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;
use App\Http\Middleware\AuthMiddleware;

/**
 * Class UtilController
 * @package App\Http\Controller
 * @Controller("api/util")
 */
class UtilController
{

    /**
     * @RequestMapping(route="uploadImg",method={RequestMethod::POST})
     * @Middleware(name=AuthMiddleware::class)
     */
    public function uploadImg(Request $request)
    {
        try {
            $files = $request->getUploadedFiles();

            /** @var UploadedFile $file */
            $file = $files['file'];
            if (!$file) throw new \Exception('FILE_DOES_NOT_EXIST');

            $size = $file->getSize();
            if ($size / 1024 / 1024 > 10) throw new \Exception('文件大小超过10M');

            [$dir, $baseDir] = $this->getBaseDirAndDir('img');

            $ext_name = substr($file->getClientFilename(), strrpos($file->getClientFilename(), '.'));
            $file_name = time() . rand(1, 999999);
            $path = $baseDir . '/' . $file_name . $ext_name;
            $file->moveTo($path);
            return apiSuccess([
                'src' => env('APP_HOST') . $dir . '/' . $file_name . $ext_name
            ]);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }

    /**
     * @RequestMapping(route="uploadFile",method={RequestMethod::POST})
     * @Middleware(name=AuthMiddleware::class)
     */
    public function uploadFile(Request $request)
    {
        try {
            $files = $request->getUploadedFiles();

            /** @var UploadedFile $file */
            $file = $files['file'];
            if (!$file) throw new \Exception('FILE_DOES_NOT_EXIST');

            $size = $file->getSize();
            if ($size / 1024 / 1024 > 10) throw new \Exception('文件大小超过10M');

            [$dir, $baseDir] = $this->getBaseDirAndDir('file');

            $ext_name = substr($file->getClientFilename(), strrpos($file->getClientFilename(), '.'));

            $file_name = time() . rand(1, 999999);
            $path = $baseDir . '/' . $file_name . $ext_name;
            $file->moveTo($path);
            return apiSuccess([
                'src' => env('APP_HOST') . $dir . '/' . $file_name . $ext_name
            ]);
        } catch (\Throwable $throwable) {
            return apiError($throwable->getCode(), $throwable->getMessage());
        }
    }

    private function getBaseDirAndDir($type = 'img')
    {
        $dir = '/upload/' . date('Ymd') . '/' . $type;

        $baseDir = alias('@base') . '/public' . $dir;

        if (!is_dir($baseDir)) {
            @mkdir($baseDir, 0777, true);
        }

        return [$dir, $baseDir];
    }
}
