<?php

class Context extends ContextCore
{
   
    /**
     * Returns the computing precision according to the current currency
     *
     * @return int
     */
    public function getComputingPrecision()
    {
        return 2;
    }
}
