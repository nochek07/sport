<?php

namespace App\Utils\Property;

interface OutDataInterface
{
    /**
     * Add in array OutData
     *
     * @param array $data
     *
     * @return mixed
     */
    public function addOutData(array $data);

    /**
     * Add in array InData
     *
     * @param array $data
     *
     * @return mixed
     */
    public function addInData(array $data);
}