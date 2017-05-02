<?php

/*
 * This file is part of the Koded package.
 *
 * (c) Mihail Binev <mihail@kodeart.com>
 *
 * Please view the LICENSE distributed with this source code
 * for the full copyright and license information.
 *
 */

namespace Koded\Exceptions;

use Exception;
use Koded\Stdlib\Interfaces\Data;
use RuntimeException;

class KodedException extends RuntimeException
{

    /**
     * @var array A map of exception code => message
     */
    protected $messages = [];

    /**
     * KodedException constructor.
     *
     * @param int $code As defined in the child classes
     * @param array $arguments [optional]
     * @param Exception $previous [optional]
     */
    public function __construct(int $code, array $arguments = [], Exception $previous = null)
    {
        $message = strtr($this->messages[$code] ?? $this->message, $arguments);
        parent::__construct($message, $code, $previous);
    }
}

class ReadOnlyException extends KodedException
{

    protected $messages = [
        Data::E_CLONING_DISALLOWED => 'Cloning the :class instance is not allowed',
        Data::E_READONLY_INSTANCE => ':class instance is read-only',
    ];
}