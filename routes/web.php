<?php

use Illuminate\Support\Facades\Route;
use Saccharine\CPQ\Cpq;

// This will map to /cpq/selector/{ownerType}/{ownerId}
Cpq::routes(['prefix' => 'cpq']);