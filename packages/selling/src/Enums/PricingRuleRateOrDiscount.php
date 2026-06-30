<?php

namespace JeffersonGoncalves\Erp\Selling\Enums;

enum PricingRuleRateOrDiscount: string
{
    case Rate = 'Rate';
    case DiscountPercentage = 'Discount Percentage';
    case DiscountAmount = 'Discount Amount';
}
