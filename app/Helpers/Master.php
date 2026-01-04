<?php

use App\Exceptions\GenericException;
use Jenssegers\Optimus\Optimus;

if (! function_exists('optimus')) {
    /**
     * Optimus helper
     *
     * @return Optimus
     */
    function optimus(): Optimus
    {
        $prime      = 659721767;
        $inverse    = 1044382103;
        $random     = 619970903;
        try {
            return new Optimus($prime, $inverse, $random);
        } catch (Throwable $e) {
            return new Optimus(659721767, 1044382103, 619970903);
        }
    }
}

if (! function_exists('decodeId')) {
    /**
     * Optimus helper
     *
     * @return int
     */
    function decodeId($id): int
    {
        try {
            if (! is_numeric($id)) {
                throw new GenericException("Invalid parameter ".json_encode($id), 422);
            }
            $id = optimus()->decode((int) $id);
        } catch (\Throwable $th) {
            throw new GenericException("Parámetro ".json_encode($id)." inválido", 422);
        }
        return $id;
    }
}
if (! function_exists('encodeId')) {
    /**
     * Optimus helper
     *
     * @return int
     */
    function encodeId($id): int
    {
        try {
            if (! is_numeric($id)) {
                throw new GenericException("Invalid parameter ".json_encode($id), 422);
            }
            $id = optimus()->encode((int) $id);
        } catch (\Throwable $th) {
            throw new GenericException("Parámetro ".$id." inválido", 422);
        }
        return $id;
    }
}
