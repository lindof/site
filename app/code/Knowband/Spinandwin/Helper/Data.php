<?php

/**
 * Knowband_Spinandwin
 *
 * @category    Knowband
 * @package     Knowband_Spinandwin
 * @author      Knowband Team <support@knowband.com.com>
 * @copyright   Knowband (http://wwww.knowband.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Knowband\Spinandwin\Helper;
//use Detection\MobileDetect;
use Ctct\ConstantContact;
use Ctct\Exceptions\CtctException;
use Ctct\Components\Contacts\Contact;
use Mobile_Detect;
use DrewM\MailChimp\MailChimp;
//use IpLocation_Ip;
//use IpLocation_Service_GeoIp;
use GeoIp2\Database\Reader;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $sp_storeManager;
    protected $sp_scopeConfig;
    protected $sp_request;
    protected $sp_state;
    protected $inlineTranslation;
    protected $sp_transportBuilder;
    protected $rulesFactory;
    protected $sp_customerGroup;
    protected $sp_objectManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $state,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Knowband\Spinandwin\Model\Coupons $couponModel,
        \Knowband\Spinandwin\Model\SpinUserData $spinuserdata,
        \Knowband\Spinandwin\Model\Users $userModel,
        \Knowband\Spinandwin\Model\Email $emailModel,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\App\ResourceConnection $resource
    )
    {
        $this->sp_storeManager = $storeManager;
        $this->sp_scopeConfig = $context->getScopeConfig();
        $this->sp_request = $context->getRequest();
        $this->sp_state = $state;
        $this->rulesFactory = $ruleFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->sp_transportBuilder = $transportBuilder;
        $this->sp_customerGroup = $customerGroup;   
        $this->sp_objectManager = $objectManager;
        $this->sp_emailModel = $emailModel;
        $this->sp_couponModel = $couponModel;
        $this->sp_spinUserdata = $spinuserdata;
        $this->sp_userModel = $userModel;
        $this->_blockFactory = $blockFactory;
        $this->_priceHelper = $priceHelper;
        $this->date = $date;
        $this->assetRepo = $assetRepo;
        $this->_resource = $resource;
        parent::__construct($context);
    }
    
    public function getDate() {
        return $this->date->date('Y-m-d H:i:s');
    }  
    

    public function createCartRule($rand_code, $expiration_time, $coupon_type, $discount_amount, $allow_free_shipping, $stop_further_discount)
    {
        try 
        {
            $current_time = time() - 5*60*60;

            $from_date = date('Y-m-d',$current_time);
//            $from_date = date('Y-m-d');
            if ($expiration_time > 1) {
                $to_date = date('Y-m-d', strtotime($from_date . ' + ' . $expiration_time . ' days'));
            } else {
                $to_date = date('Y-m-d', strtotime($from_date . ' + 1 day'));
            }
            if ($coupon_type == 'F') {
                $simple_action = 'cart_fixed';
            } else {
                $simple_action = 'by_percent';
            }
            
            if ($allow_free_shipping) {
                $free_shipping = '1'; //set to 1 for Free shipping
            } else {
                $free_shipping = '0';
            }
            
            if ($stop_further_discount) {
                $further_discount = '1'; //set to 1 for stopping fither discount
            } else {
                $further_discount = '0';
            }
            $website_id = $this->sp_storeManager->getStore()->getWebsiteId();
            $customer_groups = $this->getCustomerGroups(); //get all customer groups
            $shoppingCartPriceRule = $this->rulesFactory->create();
            
            $shoppingCartPriceRule->setName($rand_code)
                ->setDescription('Coupon created by Spin and Win Extension')
                ->setFromDate($from_date)
                ->setToDate($to_date)
                ->setUsesPerCustomer('1')
                ->setUsesPerCoupon('1')
                ->setCustomerGroupIds($customer_groups)
                ->setIsActive('1')
                ->setStopRulesProcessing($further_discount)
                ->setIsAdvanced('1')
                ->setProductIds(NULL)
                ->setSortOrder('1')
                ->setSimpleAction($simple_action)
                ->setDiscountAmount($discount_amount)
                ->setDiscountQty(NULL)
                ->setDiscountStep('0')
                ->setWebsiteIds(array($website_id))
                ->setSimpleFreeShipping($free_shipping)
                ->setApplyToShipping('0')
                ->setTimesUsed('0')
                ->setIsRss('0')
                ->setActionsHintLabel('')
                ->setCouponType('2')
                ->setCouponCode($rand_code)
                ->setUsesPerCoupon('1');

            $shoppingCartPriceRule->save();
            return true;
        } catch (\Exception $e) {
            print_r($e->getMessage());
            die;
        }
    }
    
    /*
     * Function to generate a random code for Spin Wheel Coupon
     */
    public function generateRandomCouponCode()
    {
        $length = 12;
        $code = '';
        $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ0123456789';
        $maxlength = strlen($chars);
        if ($length > $maxlength) {
            $length = $maxlength;
        }

        $i = 0;
        while ($i < $length) {
            $char = substr($chars, random_int(0, $maxlength - 1), 1);
            if (!strstr($code, $char)) {
                $code .= $char;
                $i++;
            }
        }

        $code = strtoupper($code);
        return $code;
    }
    
    
    /*
     * Function to getting all the customer groups in the system.
     */
    public function getCustomerGroups()
    {
        $customerGroup = array();
        $customerGroups = $this->sp_customerGroup->toOptionHash();
        foreach (array_keys($customerGroups) as $key) {
            $customerGroup[] = $key;
        }

        return $customerGroup;
    }
    
    public function getMediaUrl()
    {
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance(); //instance of\Magento\Framework\App\ObjectManager
        $storeManager = $_objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
        $currentStore = $storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl;
    }
    
    public function getSavedSettings($key = 'knowband/spinandwin/settings', $scope = "default", $scope_id = 0)
    {
        if ($this->sp_request->getParam('store')) {
            $scope_id = $this->sp_storeManager->getStore($this->sp_request->getParam('store'))->getId();
            $scope = "stores";
        } elseif ($this->sp_request->getParam('website')) {
            $scope_id = $this->sp_storeManager->getWebsite($this->sp_request->getParam('website'))->getId();
            $scope = "websites";
        } elseif ($this->sp_request->getParam('group')) {
            $scope_id = $this->sp_storeManager->getGroup($this->sp_request->getParam('group'))->getWebsite()->getId();
            $scope = "groups";
        } else {
            $scope = "default";
            $scope_id = 0;
        }
        $area = $this->sp_state->getAreaCode();
        if ($area == 'frontend') {
            $settings_json = $this->sp_scopeConfig->getValue($key, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        } else {
            $settings_json = $this->sp_scopeConfig->getValue($key, $scope, $scope_id);
        }
        $settings_array = json_decode($settings_json, true);
        if ($settings_array === false) {
            return $this->getDefaultSettings();
        } else {
            return $settings_array;
        }
    }
    
    public function getDefaultSettings()
    {
        return [
            "enable" => 1,
            "pull_out" => 1,
            "display_interval" => 1
            
        ];
    }
    
    public function getStoreIdDetails() {
        if ($this->sp_request->getParam('store')) {
            $storeId = $this->sp_storeManager->getStore($this->sp_request->getParam('store'))->getId();
            $websiteId = 0;
            $scope = "stores";
        } elseif ($this->sp_request->getParam('website')) {
            $websiteId = $this->sp_storeManager->getWebsite($this->sp_request->getParam('website'))->getId();
            $storeId = 0;
            $scope = "websites";
        } elseif ($this->sp_request->getParam('group')) {
            $websiteId = $this->sp_storeManager->getGroup($this->sp_request->getParam('group'))->getWebsite()->getId();
            $storeId = 0;
            $scope = "groups";
        } else {
            $scope = "default";
            $websiteId = 0;
            $storeId = 0;
        }
        return array($storeId, $websiteId, $scope);
    }
    
    /*
     * Function for getting the data of the email template
     */

    public function getEmailTemplateData($t_name)
    {
        if ($this->sp_request->getParam('store')) {
            $storeId = $this->sp_storeManager->getStore($this->sp_request->getParam('store'))->getId();
            $websiteId = 0;
            $scope = "stores";
        } elseif ($this->sp_request->getParam('website')) {
            $websiteId = $this->sp_storeManager->getWebsite($this->sp_request->getParam('website'))->getId();
            $storeId = 0;
            $scope = "websites";
        } elseif ($this->sp_request->getParam('group')) {
            $websiteId = $this->sp_storeManager->getGroup($this->sp_request->getParam('group'))->getWebsite()->getId();
            $storeId = 0;
            $scope = "groups";
        } else {
            $scope = "default";
            $websiteId = 0;
            $storeId = 0;
        }
        $emailModel = $this->sp_emailModel->getCollection()
                ->addFieldToFilter('template_name', $t_name)
                ->addFieldToFilter('store_id', $storeId)
                ->addFieldToFilter('website_id', $websiteId);
        $template_data = $emailModel->getData();

        if (!isset($template_data[0]['template_subject']) || $template_data[0]['template_subject'] == '') {
            $template_data['template_subject'] = 'Congratulations! Redeem Your Exclusive Coupon on Next Purchase';
        } else {
            $template_data['template_subject'] = $template_data[0]['template_subject'];
        }

        if (!isset($template_data[0]['template_id']) || $template_data[0]['template_id'] == '') {
            $template_data['template_id'] = 0;
        } else {
            $template_data['template_id'] = $template_data[0]['template_id'];
        }

        if (!isset($template_data[0]['template_content']) || $template_data[0]['template_content'] == '') {
            $template_data['template_content'] = $this->getDefaultHTMLContents($t_name);
        } else {
            $template_data['template_content'] = $template_data[0]['template_content'];
        }

        return $template_data;
    }
    
    /*
     * Function for getting the default HTML contents of the email templates
     */

    public function getDefaultHTMLContents($t_name)
    {
        $email_html = $this->_blockFactory->createBlock('Knowband\Spinandwin\Block\Adminhtml\Reader')
                ->setTemplate('default_email_templates/' . $t_name . '.phtml')
                ->toHtml();
        return $email_html;
    }
    
    public function getBaseUrl($param) {
        if($param == 'URL_TYPE_MEDIA'){
            return $this->sp_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        }
    }
    
    /*
     * Function for replacing static images in the email template content
     */

    public function replaceEmailContentImages($email_content, $email_img_loc = false)
    {
        if (!$email_img_loc) {
            $email_img_loc = $this->getMediaUrl().'Knowband_Spinandwin/images/email/';
        }

        $email_content = str_replace('{minimal_img_path}', $email_img_loc . 'minimal6.png', $email_content);
        $email_content = str_replace('{icon_img_path}', $email_img_loc . 'ICON.png', $email_content);
        $email_content = str_replace('{fb_img_path}', $email_img_loc . 'FB.png', $email_content);
        $email_content = str_replace('{tumbler_img_path}', $email_img_loc . 'TUMBLER.png', $email_content);
        $email_content = str_replace('{pininterest_img_path}', $email_img_loc . 'PINTEREST.png', $email_content);
        $email_content = str_replace('{twitter_img_path}', $email_img_loc . 'TWITTER.png', $email_content);
        return $email_content;
    }
    
    public function getSalesName()
   {
       return $this->sp_scopeConfig->getValue(
           'trans_email/ident_sales/name',
           \Magento\Store\Model\ScopeInterface::SCOPE_STORE
       );
   }
   
   public function getSalesEmail()
   {
       return $this->sp_scopeConfig->getValue(
           'trans_email/ident_sales/email',
           \Magento\Store\Model\ScopeInterface::SCOPE_STORE
       );
   }
   
   public function getStoreName()
   {
       return $this->sp_scopeConfig->getValue(
           'trans_email/ident_general/name',
           \Magento\Store\Model\ScopeInterface::SCOPE_STORE
       );
   }
   public function getStoreEmail()
   {
       return $this->sp_scopeConfig->getValue(
           'trans_email/ident_general/email',
           \Magento\Store\Model\ScopeInterface::SCOPE_STORE
       );
   }
   
   /*
     * Send Coupon EMail to specified customer
     */

    public function sendSpinAndWinCouponEmail($email, $coupon_code, $template_data = array())
    {
        try {
            $emailTemplate = $this->sp_transportBuilder->setTemplateIdentifier('knowband_custom_email_template');
            //$emailTemplate = Mage::getModel('core/email_template')->loadDefault('spinandwin_coupon_email_template');
            if (isset($template_data['coupon_amount'])) {
                $e_settings = $this->getSettings('email_settings');
                if ($e_settings['coupon_display_options'] == 1) {
                    return false;
                }
                $getStoreIdDetails = $this->getStoreIdDetails();
                $storeId = $getStoreIdDetails[0];
                $websiteId = $getStoreIdDetails[1];
                $emailModel = $this->sp_emailModel->getCollection()
                        ->addFieldToFilter('template_name', $e_settings['email_template'])
                        ->addFieldToFilter('store_id', $storeId)
                        ->addFieldToFilter('website_id', $websiteId);
                $t_d = $emailModel->getData();
                if (isset($t_d[0]['template_subject']) && $t_d[0]['template_subject'] != '') {
                    $t_d['template_content'] = $t_d[0]['template_content'];
                    $t_d['template_subject'] = $t_d[0]['template_subject'];
                } else {
                    $emailModel = $this->sp_emailModel->getCollection()
                            ->addFieldToFilter('template_name', $e_settings['email_template'])
                            ->addFieldToFilter('store_id', 0)
                            ->addFieldToFilter('website_id', 0);
                    $t_d = $emailModel->getData();
                    if (isset($t_d[0]['email_subject']) && $t_d[0]['email_subject'] != '') {
                        $t_d['template_content'] = $t_d[0]['email_content'];
                        $t_d['template_subject'] = $t_d[0]['email_subject'];
                    } else {
                        $t_d = $this->getEmailTemplateData($e_settings['email_template']);
                    }
                }
                $params = array('_secure' => $this->sp_request->isSecure());
                $t_d['template_content'] = str_replace('{amount}', $template_data['coupon_amount'], $t_d['template_content']);
                $t_d['template_content'] = str_replace('{coupon_code}', $coupon_code, $t_d['template_content']);
                $t_d['template_content'] = str_replace('{store_url}', $this->sp_storeManager->getStore()->getBaseUrl(), $t_d['template_content']);
                $t_d['template_content'] = str_replace('{store_name}', $this->getStoreName(), $t_d['template_content']);
                $t_d['template_content'] = str_replace('{store_email}', $this->getStoreEmail(), $t_d['template_content']);
                $email_img_loc = $this->assetRepo->getUrlWithParams('Knowband_Spinandwin', $params) . '/images/email/';
                $t_d['template_content'] = $this->replaceEmailContentImages($t_d['template_content'], $email_img_loc);
                
                $om = \Magento\Framework\App\ObjectManager::getInstance();
                $t_d['template_content'] = $om->get('Magento\Cms\Model\Template\FilterProvider')->getPageFilter()->filter($t_d['template_content']);
                
                $emailTemplateVariables['email_content'] = $t_d['template_content'];
                $emailTemplateVariables['templateSubject'] = $t_d['template_subject'];
            } else {
                $template_data['template_content'] = str_replace('{amount}', 'xxxx', $template_data['template_content']);
                $template_data['template_content'] = str_replace('{coupon_code}', 'XXXXXXXXXX', $template_data['template_content']);
                $template_data['template_content'] = str_replace('{store_url}', $this->sp_storeManager->getStore()->getBaseUrl(), $template_data['template_content']);
                $template_data['template_content'] = str_replace('{store_name}', $this->getStoreName(), $template_data['template_content']);
                $template_data['template_content'] = str_replace('{store_email}', $this->getStoreEmail(), $template_data['template_content']);
                $emailTemplateVariables['email_content'] = $template_data['template_content'];
                $emailTemplateVariables['templateSubject'] = $template_data['template_subject'];
            }

            $name = __('New Customer');
            if (isset($template_data['scope']) && isset($template_data['scope_id'])) {
                $senderName = $this->sp_scopeConfig->getValue('trans_email/ident_general/name',$template_data['scope'], $template_data['scope_id']);
                $senderEmail = $this->sp_scopeConfig->getValue('trans_email/ident_general/email',$template_data['scope'], $template_data['scope_id']);
            } else {
                $senderName = $this->getStoreName();
                $senderEmail = $this->getStoreEmail();
            }
            
            $sender = ['name' => $senderName, 'email' => $senderEmail];
            $transport = $this->sp_transportBuilder->setTemplateIdentifier('spinandwin_coupon_email_template')
                    ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
                    ->setTemplateVars($emailTemplateVariables)
                    ->setFrom($sender)
                    ->addTo($email)
                    ->setReplyTo($senderEmail)
                    ->getTransport();
            $transport->sendMessage();
            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }
    
     /*
     * Function for getting the MailChimp Lists from the API Key Provided
     */

    public function mailchimpGetLists($api_key = null)
    {
        if (trim($api_key) != '' && $api_key !== null) {
            try {
//                new \Knowband\Spinandwin
                $Mailchimp = new MailChimp($api_key);
                $lists = $Mailchimp->get('lists');
                if ($lists !== false) {
                    $options = array();
                    foreach ($lists["lists"] as $list) {
                        $options["success"][] = array('value' => $list["id"], 'label' => $list["name"]);
                    }
                } else {
                    $options["error"][] = array('value' => "0", 'label' => __('No list found. (Verify Credentials)'));
                }
            } catch (\Exception $e) {
                $options["error"][] = array('value' => "0", 'label' => $e->getMessage());
            }
        } else {
            $options["error"][] = array('value' => "0", 'label' => __('No list found. (Verify Credentials)'));
        }

        return $options;
    }
    
    /*
     * Function for getting the MailChimp Lists from the API Key Provided
     */

    public function klaviyoGetLists($api_key = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://a.klaviyo.com/api/v1/lists?api_key=' . $api_key);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $output = json_decode(curl_exec($ch));
        curl_close($ch);

        if (property_exists($output, 'status')) {
            $status = $output->status;
            if ($status === 403) {
                $reason = __('The Private Klaviyo API Key you have set is invalid.');
            } elseif ($status === 401) {
                $reason = __('The Private Klaviyo API key you have set is no longer valid.');
            } else {
                $reason = __('Unable to verify Klaviyo Private API Key.');
            }

            $result = array(
                'success' => false,
                'reason' => $reason
            );
        } else {
            $static_groups = array_filter(
                $output->data, function ($list) {
                    return $list->list_type === 'list';
                }
            );

            usort(
                $static_groups, function ($a, $b) {
                    return strtolower($a->name) > strtolower($b->name) ? 1 : -1;
                }
            );

            $result = array(
                'success' => true,
                'lists' => $static_groups
            );
        }

        $options = array();
        if (!$result["success"]) {
            $options["error"][] = array('value' => "0", 'label' => $result["reason"]);
        } else {
            if (!empty($result["lists"])) {
                foreach ($result["lists"] as $list) {
                    $options["success"][] = array('value' => $list->id, 'label' => $list->name);
                }
            } else {
                $options["error"][] = array('value' => "0", 'label' => __('No list found. (Verify Credentials)'));
            }
        }

        return $options;
    }
    
    /*
     * Function for getting the Constant Contact Lists from the API Key and Access Token Provided
     */
    public function constantContactGetLists($api_key = null, $access_token = null)
    {
        if (trim($api_key) != '' && $api_key !== null) {
            if (trim($access_token) != '' && $access_token !== null) {
                try {
                    $cc = new ConstantContact($api_key);
                    $ctctlists = $cc->listService->getLists($access_token);
                    $options = array();
                    foreach ($ctctlists as $ctlist) {
                        $options["success"][] = array('value' => $ctlist->id, 'label' => $ctlist->name);
                    }
                } catch (CtctException $exception) {
                    $errors = array();
                    foreach ($exception->getErrors() as $error) {
                        $errors[] = $error;
                    }
                    $options["error"][] = array('value' => "0", 'label' => json_encode($errors));
                } catch (\Exception $e) {
                    $options["error"][] = array('value' => "0", 'label' => $e->getMessage());
                }
            }
        } else {
            $options["error"][] = array('value' => "0", 'label' => __('No list found. (Verify Credentials)'));
        }
        return $options;
    }
    
    /*
     * Function to detect device
     */

    public function detectDevice()
    {
        if(class_exists('Mobile_Detect')){
            $mobile_device = new Mobile_Detect();
            if ($mobile_device->isMobile()) {
                $mobile_class = "Mobile";
            } elseif ($mobile_device->isTablet()) {
                $mobile_class = "Tablet";
            } else {
                $mobile_class = "Desktop";
            }
        }else{
            $mobile_class = "Mobile";
        }

        return $mobile_class;
    }

    /*
     * Function to detect device
     */

    public function getCountryFromIp($full = false) {
        $ip = $this->getUserIp();

        if ($ip == '127.0.0.1') {
            return '0';
        } else {
            if (class_exists('Reader')) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
                $rootPath = $directory->getRoot();
                $reader = new Reader($rootPath . '/app/code/Knowband/Spinandwin/view/frontend/web/GeoLite2-Country.mmdb');

                $record = $reader->country($ip);

                if ($record !== false) {
                    if ($full) {
                        return $record->country->name;
                    } else {
                        return $record->country->isoCode;
                    }
                }
            }
        }
        return '0';
    }

    /*
     * Function for checking the IP of a customer
     */

    protected function getUserIp()
    {
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') === false) {
                $addr = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
                return trim($addr[0]);
            } else {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
    
    public function getSettings($key) {
        $settings = $this->getSavedSettings();
        return $settings[$key];
    }
    
    /*
     * Function to handle the front end rotate wheel functionality and create a coupon code in case the customer wins
     */
    public function checkSlicesAndGenerateCoupon($email = '', $fname = '', $lname = '') 
    {
        $response_array = array();
        $general_settings = $this->getSettings('general_settings');
        $this->mailchimpSubscribeEmailList($email, $fname, $lname);
        $this->klaviyoSubscribeEmailList($email, $fname, $lname);
        $this->constantContactSubscribeEmailList($email, $fname, $lname);
        if (isset($general_settings['email_recheck']) && $general_settings['email_recheck'] == 1) {
            if ($this->checkEmailRecheck($email)) {
                $response_array['email_recheck_failed'] = __('You have already taken advantage of this offer. Please try another email.');
                return $response_array;
            }
        }

        $slice_data = $this->getSettings('slice_settings');
        $number_of_slices = isset($slice_data['number_of_slices'])?(int)$slice_data['number_of_slices']:12;
        $prob_arr = array();
        $count = 1;
        foreach ($slice_data as $key => $slice) {
            if(isset($slice['gravity']) && $count <= $number_of_slices){
                for ($i = 0; $i < $slice['gravity']; $i++) {
                    $prob_arr[] = $key;
                }
                $count++;
            }
        }
        $random_key = array_rand($prob_arr);
        $selected_slice = $prob_arr[$random_key];
        $exploded_slice = explode('_', $selected_slice);
        $response_array['slice_to_select'] = $exploded_slice[1];
        
        if ($slice_data[$selected_slice]['coupon_value'] == 0 && $slice_data[$selected_slice]['coupon_type'] != 'S') {
            $response_array['win'] = false;
            $this->addUserToUserList(
                    null,
                    $email
                );
            $this->addUserDetail($email,$fname,$lname);
            $response_array['error'] = __('Not your lucky day today. Better Luck Next Time.');
        } else {
            $response_array['win'] = true;
            //generate automatic coupon code
            $coupon_code = $this->generateRandomCouponCode();
            //creating rule & coupon
            if($slice_data[$selected_slice]['coupon_type'] == 'S'){
                $flag = true;
            } else {
                $flag = false;
            }
            $check_rule_creation = $this->createCartRule(
                $coupon_code,
                1,
                $slice_data[$selected_slice]['coupon_type'],
                $slice_data[$selected_slice]['coupon_value'],
                $flag,
                true
            );
            if ($check_rule_creation) {
                $coupon_id = $this->addCouponToCouponsList(
                    $coupon_code,
                    $slice_data[$selected_slice]['coupon_value'],
                    $slice_data[$selected_slice]['coupon_type']
                );
                $this->addUserToUserList(
                    $coupon_id,
                    $email
                );
                
                $this->addUserDetail($email,$fname,$lname);
                
                $response_array['coupon_code'] = $coupon_code;
                $response_array['label'] = $slice_data[$selected_slice]['label'];
            } else {
                $response_array['coupon_code'] = '';
                $response_array['coupon_error'] = __('Something went wrong while generating the coupon code, please try again.');
            }
        }

        return $response_array;
    }
    
    /*
     * Function for adding coupon to coupons list
     */

    public function addCouponToCouponsList($code, $c_val, $c_val_type)
    {
        try {
            $couponModel = $this->sp_couponModel;
            $storeId = $this->sp_storeManager->getStore()->getStoreId();
            $websiteId = $this->sp_storeManager->getStore()->getWebsiteId();
            $couponModel->setStoreId($storeId);
            $couponModel->setWebsiteId($websiteId);
            $couponModel->setCouponCode($code);
            $couponModel->setCouponValue($c_val);
            $couponModel->setCouponValueType($c_val_type);
            $couponModel->setUseType('1');
            $couponModel->setCouponExpireInDays('1');
            $from_date = date('Y-m-d');
            $to_date = date('Y-m-d', strtotime($from_date . ' + 1 day'));
            $couponModel->setCouponExpireDate($to_date);
            $currency = $this->sp_storeManager->getStore()->getCurrentCurrencyCode();
            $couponModel->setDiscountCurrency($currency);
            $couponModel->setCreatedAt($this->date->date());
            $couponModel->setUpdatedAt($this->date->date());
            $couponModel->save();
            $inserted_id = $couponModel->getId();
            $couponModel->unsetData();
        } catch (\Exception $e) {
        }

        return $inserted_id;
    }
    
    /*
     * Function for adding user to users list
     */

    public function addUserToUserList($coupon_id, $email)
    {
        try {
            $userModel = $this->sp_userModel;
            $userModel->setCouponId($coupon_id);
            $userModel->setCustomerEmail($email);
            $userModel->setCouponUsage('0');
            $device = $this->detectDevice();
            $userModel->setDevice($device);
            $country = $this->getCountryFromIp(true);
            $userModel->setCountry($country);
            $userModel->setCreatedAt($this->date->date());
            $userModel->setUpdatedAt($this->date->date());
            $userModel->save();
            $userModel->unsetData();
        } catch (\Exception $e) {
        }
    }
    
    public function addUserDetail($email, $fname, $lname)
    {
        
        try {
            $spinuserModel = $this->sp_spinUserdata;
            $spinuserModel->setFname($fname);
            $spinuserModel->setLname($lname);
            $spinuserModel->setEmail($email);
            $spinuserModel->setDateAdded($this->date->date());
            $spinuserModel->save();
        } catch (\Exception $e) {          
        }
        
    }
    
    /*
     * Function for sending the coupon code to customer through an email
     */
    public function sendEmailWithCouponCode($cus_email, $coupon_code)
    {
        $e_settings = $this->getSettings('email_settings');
        $response_array = array();
        if ($e_settings['coupon_display_options'] != 1) {
            $email_variables = array();
            $email_variables['email'] = $cus_email;
            $email_variables['coupon_code'] = $coupon_code;

            $storeId = $this->sp_storeManager->getStore()->getStoreId();
            $websiteId = $this->sp_storeManager->getStore()->getWebsiteId();
            $couponModel = $this->sp_couponModel->getCollection()
                    ->addFieldToFilter('coupon_code', $coupon_code)
                    ->addFieldToFilter('store_id', $storeId)
                    ->addFieldToFilter('website_id', $websiteId);
            $c_data = $couponModel->getData();
            if (isset($c_data[0]['coupon_value_type']) && $c_data[0]['coupon_value_type'] == 'P') {
                $email_variables['coupon_amount'] = $c_data[0]['coupon_value'].'%';
            } else {
                if (isset($c_data[0]['coupon_value'])) {
                    $email_variables['coupon_amount'] = $this->_priceHelper->currency($c_data[0]['coupon_value'], true, false);
                } else {
                    $email_variables['coupon_amount'] = '****';
                }
            }

            if ($this->sendSpinAndWinCouponEmail($cus_email, $coupon_code, $email_variables)) {
                $response_array['success'] = true;
            } else {
                $response_array['error'] = __('Something went wrong while sending the coupon email, please try again.');
            }
        }

        return $response_array;
    }
    
    /*
     * Function for adding customer email to the specified MailChimp list
     */

    public function mailchimpSubscribeEmailList($email, $first_name = null, $last_name = null)
    {
        try {
            $db_settings = $this->getSettings('email_marketing');
            if (isset($db_settings['mailchimp_enable']) && $db_settings['mailchimp_enable'] == 1) {
                $api_key = $db_settings['mailchimp_api'];
                $list_id = $db_settings['mailchimp_list'];
                $Mailchimp = new Mailchimp($api_key);
                
                $result = $Mailchimp->post(
                    "lists/$list_id/members", 
                    array(
                        'email_address' => trim($email),
                        'status' => 'subscribed',
                    )
                );

                $subscriber_hash = $Mailchimp->subscriberHash(trim($email));
                $Mailchimp->patch("lists/$list_id/members/$subscriber_hash", array('merge_fields' => array('FNAME' => $first_name, 'LNAME' => $last_name)));
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /*
     * Function for adding customer email to the specified Klaviyo list
     */
    public function klaviyoSubscribeEmailList($email, $first_name = null, $last_name = null)
    {
        $db_settings = $this->getSettings('email_marketing');
        if (isset($db_settings['klaviyo_enable']) && $db_settings['klaviyo_enable'] == 1) {
            $api_key = $db_settings['klaviyo_api'];
            $list_id = $db_settings['klaviyo_list'];
            $properties = array();
            if ($first_name) {
                $properties['$first_name'] = $first_name;
            }

            if ($last_name) {
                $properties['$last_name'] = $last_name;
            }

            $properties_val = count($properties) ? urlencode(json_encode($properties)) : '{}';
            $fields = array(
                'api_key=' . $api_key,
                'email=' . urlencode($email),
                'confirm_optin=false',
                'properties=' . $properties_val,
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://a.klaviyo.com/api/v1/list/' . $list_id . '/members');
            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, join('&', $fields));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_exec($ch);
            curl_close($ch);
        } else {
            return false;
        }
    }
    
    /*
     * Subcribes a user to contant contact API
     */
    public function constantContactSubscribeEmailList($email, $firstname = '', $lastname = '') {
        $db_settings = $this->getSettings('email_marketing');
        if (isset($db_settings['constant_contact_enable']) && $db_settings['constant_contact_enable'] == 1) {
            $api_key = $db_settings['constant_contact_api'];
            $access_token = $db_settings['constant_contact_token'];
            $list_id = $db_settings['constant_contact_list'];
            try {
                $cc = new ConstantContact($api_key);
                $contacts = $cc->contactService->getContacts($access_token, array('email' => $email));
                if (empty($contacts->results)) {
                    $contact = new Contact();
                    $contact->addEmail($email);
                    $contact->addList($list_id);
                    $contact->first_name = $firstname;
                    $contact->last_name = $lastname;
                    $cc->contactService->addContact($access_token, $contact, false);
                }else{
                    $contact = $contacts->results[0];
                    $contact->addList($list_id);
                    $contact->first_name = '';
                    $contact->last_name = '';
                    $cc->contactService->updateContact($access_token, $contact, false);
                }
            } catch (CtctException $ex) {
                $errors = $ex->getErrors();
                if (count($errors) > 0) {
                    $response['error'] = __('Error occurred while subscribing. Please try again later.');
                }
                return false;
            }
            return true;
        }else{
            return false;
        }
    }
    
    /*
     * Function for checking email recheck setting
     */

    public function checkEmailRecheck($email)
    {
        $userModel = $this->sp_userModel->getCollection()
                ->addFieldToFilter('customer_email', $email);
        $user_data = $userModel->getData();
        if (is_array($user_data) && !empty($user_data)) {
            return true;
        } else {
            return false;
        }
    }
    /*
     * Function to save the email template contents
     */
    public function saveEmailTemplate($id, $name, $sub, $content)
    {
        try {
            $getStoreIdDetails = $this->getStoreIdDetails();
            $storeId = $getStoreIdDetails[0];
            $websiteId = $getStoreIdDetails[1];
            $emailModel = $this->sp_emailModel->load((int) $id);
            if ($emailModel->getData()) {
                $emailModel->setTemplateName($name);
                $emailModel->setTemplateSubject($sub);
                $emailModel->setTemplateContent($content);
                $emailModel->setTemplateDescription($name);
                $emailModel->setUpdatedAt($this->date->date());
                $emailModel->setId($id)->save();
            } else {
                $emailModel->setStoreId($storeId);
                $emailModel->setWebsiteId($websiteId);
                $emailModel->setTemplateName($name);
                $emailModel->setTemplateSubject($sub);
                $emailModel->setTemplateContent($content);
                $emailModel->setTemplateDescription($name);
                $emailModel->setCreatedAt($this->date->date());
                $emailModel->setUpdatedAt($this->date->date());
                $emailModel->save();
            }

            $emailModel->unsetData();
        } catch (\Exception $e) {
        }
    }
    
    /*
     * Function for getting the Country Pie Charts Data
     */
    public function getCountryPieChartsData()
    {
        $userCollection = $this->sp_userModel->getCollection();
        $userCollection->addExpressionFieldToSelect("total_country" , 'COUNT({{main_table.country}})',array("main_table.country" => "main_table.country"));
        $userCollection->getSelect()->group('main_table.country');
        $final_data = array();
        $count = 0;
        foreach($userCollection as $coll)
        {
            $final_data[$count]['country'] = $coll->getCountry();
            $final_data[$count]['country_count'] = $coll->getTotalCountry();
            $count++;
        }
        return $final_data;
    }
    
    /*
     * Function for getting the Device Pie Charts Data
     */
    public function getDevicePieChartsData()
    {
        $userCollection = $this->sp_userModel->getCollection();
        $userCollection->addExpressionFieldToSelect("total_device" , 'COUNT({{main_table.device}})',array("main_table.device" => "main_table.device"));
        $userCollection->getSelect()->group('main_table.device');
        $final_data = array();
        $count = 0;
        foreach($userCollection as $coll)
        {
            $final_data[$count]['device'] = $coll->getDevice();
            $final_data[$count]['device_count'] = $coll->getTotalDevice();
            $count++;
        }
        return $final_data;
    }
    
    /*
     * Function for getting the Count Header Data (Stats Tab)
     */
    public function getCountStatsData()
    {
        $final_data = array();
        $userCollection = $this->sp_userModel->getCollection();
        $final_data['total_generated'] = $userCollection->getSize();
        $userCollection->addFieldToFilter('main_table.coupon_usage', array('eq' => '1'));
        $final_data['total_used'] = $userCollection->count();
        return $final_data;
    }
    
    /*
     * Function for getting the Line Cart Data
     */
    public function getLineChartFilteredData($from_date, $to_date)
    {
        if (isset($from_date) && isset($to_date)) {
            $start_date = explode('/', $from_date);
            $start_date = $start_date[2] . '-' . $start_date[0] . '-' . $start_date[1];
            $end_date = explode('/', $to_date);
            $end_date = $end_date[2] . '-' . $end_date[0] . '-' . $end_date[1];
            $start_date = strtotime($start_date);
            $end_date = strtotime($end_date);
            $datediff = $end_date - $start_date;
            $days = floor($datediff / (60 * 60 * 24));
            // d($days);
            if ($days == 0) {
                $diff = 'hours';
            } elseif ($days < 31) {
                $diff = 'days';
            } elseif ($days < 366) {
                $diff = 'months';
            } else {
                $diff = 'year';
            }

            switch ($diff) {
                case 'hours':
                    $start_date = date('Y-m-d', $start_date);
                    $new_start_date = date('Y-m-d 00:00:00', strtotime($start_date));
                    $end_date = date('Y-m-d 23:59:59', $end_date);
                    $couponCollection = $this->sp_couponModel->getCollection();
                    $couponCollection->addExpressionFieldToSelect("total_coupons" , 'COUNT({{main_table.coupon_id}})', array("main_table.coupon_id" => "main_table.coupon_id"));
                    $couponCollection->addExpressionFieldToSelect("hour" , 'HOUR({{main_table.created_at}})', array("main_table.created_at" => "main_table.created_at"));
                    $couponCollection->getSelect()->where('DATE(main_table.created_at) >= "'.$new_start_date.'" and DATE(main_table.created_at) <="'.$end_date.'"');
                    $couponCollection->getSelect()->group('HOUR(created_at)');
                    $couponCollection->getSelect()->order('HOUR(created_at)');
                    $total_generated_coupon_data = $couponCollection->getData();
                    unset($couponCollection);
                    $i = 0;
                    $dates = array();
                    $coupons_data = array();
                    foreach ($total_generated_coupon_data as $k => $coupon) {
                        $coupons_data[$coupon['hour']] = $coupon['total_coupons'];
                    }
                    //d($coupons_data);
                    $total_generated_coupon_graph_data = array();
                    $date = 0;
                    while (true) {
                        $i++;
                        if (isset($coupons_data[$date])) {
                            $total_generated_coupon_graph_data[] = $coupons_data[$date];
                        } else {
                            $total_generated_coupon_graph_data[] = 0;
                        }
                        $date += 1;
                        if ($date > date('H', strtotime($end_date))) {
                            break;
                        }
                        if ($i == 25) {
                            break;
                        }
                    }
                    $graph_data = array();
                    $graph_data['total_generated'] = $total_generated_coupon_graph_data;

                    $couponCollection = $this->sp_couponModel->getCollection();
                    $couponCollection->getSelect()->join(array('user' => $couponCollection->getTable('vss_spinandwin_user_list')), 'user.coupon_id = main_table.coupon_id');
                    $couponCollection->addExpressionFieldToSelect("unused_coupons" , 'COUNT({{main_table.coupon_id}})', array("main_table.coupon_id" => "main_table.coupon_id"));
                    $couponCollection->addExpressionFieldToSelect("hour" , 'HOUR({{main_table.created_at}})', array("main_table.created_at" => "main_table.created_at"));
                    $couponCollection->getSelect()->where('coupon_usage = "0" and DATE(main_table.created_at) >= "'.$new_start_date.'" and DATE(main_table.created_at) <="'.$end_date.'"');
                    $couponCollection->getSelect()->group('HOUR(created_at)');
                    $couponCollection->getSelect()->order('HOUR(created_at)');
                    $total_unused_data = $couponCollection->getData();
                    unset($couponCollection);

                    $i = 0;
                    $dates = array();
                    $coupons_data = array();
                    foreach ($total_unused_data as $k => $coupon) {
                        $coupons_data[$coupon['hour']] = $coupon['unused_coupons'];
                    }
                    $total_unused_garph_data = array();
                    $date = 0;
                    while (true) {
                        $i++;
                        if (isset($coupons_data[$date])) {
                            $total_unused_garph_data[] = $coupons_data[$date];
                        } else {
                            $total_unused_garph_data[] = 0;
                        }
                        $date += 1;
                        if ($date > date('H', strtotime($end_date))) {
                            break;
                        }
                        if ($i == 25) {
                            break;
                        }
                    }
                    $graph_data['total_unused'] = $total_unused_garph_data;
                    
                    $couponCollection = $this->sp_couponModel->getCollection();
                    $couponCollection->getSelect()->join(array('user' => $couponCollection->getTable('vss_spinandwin_user_list')), 'user.coupon_id = main_table.coupon_id');
                    $couponCollection->addExpressionFieldToSelect("used_coupons" , 'COUNT({{main_table.coupon_id}})', array("main_table.coupon_id" => "main_table.coupon_id"));
                    $couponCollection->addExpressionFieldToSelect("hour" , 'HOUR({{main_table.created_at}})', array("main_table.created_at" => "main_table.created_at"));
                    $couponCollection->getSelect()->where('coupon_usage = "1" and DATE(main_table.created_at) >= "'.$new_start_date.'" and DATE(main_table.created_at) <="'.$end_date.'"');
                    $couponCollection->getSelect()->group('HOUR(created_at)');
                    $couponCollection->getSelect()->order('HOUR(created_at)');
                    $total_used_data = $couponCollection->getData();
                    unset($couponCollection);
                    $i = 0;
                    $dates = array();
                    $coupons_data = array();
                    foreach ($total_used_data as $k => $coupon) {
                        $coupons_data[$coupon['hour']] = $coupon['used_coupons'];
                    }
                    $total_used_graph_data = array();
                    $date = 0;
                    $ticks = array();
                    while (true) {
                        $i++;
                        if (isset($coupons_data[$date])) {
                            $total_used_graph_data[] = $coupons_data[$date];
                        } else {
                            $total_used_graph_data[] = 0;
                        }
                        $ticks[] = date("h a", strtotime($new_start_date));
                        $date += 1;
                        $new_start_date = date("Y-m-d H:i:s", strtotime("+1 hour", strtotime($new_start_date)));
                        if ($date > date('H', strtotime($end_date))) {
                            break;
                        }
                        if ($i == 25) {
                            break;
                        }
                    }
                    $graph_data['total_used'] = $total_used_graph_data;
                    $graph_data['ticks'] = $ticks;
                    return $graph_data;
                    break;
                case 'days':
                    $start_date = date('Y-m-d 00:00:00', $start_date);
                    $end_date = date('Y-m-d 23:59:59', $end_date);
                    
                    $couponCollection = $this->sp_couponModel->getCollection();
                    $couponCollection->addExpressionFieldToSelect("total_coupons" , 'COUNT({{main_table.coupon_id}})', array("main_table.coupon_id" => "main_table.coupon_id"));
                    $couponCollection->addExpressionFieldToSelect("day" , 'DATE({{main_table.created_at}})', array("main_table.created_at" => "main_table.created_at"));
                    $couponCollection->getSelect()->where('DATE(main_table.created_at) >= "'.$start_date.'" and DATE(main_table.created_at) <="'.$end_date.'"');
                    $couponCollection->getSelect()->group('DATE(created_at)');
                    $couponCollection->getSelect()->order('DATE(created_at)');
                    $total_generated_coupon_data = $couponCollection->getData();
                    unset($couponCollection);
                    $i = 0;
                    $dates = array();
                    $coupons_data = array();
                    foreach ($total_generated_coupon_data as $k => $coupon) {
                        $coupons_data[$coupon['day']] = $coupon['total_coupons'];
                    }

                    $i = 0;
                    $date = date('Y-m-d', strtotime($start_date));
                    while (true) {
                        $i++;
                        if (isset($coupons_data[$date])) {
                            $total_generated_coupon_graph_data[] = $coupons_data[$date];
                        } else {
                            $total_generated_coupon_graph_data[] = 0;
                        }
                        $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                        if (strtotime($date) > strtotime($end_date)) {
                            break;
                        }
                        if ($i == 60) {
                            break;
                        }
                    }
                    $graph_data['total_generated'] = $total_generated_coupon_graph_data;

                    $couponCollection = $this->sp_couponModel->getCollection();
                    $couponCollection->getSelect()->join(array('user' => $couponCollection->getTable('vss_spinandwin_user_list')), 'user.coupon_id = main_table.coupon_id');
                    $couponCollection->addExpressionFieldToSelect("unused_coupons" , 'COUNT({{main_table.coupon_id}})', array("main_table.coupon_id" => "main_table.coupon_id"));
                    $couponCollection->addExpressionFieldToSelect("day" , 'DATE({{main_table.created_at}})', array("main_table.created_at" => "main_table.created_at"));
                    $couponCollection->getSelect()->where('coupon_usage = "0" and DATE(main_table.created_at) >= "'.$start_date.'" and DATE(main_table.created_at) <="'.$end_date.'"');
                    $couponCollection->getSelect()->group('DATE(created_at)');
                    $couponCollection->getSelect()->order('DATE(created_at)');
                    $total_unused_data = $couponCollection->getData();
                    unset($couponCollection);

                    $i = 0;
                    $dates = array();
                    $coupons_data = array();
                    foreach ($total_unused_data as $k => $coupon) {
                        $coupons_data[$coupon['day']] = $coupon['unused_coupons'];
                    }

                    $i = 0;


                    $date = date('Y-m-d', strtotime($start_date));
                    while (true) {
                        $i++;
                        if (isset($coupons_data[$date])) {
                            $total_unused_garph_data[] = $coupons_data[$date];
                        } else {
                            $total_unused_garph_data[] = 0;
                        }
                        $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                        if (strtotime($date) > strtotime($end_date)) {
                            break;
                        }
                        if ($i == 60) {
                            break;
                        }
                    }
                    $graph_data['total_unused'] = $total_unused_garph_data;
                    $couponCollection = $this->sp_couponModel->getCollection();
                    $couponCollection->getSelect()->join(array('user' => $couponCollection->getTable('vss_spinandwin_user_list')), 'user.coupon_id = main_table.coupon_id');
                    $couponCollection->addExpressionFieldToSelect("used_coupons" , 'COUNT({{main_table.coupon_id}})', array("main_table.coupon_id" => "main_table.coupon_id"));
                    $couponCollection->addExpressionFieldToSelect("day" , 'DATE({{main_table.created_at}})', array("main_table.created_at" => "main_table.created_at"));
                    $couponCollection->getSelect()->where('coupon_usage = "1" and DATE(main_table.created_at) >= "'.$start_date.'" and DATE(main_table.created_at) <="'.$end_date.'"');
                    $couponCollection->getSelect()->group('DATE(created_at)');
                    $couponCollection->getSelect()->order('DATE(created_at)');
                    $total_used_data = $couponCollection->getData();
                    unset($couponCollection);

                    $i = 0;
                    $ticks = array();
                    $dates = array();
                    $coupons_data = array();
                    foreach ($total_used_data as $k => $coupon) {
                        $coupons_data[$coupon['day']] = $coupon['used_coupons'];
                    }

                    $i = 0;

                    $total_used_garph_data = array();
                    $date = date('Y-m-d', strtotime($start_date));
                    while (true) {
                        $i++;
                        if (isset($coupons_data[$date])) {
                            $total_used_garph_data[] = $coupons_data[$date];
                        } else {
                            $total_used_garph_data[] = 0;
                        }
                        $ticks[] = date("d M", strtotime($date));
                        $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                        if (strtotime($date) > strtotime($end_date)) {
                            break;
                        }
                        if ($i == 60) {
                            break;
                        }
                    }
                    $graph_data['total_used'] = $total_used_garph_data;
                    $graph_data['ticks'] = $ticks;
                    return $graph_data;

                    break;

                case 'months':
                    $start_date = date('Y-m-d 00:00:00', $start_date);
                    $end_date = date('Y-m-d 23:59:59', $end_date);
                    $couponCollection = $this->sp_couponModel->getCollection();
                    $couponCollection->addExpressionFieldToSelect("total_coupons" , 'COUNT({{main_table.coupon_id}})', array("main_table.coupon_id" => "main_table.coupon_id"));
                    $couponCollection->addExpressionFieldToSelect("month" , 'MONTH({{main_table.created_at}})', array("main_table.created_at" => "main_table.created_at"));
                    $couponCollection->getSelect()->where('DATE(main_table.created_at) >= "'.$start_date.'" and DATE(main_table.created_at) <="'.$end_date.'"');
                    $couponCollection->getSelect()->group('MONTH(created_at)');
                    $couponCollection->getSelect()->order('MONTH(created_at)');
                    $total_generated_coupon_data = $couponCollection->getData();
                    unset($couponCollection);
                    $i = 0;
                    $date = date('m', strtotime($start_date));
                    $coupons_data = array();
                    foreach ($total_generated_coupon_data as $k => $coupon) {
                        $coupons_data[$coupon['month']] = $coupon['total_coupons'];
                    }
                    $i = 0;
                    while (true) {
                        $i++;
                        if (isset($coupons_data[$date])) {
                            $total_generated_coupon_graph_data[] = $coupons_data[$date];
                        } else {
                            $total_generated_coupon_graph_data[] = 0;
                        }
                        $date += 1;
                        if ($date > date('m', strtotime($end_date))) {
                            break;
                        }
                        if ($i == 60) {
                            break;
                        }
                    }
                    $graph_data['total_generated'] = $total_generated_coupon_graph_data;

                    $couponCollection = $this->sp_couponModel->getCollection();
                    $couponCollection->getSelect()->join(array('user' => $couponCollection->getTable('vss_spinandwin_user_list')), 'user.coupon_id = main_table.coupon_id');
                    $couponCollection->addExpressionFieldToSelect("unused_coupons" , 'COUNT({{main_table.coupon_id}})', array("main_table.coupon_id" => "main_table.coupon_id"));
                    $couponCollection->addExpressionFieldToSelect("month" , 'MONTH({{main_table.created_at}})', array("main_table.created_at" => "main_table.created_at"));
                    $couponCollection->getSelect()->where('coupon_usage = "0" and DATE(main_table.created_at) >= "'.$start_date.'" and DATE(main_table.created_at) <="'.$end_date.'"');
                    $couponCollection->getSelect()->group('MONTH(created_at)');
                    $couponCollection->getSelect()->order('MONTH(created_at)');
                    $total_unused_data = $couponCollection->getData();
                    unset($couponCollection);

                    $i = 0;
                    $date = date('m', strtotime($start_date));
                    $coupons_data = array();
                    foreach ($total_unused_data as $k => $coupon) {
                        $coupons_data[$coupon['month']] = $coupon['unused_coupons'];
                    }
                    $i = 0;
                    while (true) {
                        $i++;
                        if (isset($coupons_data[$date])) {
                            $total_unused_garph_data[] = $coupons_data[$date];
                        } else {
                            $total_unused_garph_data[] = 0;
                        }
                        $date += 1;
                        if ($date > date('m', strtotime($end_date))) {
                            break;
                        }
                        if ($i == 60) {
                            break;
                        }
                    }
                    $graph_data['total_unused'] = $total_unused_garph_data;

                    $couponCollection = $this->sp_couponModel->getCollection();
                    $couponCollection->getSelect()->join(array('user' => $couponCollection->getTable('vss_spinandwin_user_list')), 'user.coupon_id = main_table.coupon_id');
                    $couponCollection->addExpressionFieldToSelect("used_coupons" , 'COUNT({{main_table.coupon_id}})', array("main_table.coupon_id" => "main_table.coupon_id"));
                    $couponCollection->addExpressionFieldToSelect("month" , 'MONTH({{main_table.created_at}})', array("main_table.created_at" => "main_table.created_at"));
                    $couponCollection->getSelect()->where('coupon_usage = "1" and DATE(main_table.created_at) >= "'.$start_date.'" and DATE(main_table.created_at) <="'.$end_date.'"');
                    $couponCollection->getSelect()->group('MONTH(created_at)');
                    $couponCollection->getSelect()->order('MONTH(created_at)');
                    $total_used_data = $couponCollection->getData();
                    unset($couponCollection);

                    $date = date('m', strtotime($start_date));
                    $coupons_data = array();
                    $ticks = array();
                    foreach ($total_used_data as $k => $coupon) {
                        $coupons_data[$coupon['month']] = $coupon['used_coupons'];
                    }
                    $i = 0;
                    while (true) {
                        $i++;
                        if (isset($coupons_data[$date])) {
                            $total_used_garph_data[] = $coupons_data[$date];
                        } else {
                            $total_used_garph_data[] = 0;
                        }
                        $ticks[] = $this->getmonths($date);
                        $date += 1;
                        if ($date > date('m', strtotime($end_date))) {
                            break;
                        }
                        if ($i == 60) {
                            break;
                        }
                    }
                    $graph_data['total_used'] = $total_used_garph_data;
                    $graph_data['ticks'] = $ticks;
                    return $graph_data;
                    break;

                case 'year':
                    $start_date = date('Y-m-d 00:00:00', $start_date);
                    $end_date = date('Y-m-d 23:59:59', $end_date);

                    $couponCollection = $this->sp_couponModel->getCollection();
                    $couponCollection->addExpressionFieldToSelect("total_coupons" , 'COUNT({{main_table.coupon_id}})', array("main_table.coupon_id" => "main_table.coupon_id"));
                    $couponCollection->addExpressionFieldToSelect("Year" , 'YEAR({{main_table.created_at}})', array("main_table.created_at" => "main_table.created_at"));
                    $couponCollection->getSelect()->where('DATE(main_table.created_at) >= "'.$start_date.'" and DATE(main_table.created_at) <="'.$end_date.'"');
                    $couponCollection->getSelect()->group('YEAR(created_at)');
                    $couponCollection->getSelect()->order('YEAR(created_at)');
                    $total_generated_coupon_data = $couponCollection->getData();
                    unset($couponCollection);
                    $date = date('Y', strtotime($start_date));
                    $coupons_data = array();

                    foreach ($total_generated_coupon_data as $k => $coupon) {
                        $coupons_data[$coupon['Year']] = $coupon['total_coupons'];
                    }
                    $i = 0;
                    while (true) {
                        $i++;
                        if (isset($coupons_data[$date])) {
                            $total_generated_coupon_graph_data[] = $coupons_data[$date];
                        } else {
                            $total_generated_coupon_graph_data[] = 0;
                        }
                        $date += 1;
                        if ($date > date('Y', strtotime($end_date))) {
                            break;
                        }
                        if ($i == 60) {
                            break;
                        }
                    }

                    $graph_data['total_generated'] = $total_generated_coupon_graph_data;
                    $couponCollection = $this->sp_couponModel->getCollection();
                    $couponCollection->getSelect()->join(array('user' => $couponCollection->getTable('vss_spinandwin_user_list')), 'user.coupon_id = main_table.coupon_id');
                    $couponCollection->addExpressionFieldToSelect("unused_coupons" , 'COUNT({{main_table.coupon_id}})', array("main_table.coupon_id" => "main_table.coupon_id"));
                    $couponCollection->addExpressionFieldToSelect("Year" , 'YEAR({{main_table.created_at}})', array("main_table.created_at" => "main_table.created_at"));
                    $couponCollection->getSelect()->where('coupon_usage = "0" and DATE(main_table.created_at) >= "'.$start_date.'" and DATE(main_table.created_at) <="'.$end_date.'"');
                    $couponCollection->getSelect()->group('YEAR(created_at)');
                    $couponCollection->getSelect()->order('YEAR(created_at)');
                    $total_unused_data = $couponCollection->getData();
                    unset($couponCollection);

                    $date_strt = date('Y', strtotime($start_date));
                    $coupons_used_data = array();
                    foreach ($total_unused_data as $k => $coupon) {
                        $coupons_used_data[$coupon['Year']] = $coupon['unused_coupons'];
                    }
                    $i = 0;
                    while (true) {
                        $i++;
                        if (isset($coupons_used_data[$date_strt])) {
                            $total_unused_garph_data[] = $coupons_used_data[$date_strt];
                        } else {
                            $total_unused_garph_data[] = 0;
                        }
                        $date_strt += 1;
                        if ($date_strt > date('Y', strtotime($end_date))) {
                            break;
                        }
                        if ($i == 60) {
                            break;
                        }
                    }

                    $graph_data['total_unused'] = $total_unused_garph_data;

                    $couponCollection = $this->sp_couponModel->getCollection();
                    $couponCollection->getSelect()->join(array('user' => $couponCollection->getTable('vss_spinandwin_user_list')), 'user.coupon_id = main_table.coupon_id');
                    $couponCollection->addExpressionFieldToSelect("used_coupons" , 'COUNT({{main_table.coupon_id}})', array("main_table.coupon_id" => "main_table.coupon_id"));
                    $couponCollection->addExpressionFieldToSelect("Year" , 'YEAR({{main_table.created_at}})', array("main_table.created_at" => "main_table.created_at"));
                    $couponCollection->getSelect()->where('coupon_usage = "1" and DATE(main_table.created_at) >= "'.$start_date.'" and DATE(main_table.created_at) <="'.$end_date.'"');
                    $couponCollection->getSelect()->group('YEAR(created_at)');
                    $couponCollection->getSelect()->order('YEAR(created_at)');
                    $total_used_data = $couponCollection->getData();
                    unset($couponCollection);
                    $date = date('Y', strtotime($start_date));
                    $discount_data = array();
                    $ticks = array();

                    foreach ($total_used_data as $k => $discount) {
                        $discount_data[$discount['Year']] = $discount['used_coupons'];
                    }
                    $i = 0;
                    while (true) {
                        $i++;
                        if (isset($discount_data[$date])) {
                            $total_used_garph_data[] = $discount_data[$date];
                        } else {
                            $total_used_garph_data[] = 0;
                        }
                        $ticks[] = $date;
                        $date += 1;
                        if ($date > date('Y', strtotime($end_date))) {
                            break;
                        }
                        if ($i == 60) {
                            break;
                        }
                    }
                    $graph_data['total_used'] = $total_used_garph_data;
                    $graph_data['ticks'] = $ticks;
                    return $graph_data;
                    break;

                default:
                    $graph_data = array();
                    return $graph_data;
                    break;
            }
        }
    }
    
    
    public function getMonths($month_num)
    {
        $month_name = '';
        switch ($month_num) {
            case '1':
                $month_name = __('Jan');
                break;
            case '2':
                $month_name = __('Feb');
                break;
            case '3':
                $month_name = __('Mar');
                break;
            case '4':
                $month_name = __('Apr');
                break;
            case '5':
                $month_name = __('May');
                break;
            case '6':
                $month_name = __('Jun');
                break;
            case '7':
                $month_name = __('Jul');
                break;
            case '8':
                $month_name = __('Aug');
                break;
            case '9':
                $month_name = __('Sep');
                break;
            case '10':
                $month_name = __('Oct');
                break;
            case '11':
                $month_name = __('Nov');
                break;
            case '12':
                $month_name = __('Dec');
                break;
        }
        return $month_name;
    }

    
    public function getThemes()
    {
        
        return array(
            '1' => __('None'),
            'rose_day' => __('Rose Day'),
            'propose_day' => __('Propose Day'),
            'chocolate_day' => __('Chocolate Day'),
            'teddy_day' => __('Teddy Day'),
            'promise_day' => __('Promise Day'),
            'hug_day' => __('Hug Day'),
            'kiss_day' => __('Kiss Day'),
            'valentine_day' => __('Valentine\'s Day'),
            '2' => __('Xmas Theme 1'),
            '3' => __('Xmas Theme 2'),
            'merry_christmas_1' => __('Merry Christmas - Theme 1'),
            'merry_christmas_2' => __('Merry Christmas - Theme 2'),
            'merry_christmas_3' => __('Merry Christmas - Theme 3'),
            'merry_christmas_4' => __('Merry Christmas - Theme 4'),
            'merry_christmas_5' => __('Merry Christmas - Theme 5'),    
            'blackfriday_theme_1' => __('Black Friday Theme1'),
            'blackfriday_theme_2' => __('Black Friday Theme2'),
            'blackfriday_theme_3' => __('Black Friday Theme3'),
            'blackfriday_theme_4' => __('Black Friday Theme4'),
            'diwali_theme_1' => __('Diwali Theme1'),
            'diwali_theme_2' => __('Diwali Theme2'),
            'diwali_theme_3' => __('Diwali Theme3'),
            'diwali_theme_4' => __('Diwali Theme4'),
            'diwali_theme_5' => __('Diwali Theme5'),
            'easter_theme_1' => __('Easter Theme1'),
            'easter_theme_2' => __('Easter Theme2'),
            'easter_theme_3' => __('Easter Theme3'),
            'holloween_theme_1' => __('Halloween Theme1'),
            'holloween_theme_2' => __('Halloween Theme2'),
            'holloween_theme_3' => __('Halloween Theme3'),
            'holloween_theme_4' => __('Halloween Theme4'),
            'thanking_theme_1' => __('Thanks Giving Theme1'),
            'new_year_2020_1' => __('Happy New Year Theme1'),
            'new_year_2020_2' => __('Happy New Year Theme2'),
            'new_year_2020_3' => __('Happy New Year Theme3'),
            'new_year_2020_4' => __('Happy New Year Theme4'),
            'new_year_2020_5' => __('Happy New Year Theme5'),
            'new_year_2020_6' => __('Happy New Year Theme6'),
            'new_year_2020_7' => __('Happy New Year Theme7'),
        );
    } 
    
    public function getWheelDesign()
    {
        return array(
            '1' => __('Wheel Design 1'),
            '2' => __('Wheel Design 2'),
        );
    }
}
