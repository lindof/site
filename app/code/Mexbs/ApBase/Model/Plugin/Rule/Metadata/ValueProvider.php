<?php
namespace Mexbs\ApBase\Model\Plugin\Rule\Metadata;

class ValueProvider
{
    public function afterGetMetadataValues(
        \Magento\SalesRule\Model\Rule\Metadata\ValueProvider $subject,
        $metaDataValues
    ){
        $apSimpleActionOptions = [];

        if(class_exists("\Mexbs\AdditionalPromotions\Model\Rule\Action\Details\FirstNNextMAfterKPercentDiscount")){
            $apSimpleActionOptions =
                [
                    [
                        'label' => 'Discount steps: First N items, next M items, next K items ...',
                        'value' =>
                        [
                            [
                                'label' => __('Percent Discount: First N items with A% discount, next M items with B% ...'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\FirstNNextMAfterKPercentDiscount::SIMPLE_ACTION
                            ],
                            [
                                'label' => __('Fixed Discount: First N items with A$ discount, next M items with B$ ...'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\FirstNNextMAfterKFixedDiscount::SIMPLE_ACTION
                            ],
                            [
                                'label' => __('Fixed Price: First N items for A$, next M items for B$ ...'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\FirstNNextMAfterKFixedPriceDiscount::SIMPLE_ACTION
                            ]
                        ]
                    ],
                    [
                        'label' => 'Get Y$ for each X$ spent',
                        'value' =>
                        [
                            [
                                'label' => __('Get Y$ for each X$ spent on all items matching ...'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\GetYForEachXSpent::SIMPLE_ACTION
                            ],
                            [
                                'label' => __('Get Y$ for each X$ spent, on up to N items in cart matching ...'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\GetYForEachXSpentUpToN::SIMPLE_ACTION
                            ]
                        ]
                    ],
                    [
                        'label' => 'BOGO: Buy X get different Y',
                        'value' =>
                        [
                            [
                                'label' => __('Percent Discount: Buy X get N of different Y with Z% discount'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\BuyXGetNOfYPercentDiscount::SIMPLE_ACTION
                            ],
                            [
                                'label' => __('Fixed Discount: Buy X get N of different Y with Z$ discount'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\BuyXGetNOfYFixedDiscount::SIMPLE_ACTION
                            ],
                            [
                                'label' => __('Fixed Price: Buy X get N of different Y for Z$'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\BuyXGetNOfYFixedPriceDiscount::SIMPLE_ACTION
                            ],
                        ]
                    ],
                    [
                        'label' => 'Extended BOGO: Buy A + B + C + ... get different D',
                        'value' =>
                        [
                            [
                                'label' => __('Percent Discount: Buy A + B + C ... get N of different D with Z% discount'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\BuyABCGetNOfDPercentDiscount::SIMPLE_ACTION
                            ],
                            [
                                'label' => __('Fixed Discount: Buy A + B + C ... get N of different D with Z$ discount'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\BuyABCGetNOfDFixedDiscount::SIMPLE_ACTION
                            ],
                            [
                                'label' => __('Fixed Price: Buy A + B + C ... get N of different D for Z$'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\BuyABCGetNOfDFixedPriceDiscount::SIMPLE_ACTION
                            ],
                        ]
                    ],
                    [
                        'label' => 'Bundle: Buy A + B + C + D for ...',
                        'value' =>
                        [
                            [
                                'label' => __('Percent Discount: N items of type A + M items of type B + ..., with Z% discount'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\ProductsSetPercentDiscount::SIMPLE_ACTION
                            ],
                            [
                                'label' => __('Fixed Discount: N items of type A + M items of type B + ..., with Z$ discount'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\ProductsSetFixedDiscount::SIMPLE_ACTION
                            ],
                            [
                                'label' => __('Fixed Price: N items of type A + M items of type B + ..., for Z$'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\ProductsSetFixedPriceDiscount::SIMPLE_ACTION
                            ]
                        ]
                    ],
                    [
                        'label' => 'Category tier: Get each group of N items for ...',
                        'value' =>
                        [
                            [
                                'label' => __('Percent Discount: Group of N items with Z% discount'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\EachGroupOfNPercentDiscount::SIMPLE_ACTION
                            ],
                            [
                                'label' => __('Fixed Discount: Group of N items with Z$ discount'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\EachGroupOfNFixedDiscount::SIMPLE_ACTION
                            ],
                            [
                                'label' => __('Fixed Price: Group of N items for Z$'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\EachGroupOfNFixedPriceDiscount::SIMPLE_ACTION
                            ]
                        ]
                    ],
                    [
                        'label' => 'N + M / Each N:  N + M on items of same type, after M added to cart for full price',
                        'value' =>
                        [
                            [
                                'label' => __('Percent Discount: Buy N, get M subsequent items with Z% discount, after M added'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\GetEachNAfterMPercentDiscount::SIMPLE_ACTION
                            ],
                            [
                                'label' => __('Fixed Discount: Buy N, get M subsequent items with Z$ discount, after M added'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\GetEachNAfterMFixedDiscount::SIMPLE_ACTION
                            ],
                            [
                                'label' => __('Fixed Price: Buy N, get M subsequent items for Z$, after M added'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\GetEachNAfterMFixedPriceDiscount::SIMPLE_ACTION
                            ]
                        ]
                    ],
                    [
                        'label' => 'All after M added: Discount on all items of same type, after M added to cart for full price',
                        'value' =>
                        [
                            [
                                'label' => __('Percent Discount: Get all items with Z% discount, after M added'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\GetAllAfterMPercentDiscount::SIMPLE_ACTION
                            ],
                            [
                                'label' => __('Fixed Discount: Get all items with Z$ discount, after M added'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\GetAllAfterMFixedDiscount::SIMPLE_ACTION
                            ],
                            [
                                'label' => __('Fixed Price: Get all items for Z$, after M added'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\GetAllAfterMFixedPriceDiscount::SIMPLE_ACTION
                            ]
                        ]
                    ],
                    [
                        'label' => 'Cheapest / Most Expensive: Discount the Nth cheapest (or most expensive) item in cart',
                        'value' =>
                        [
                            [
                                'label' => __('Percent Discount: Get the nth cheapest item with Z% discount'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\CheapestPercentDiscount::SIMPLE_ACTION
                            ],
                            [
                                'label' => __('Fixed Discount: Get the nth cheapest item with Z$ discount'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\CheapestFixedDiscount::SIMPLE_ACTION
                            ],
                            [
                                'label' => __('Fixed Price: Get the nth cheapest item for Z$ discount'),
                                'value' =>  \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\CheapestFixedPriceDiscount::SIMPLE_ACTION
                            ]
                        ]
                    ]
                ];
        }else{
            if(class_exists("\Mexbs\FreeGift\Model\Rule\Action\Details\GetProductPercentDiscount")){
                $apSimpleActionOptions[] = [
                    'label' => 'Get free/discounted product(s)',
                    'value' =>
                    [
                        [
                            'label' => __('Percent Discount: Get free/discounted product(s) matching ...'),
                            'value' =>  \Mexbs\FreeGift\Model\Rule\Action\Details\GetProductPercentDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Discount: Get a fixed discount on product(s) matching ...'),
                            'value' =>  \Mexbs\FreeGift\Model\Rule\Action\Details\GetProductFixedDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Price: Get product(s) matching ... for a fixed price ...'),
                            'value' =>  \Mexbs\FreeGift\Model\Rule\Action\Details\GetProductFixedPriceDiscount::SIMPLE_ACTION
                        ]
                    ]
                ];
            }
            if(class_exists("\Mexbs\FreeGift\Model\Rule\Action\Details\GetProductForEachXSpentPercentDiscount")){
                $apSimpleActionOptions[] = [
                    'label' => 'Get free or discounted product(s) for each X$ spent',
                    'value' =>
                    [
                        [
                            'label' => __('Percent Discount: Get free or discounted product(s), for each X$ spent on all items matching ...'),
                            'value' =>  \Mexbs\FreeGift\Model\Rule\Action\Details\GetProductForEachXSpentPercentDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Discount: Get a fixed discount on product(s), for each X$ spent on all items matching ...'),
                            'value' =>  \Mexbs\FreeGift\Model\Rule\Action\Details\GetProductForEachXSpentFixedDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Price: Get a fixed price on product(s), for each X$ spent on all items matching ...'),
                            'value' =>  \Mexbs\FreeGift\Model\Rule\Action\Details\GetProductForEachXSpentFixedPriceDiscount::SIMPLE_ACTION
                        ]
                    ]
                ];
            }
            if(class_exists("\Mexbs\DiscountSteps\Model\Rule\Action\Details\FirstNNextMAfterKPercentDiscount")){
                $apSimpleActionOptions[] = [
                    'label' => 'Discount steps: First N items, next M items, next K items ...',
                    'value' =>
                    [
                        [
                            'label' => __('Percent Discount: First N items with A% discount, next M items with B% ...'),
                            'value' =>  \Mexbs\DiscountSteps\Model\Rule\Action\Details\FirstNNextMAfterKPercentDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Discount: First N items with A$ discount, next M items with B$ ...'),
                            'value' =>  \Mexbs\DiscountSteps\Model\Rule\Action\Details\FirstNNextMAfterKFixedDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Price: First N items for A$, next M items for B$ ...'),
                            'value' =>  \Mexbs\DiscountSteps\Model\Rule\Action\Details\FirstNNextMAfterKFixedPriceDiscount::SIMPLE_ACTION
                        ]
                    ]
                ];
            }
            if(class_exists("\Mexbs\YForEachXSpent\Model\Rule\Action\Details\GetYForEachXSpent")){
                $apSimpleActionOptions[] = [
                    'label' => 'Get Y$ for each X$ spent',
                    'value' =>
                    [
                        [
                            'label' => __('Get Y$ for each X$ spent on all items matching ...'),
                            'value' =>  \Mexbs\YForEachXSpent\Model\Rule\Action\Details\GetYForEachXSpent::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Get Y$ for each X$ spent, on up to N items in cart matching ...'),
                            'value' =>  \Mexbs\YForEachXSpent\Model\Rule\Action\Details\GetYForEachXSpentUpToN::SIMPLE_ACTION
                        ]
                    ]
                ];
            }
            if(class_exists("\Mexbs\FreeGift\Model\Rule\Action\Details\BuyXGetNOfYPercentDiscount")){
                $apSimpleActionOptions[] = [
                    'label' => 'BOGO: Buy X get different Y',
                    'value' =>
                    [
                        [
                            'label' => __('Percent Discount: Buy X get N of different Y with Z% discount'),
                            'value' =>  \Mexbs\FreeGift\Model\Rule\Action\Details\BuyXGetNOfYPercentDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Discount: Buy X get N of different Y with Z$ discount'),
                            'value' =>  \Mexbs\FreeGift\Model\Rule\Action\Details\BuyXGetNOfYFixedDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Price: Buy X get N of different Y for Z$'),
                            'value' =>  \Mexbs\FreeGift\Model\Rule\Action\Details\BuyXGetNOfYFixedPriceDiscount::SIMPLE_ACTION
                        ],
                    ]
                ];
            }
            if(class_exists("\Mexbs\Bogo\Model\Rule\Action\Details\BuyXGetNOfYPercentDiscount")){
                $apSimpleActionOptions[] = [
                    'label' => 'BOGO: Buy X get different Y',
                    'value' =>
                    [
                        [
                            'label' => __('Percent Discount: Buy X get N of different Y with Z% discount'),
                            'value' =>  \Mexbs\Bogo\Model\Rule\Action\Details\BuyXGetNOfYPercentDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Discount: Buy X get N of different Y with Z$ discount'),
                            'value' =>  \Mexbs\Bogo\Model\Rule\Action\Details\BuyXGetNOfYFixedDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Price: Buy X get N of different Y for Z$'),
                            'value' =>  \Mexbs\Bogo\Model\Rule\Action\Details\BuyXGetNOfYFixedPriceDiscount::SIMPLE_ACTION
                        ],
                    ]
                ];
            }
            if(class_exists("\Mexbs\ExtendedBogo\Model\Rule\Action\Details\BuyABCGetNOfDPercentDiscount")){
                $apSimpleActionOptions[] = [
                    'label' => 'BOGO: Buy X get different Y',
                    'value' =>
                    [
                        [
                            'label' => __('Percent Discount: Buy X get N of different Y with Z% discount'),
                            'value' =>  \Mexbs\ExtendedBogo\Model\Rule\Action\Details\BuyXGetNOfYPercentDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Discount: Buy X get N of different Y with Z$ discount'),
                            'value' =>  \Mexbs\ExtendedBogo\Model\Rule\Action\Details\BuyXGetNOfYFixedDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Price: Buy X get N of different Y for Z$'),
                            'value' =>  \Mexbs\ExtendedBogo\Model\Rule\Action\Details\BuyXGetNOfYFixedPriceDiscount::SIMPLE_ACTION
                        ],
                    ]
                ];
            }
            if(class_exists("\Mexbs\BundledDiscount\Model\Rule\Action\Details\ProductsSetPercentDiscount")){
                $apSimpleActionOptions[] = [
                    'label' => 'Bundle: Buy A + B + C + D for ...',
                    'value' =>
                    [
                        [
                            'label' => __('Percent Discount: N items of type A + M items of type B + ..., with Z% discount'),
                            'value' =>  \Mexbs\BundledDiscount\Model\Rule\Action\Details\ProductsSetPercentDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Discount: N items of type A + M items of type B + ..., with Z$ discount'),
                            'value' =>  \Mexbs\BundledDiscount\Model\Rule\Action\Details\ProductsSetFixedDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Price: N items of type A + M items of type B + ..., for Z$'),
                            'value' =>  \Mexbs\BundledDiscount\Model\Rule\Action\Details\ProductsSetFixedPriceDiscount::SIMPLE_ACTION
                        ]
                    ]
                ];
            }
            if(class_exists("\Mexbs\DynamicTier\Model\Rule\Action\Details\EachGroupOfNPercentDiscount")){
                $apSimpleActionOptions[] = [
                    'label' => 'Cross tier: Get each group of N items for ...',
                    'value' =>
                    [
                        [
                            'label' => __('Percent Discount: Group of N items with Z% discount'),
                            'value' =>  \Mexbs\DynamicTier\Model\Rule\Action\Details\EachGroupOfNPercentDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Discount: Group of N items with Z$ discount'),
                            'value' =>  \Mexbs\DynamicTier\Model\Rule\Action\Details\EachGroupOfNFixedDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Price: Group of N items for Z$'),
                            'value' =>  \Mexbs\DynamicTier\Model\Rule\Action\Details\EachGroupOfNFixedPriceDiscount::SIMPLE_ACTION
                        ]
                    ]
                ];
            }
            if(class_exists("\Mexbs\EachN\Model\Rule\Action\Details\GetEachNAfterMPercentDiscount")){
                $apSimpleActionOptions[] = [
                    'label' => 'N + M / Each N:  N + M on items of same type, after M added to cart for full price',
                    'value' =>
                    [
                        [
                            'label' => __('Percent Discount: Buy N, get M subsequent items with Z% discount, after M added'),
                            'value' =>  \Mexbs\EachN\Model\Rule\Action\Details\GetEachNAfterMPercentDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Discount: Buy N, get M subsequent items with Z$ discount, after M added'),
                            'value' =>  \Mexbs\EachN\Model\Rule\Action\Details\GetEachNAfterMFixedDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Price: Buy N, get M subsequent items for Z$, after M added'),
                            'value' =>  \Mexbs\EachN\Model\Rule\Action\Details\GetEachNAfterMFixedPriceDiscount::SIMPLE_ACTION
                        ]
                    ]
                ];
            }
            if(class_exists("\Mexbs\FreeGift\Model\Rule\Action\Details\GetEachNAfterMPercentDiscount")){
                $apSimpleActionOptions[] = [
                    'label' => 'N + M / Each N:  N + M on items of same type, after M added to cart for full price',
                    'value' =>
                    [
                        [
                            'label' => __('Percent Discount: Buy N, get M subsequent items with Z% discount, after M added'),
                            'value' =>  \Mexbs\FreeGift\Model\Rule\Action\Details\GetEachNAfterMPercentDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Discount: Buy N, get M subsequent items with Z$ discount, after M added'),
                            'value' =>  \Mexbs\FreeGift\Model\Rule\Action\Details\GetEachNAfterMFixedDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Price: Buy N, get M subsequent items for Z$, after M added'),
                            'value' =>  \Mexbs\FreeGift\Model\Rule\Action\Details\GetEachNAfterMFixedPriceDiscount::SIMPLE_ACTION
                        ]
                    ]
                ];
            }
            if(class_exists("\Mexbs\EachN\Model\Rule\Action\Details\GetAllAfterMPercentDiscount")){
                $apSimpleActionOptions[] = [
                    'label' => 'All after M added: Discount on all items of same type, after M added to cart for full price',
                    'value' =>
                    [
                        [
                            'label' => __('Percent Discount: Get all items with Z% discount, after M added'),
                            'value' =>  \Mexbs\EachN\Model\Rule\Action\Details\GetAllAfterMPercentDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Discount: Get all items with Z$ discount, after M added'),
                            'value' =>  \Mexbs\EachN\Model\Rule\Action\Details\GetAllAfterMFixedDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Price: Get all items for Z$, after M added'),
                            'value' =>  \Mexbs\EachN\Model\Rule\Action\Details\GetAllAfterMFixedPriceDiscount::SIMPLE_ACTION
                        ]
                    ]
                ];
            }
            if(class_exists("\Mexbs\Cheapest\Model\Rule\Action\Details\CheapestPercentDiscount")){
                $apSimpleActionOptions[] = [
                    'label' => 'Cheapest / Most Expensive: Discount the Nth cheapest (or most expensive) item in cart',
                    'value' =>
                    [
                        [
                            'label' => __('Percent Discount: Get the nth cheapest item with Z% discount'),
                            'value' =>  \Mexbs\Cheapest\Model\Rule\Action\Details\CheapestPercentDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Discount: Get the nth cheapest item with Z$ discount'),
                            'value' =>  \Mexbs\Cheapest\Model\Rule\Action\Details\CheapestFixedDiscount::SIMPLE_ACTION
                        ],
                        [
                            'label' => __('Fixed Price: Get the nth cheapest item for Z$ discount'),
                            'value' =>  \Mexbs\Cheapest\Model\Rule\Action\Details\CheapestFixedPriceDiscount::SIMPLE_ACTION
                        ]
                    ]
                ];
            }
        }




        if(isset($metaDataValues['actions']['children']['simple_action']['arguments']['data']['config']['options'])){
            if(!is_array($metaDataValues['actions']['children']['simple_action']['arguments']['data']['config']['options'])){
                $metaDataValues['actions']['children']['simple_action']['arguments']['data']['config']['options'] = [];
            }
            $metaDataValues['actions']['children']['simple_action']['arguments']['data']['config']['options'] =
                array_merge($metaDataValues['actions']['children']['simple_action']['arguments']['data']['config']['options'],
                    $apSimpleActionOptions);
        }

        return $metaDataValues;
    }
}