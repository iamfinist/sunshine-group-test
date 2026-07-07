<?php

use Phalcon\Http\Request;

interface GridServiceInterface
{
    public function getFilter(Request $request): array;
    public function getLimit(Request $request): int;
    public function getOffset(Request $request): int;
}