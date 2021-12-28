<?php

namespace OmidZahed\LaravelRequestDocs;

use Illuminate\Support\Facades\Facade;

/**
 * @see \OmidZahed\LaravelRequestDocs\LaravelRequestDocs
 */
class LaravelRequestDocsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-request-docs';
    }
}
