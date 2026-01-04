<?php
namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class GenericException extends HttpException
{
    /**
     * @param string|null     $message  The internal exception message
     * @param \Throwable|null $previous The previous exception
     * @param int             $code     The internal exception code
     */
    public function __construct(?string $message = '', $codeHttp = 404,\Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($codeHttp, $message, $previous, $headers, $code);
    }
}