<?php

namespace Echo\Framework\Routing\Route;

use Echo\Framework\Routing\Route;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Head extends Route {}
