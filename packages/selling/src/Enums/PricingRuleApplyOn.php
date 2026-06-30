<?php

namespace JeffersonGoncalves\Erp\Selling\Enums;

enum PricingRuleApplyOn: string
{
    case Item = 'Item';
    case ItemGroup = 'Item Group';
    case Brand = 'Brand';
}
