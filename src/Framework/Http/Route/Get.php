<?php

namespace Echo\Framework\Http\Route;

use Echo\Framework\Http\Route;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Get extends Route {}
