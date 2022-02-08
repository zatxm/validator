<?php

namespace Zatxm\Validation;

use Exception;

class ValidationException extends Exception
{
    /**
     * The validator instance.
     *
     * @var Validator
     */
    public $validator;

    /**
     * The recommended response to send to the client.
     *
     * @var mixed
     */
    public $response;

    /**
     * Create a new exception instance.
     *
     * @param  Validator $validator
     * @param  mixed     $response
     * @return void
     */
    public function __construct($validator, $response = null)
    {
        parent::__construct('The given data failed to pass validation.');

        $this->response = $response;
        $this->validator = $validator;
    }

    /**
     * Get the underlying response instance.
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }
}
