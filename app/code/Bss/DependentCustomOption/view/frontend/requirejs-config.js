/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_DependentCustomOption
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
var config = {
    map: {
        '*': {
            bss_dco_control: 'Bss_DependentCustomOption/js/dco-control',
            bss_dco_option: 'Bss_DependentCustomOption/js/dco-option'
        }
    },
	config: {
		mixins: {
			'Magento_Catalog/js/price-options': {
				'Bss_DependentCustomOption/js/price-options-mixin': true
			}
		}
	}
};
