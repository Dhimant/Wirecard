<?php
namespace Czar\Wirecard\Api;
 
interface BinInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    public function number($number);
}