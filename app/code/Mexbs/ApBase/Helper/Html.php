<?php
namespace Mexbs\ApBase\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Html extends AbstractHelper{

    protected $escaper;

    public function __construct(
        \Mexbs\ApBase\Escaper $escaper
    ){
        $this->escaper = $escaper;
    }

    public function getEscaper(){
        return $this->escaper;
    }
}