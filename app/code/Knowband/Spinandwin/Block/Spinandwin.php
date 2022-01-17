<?php
namespace Knowband\Spinandwin\Block;
 
class Spinandwin extends \Magento\Framework\View\Element\Template
{
    public function __construct(
            \Magento\Framework\View\Element\Template\Context $context,
            \Knowband\Spinandwin\Helper\Data $helper,
            \Knowband\Spinandwin\Model\ThemeSchedule $themeSchedule
            )
    {
        $this->sp_helper = $helper;
        $this->sp_themeSchedule = $themeSchedule;
        parent::__construct($context);
    }
    
    public function getSettings($key = 'knowband/spinandwin/settings')
    {
        return $this->sp_helper->getSavedSettings($key);
    }
    
    public function getMediaUrl()
    {
        return $this->sp_helper->getMediaUrl();
    }
    
    /*
     * Function to detect device
     */

    public function getDevice()
    {
        return $this->sp_helper->detectDevice();
    }

    /*
     * Function to check time interval
     */

    public function checkTimeInterval($display_settings)
    { 
        $current_client_time = strtotime(date('Y-m-d H:i:s'));
        if (isset($display_settings['fix_time']) && $display_settings['fix_time'] == 1) {
        $date_start = $display_settings['active_date'];
        $date_end = $display_settings['expire_date'];
            if ($current_client_time >= strtotime($date_start) && $current_client_time <= strtotime($date_end)) {
            } else {
                return false;
            }
        }
        return true;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cookieManager = $objectManager->get('Magento\Framework\Stdlib\CookieManagerInterface');

        $first_visit_cookie = $cookieManager->getCookie('first_visit_cookie');
        $visit_cookie = $cookieManager->getCookie('visit_cookie');

        //Set per visit frequency
//        if (isset($cookie_knowband_spinwheel_visitor) && $cookie_knowband_spinwheel_visitor == 1) {
//            if (isset($display_settings['display_frequency'])) {
//                if ($display_settings['display_frequency'] == 2) {  //hour
//                    if (!isset($this->context->cookie->visit_cookie)) {
//                        $this->context->cookie->__set('visit_cookie', 1);
//                        $this->context->cookie->setExpire(time() + 3600);
//                    }
//                } elseif ($display_settings['display_frequency'] == 3) {  //day
//                    if (!isset($this->context->cookie->visit_cookie)) {
//                        $this->context->cookie->__set('visit_cookie', 2);
//                        $this->context->cookie->setExpire(time() + 86400);
//                    }
//                } elseif ($display_settings['display_frequency'] == 4) {  //week
//                    if (!isset($this->context->cookie->visit_cookie)) {
//                        $this->context->cookie->__set('visit_cookie', 3);
//                        $this->context->cookie->setExpire(time() + 86400 * 7);
//                    }
//                } elseif ($display_settings['display_frequency'] == 5) {  //month
//                    if (!isset($this->context->cookie->visit_cookie)) {
//                        $this->context->cookie->__set('visit_cookie', 4);
//                        $this->context->cookie->setExpire(time() + 86400 * 30);
//                    }
//                } else {
//                    $every_visit_flag = false;
//                }
//            }
//        }
            if (isset($display_settings['display_frequency'])) {
            if (($display_settings['display_frequency'] == 1 
                && $current_client_time >= strtotime($date_start) 
                && $current_client_time <= strtotime($date_end))) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    /*
     * Function to check if to show on this page or not
     */

    public function checkShowOnPage($display_settings)
    {
        return true; // As there is no way to get list of all pages in Magento 2
    }

    /*
     * Function for getting the URL of Spin and Win media directory
     */

    public function getFrontMediaURL()
    {
        return $this->assetRepo->getUrlWithParams('Knowband_Spinandwin', $params).'/images/email/';
    }

    /*
     * Function for checking the country of a customer via IP
     */

    public function getCountryFromIp()
    {
        return $this->sp_helper->getCountryFromIp();
    }

    /*
     * Function for checking the country of a customer via IP
     */

    public function checkShowOnCountry($display_settings)
    {
        $country_iso = $this->getCountryFromIp();
        if ($display_settings['geo_location'] == '1') {
            return true;
        } else if ($display_settings['geo_location'] == '2') {
            if (in_array($country_iso, $display_settings['selected_country']) || in_array($country_iso, $display_settings['selected_country'])) {
                return true;
            }
        } else if ($display_settings['geo_location'] == '3') {
            if (!(in_array($country_iso, $display_settings['selected_country']) || in_array($country_iso, $display_settings['selected_country']))) {
                return true;
            }
        }

        return false;
    }
    
    public function getThemeImageFileName($theme){
        $theme_array = array(
            'football_generic_theme1' => 'Football_Generic_Theme1.gif',
            'football_generic_theme2' => 'Football_Generic_Theme2.jpg',
            'football_Argentina' => 'Spin-and-win-Football-Argentina-flag.gif',
            'football_Australia' => 'Spin-and-win-Football-Australia-flag.gif',
            'football_Belgium' => 'Spin-and-win-Football-Belgium-flag.gif',
            'football_Brazil' => 'Spin-and-win-Football-Brazil-flag.gif',
            'football_Costa-Rica' => 'Spin-and-win-Football-Costa-Rica-flag.gif',
            'football_Croatia' => 'Spin-and-win-Football-Croatia-flag.gif',
            'football_Denmark' => 'Spin-and-win-Football-Denmark-flag.gif',
            'football_England' => 'Spin-and-win-Football-England-flag.gif',
            'football_France' => 'Spin-and-win-Football-France-flag.gif',
            'football_Germany' => 'Spin-and-win-Football-Germany-flag.gif',
            'football_Iceland' => 'Spin-and-win-Football-Iceland-flag.gif',
            'football_Mexico' => 'Spin-and-win-Football-Mexico-flag.gif',
            'football_Peru' => 'Spin-and-win-Football-Peru-flag.gif',
            'football_Poland' => 'Spin-and-win-Football-Poland-flag.gif',
            'football_Portugal' => 'Spin-and-win-Football-Portugal-flag.gif',
            'football_Russian' => 'Spin-and-win-Football-Russian-flag.gif',
            'football_Spain' => 'Spin-and-win-Football-Spain-flag.gif',
            'football_Sweden' => 'Spin-and-win-Football-Sweden-flag.gif',
            'football_Switzerland' => 'Spin-and-win-Football-Switzerland-flag.gif'
            );
        if(isset($theme_array[$theme])){
            return $theme_array[$theme];
        }
        return 'Football_Generic_Theme1.gif';
    }
    
    /**
     * Get Look and feel settings
     * @return type
     */
    public function getLookandFeelSettings() {
        $settings = $this->getSettings();
        $lookandfeel_settings = $settings['lookandfeel_settings'];    
        if(isset($settings['theme_scheduling']["enable_theme_scheduling"]) && $settings['theme_scheduling']["enable_theme_scheduling"]) {
            $current_date = $this->sp_helper->getDate();
            $themeScheduleCollection = $this->sp_themeSchedule->getCollection()
                    ->addFieldToFilter('status', ['eq' => '1'])
                    ->addFieldToFilter('from_date', ['lteq' => $current_date])
                    ->addFieldToFilter('to_date', ['gteq' => $current_date]);

            if ($themeScheduleCollection->getSize()) {
                $themeScheduleModel = $themeScheduleCollection->getFirstItem();
                $themeSettings = $themeScheduleModel->getSettings();
                $lookandfeel_settings = json_decode($themeSettings, true);
            }
        }

        return $lookandfeel_settings;
    }
    
}
